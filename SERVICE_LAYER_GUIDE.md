# Service Layer Architecture Documentation

## Overview

The service layer has been implemented to extract business logic from controllers, promoting better separation of concerns and code reusability.

## Services Available

### 1. **AssetService** (`app/Services/AssetService.php`)

Handles all asset-related business logic.

#### Methods:

- **`barcodeDuplicate(string $barcode): ?Asset`**
  - Checks if a barcode already exists for an asset
  - Returns the asset if duplicate found, null otherwise

- **`create(array $data): Asset`**
  - Creates a new asset with transaction and activity logging
  - Data array accepts: `barcode_id`, `article`, `category`, `description`, `unit_measure`, `supplier`, `unit_value`, `status`, `image`
  - Automatically creates opening transaction and activity log
  - Handles image file upload and storage
  - Throws exception on failure (automatically rolled back)

- **`update(Asset $asset, array $data): Asset`**
  - Updates existing asset with same fields as create
  - Handles image replacement (deletes old image if new one provided)
  - Logs all changes in activity log
  - Throws exception on failure

- **`delete(Asset $asset): bool`**
  - Deletes asset and removes associated image file
  - Logs deletion in activity log
  - Throws exception on failure

- **`getAssignmentInfo(Asset $asset): ?array`**
  - Retrieves latest assignment information from ICS requests
  - Returns array with: `assigned_to`, `status`, `request`
  - Used to display who asset is assigned to

#### Usage Example:

```php
use App\Services\AssetService;

class MyController extends Controller
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    public function store(Request $request)
    {
        // Check for duplicate
        if ($this->assetService->barcodeDuplicate($request->barcode_id)) {
            return redirect()->back()->with('error', 'Barcode already exists');
        }

        // Create asset
        $asset = $this->assetService->create([
            'barcode_id' => $request->barcode_id,
            'article' => $request->article,
            'description' => $request->description,
            // ... other fields
        ]);

        return redirect()->back()->with('success', 'Asset created');
    }
}
```

---

### 2. **SupplyService** (`app/Services/SupplyService.php`)

Handles all supply/inventory-related business logic.

#### Methods:

- **`checkDuplicate(array $data): ?Supply`**
  - Checks for duplicate supply based on article, description, unit_measure, unit_value, and supplier
  - Returns the supply if duplicate found, null otherwise

- **`create(array $data): Supply`**
  - Creates new supply with auto-generated barcode (SUP-YYYYMMDD-XXXX format)
  - Data array accepts: `article`, `description`, `unit_measure`, `unit_value`, `supplier`, `quantity`, `low_stock_threshold`, `status`, `image`
  - Auto-increments sequence number in SystemSetting
  - Creates opening transaction if quantity provided
  - Handles image upload

- **`update(Supply $supply, array $data): Supply`**
  - Updates supply with provided fields
  - Handles image file management

- **`delete(Supply $supply): bool`**
  - Deletes supply and removes image
  - Logs deletion

- **`processStockTransaction(Supply $supply, array $data): Transaction`**
  - Processes IN or OUT transaction for supply
  - Data array: `quantity`, `type` (IN/OUT), `supplier`, `transaction_date`, `remarks`
  - Updates supply quantity automatically
  - Creates transaction record and activity log

- **`getStockInfo(Supply $supply): array`**
  - Returns comprehensive stock information
  - Returns: `current_quantity`, `total_input`, `total_output`, `low_stock_threshold`, `is_low_stock`, `is_out_of_stock`

#### Usage Example:

```php
use App\Services\SupplyService;

class SuppliesController extends Controller
{
    private SupplyService $supplyService;

    public function __construct(SupplyService $supplyService)
    {
        $this->supplyService = $supplyService;
    }

    public function processTransaction(Request $request, $id)
    {
        $supply = Supply::findOrFail($id);

        try {
            $transaction = $this->supplyService->processStockTransaction($supply, [
                'quantity' => $request->quantity,
                'type' => $request->transaction_type,
                'supplier' => $request->supplier,
                'remarks' => $request->remarks
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
```

---

### 3. **RisService** (`app/Services/RisService.php`)

Handles Requisition and Issue Slip (RIS) request processing.

#### Methods:

- **`getValidStatuses(): array`**
  - Static method returning all valid RIS statuses
  - Returns: `['Pending Staff Review', 'Forwarded to Admin', 'Approved', 'Declined', 'Rejected', 'Cancelled']`

- **`create(array $data): RisRequest`**
  - Creates new RIS request with auto-generated RIS number
  - Data array: `entity_name`, `division`, `office`, `fund_cluster`, `rcc`, `purpose`, `sig_requested_by`, `sig_requested_by_name`, `desig_requested`, `date_requested`
  - Auto-generates RIS number (RIS-YYYYMMDD-XXXXX format)

- **`update(RisRequest $risRequest, array $data): RisRequest`**
  - Updates RIS request with new data
  - Supports action-based status changes: `forward`, `return`, `approve`, `decline`, `reject`
  - Updates all signature and date fields

- **`approve(RisRequest $risRequest, array $data): RisRequest`**
  - Marks RIS as approved
  - Data array: `sig_approved_by`, `sig_approved_by_name`, `desig_approved`, `date_approved`

- **`decline(RisRequest $risRequest, string $reason = null): RisRequest`**
  - Marks RIS as declined with optional reason

- **`getItemsWithStock(RisRequest $risRequest): array`**
  - Gets all items in RIS with current stock information
  - Returns array of items with stock details and availability

#### Usage Example:

```php
use App\Services\RisService;

class RisController extends Controller
{
    private RisService $risService;

    public function __construct(RisService $risService)
    {
        $this->risService = $risService;
    }

    public function process(Request $request, $id)
    {
        $ris = RisRequest::findOrFail($id);

        $ris = $this->risService->update($ris, [
            'entity_name' => $request->entity_name,
            'action' => 'approve',
            'sig_approved_by' => $request->approved_by,
            'date_approved' => now()->toDateString()
        ]);

        return redirect()->back()->with('success', 'RIS approved');
    }
}
```

---

### 4. **QR Identity Service** (`app/Services/BarcodeService.php`)

Handles QR-oriented inventory identity lookup, rendering support, scanning, and retrieval. The class name and `barcode_id` database column are retained for compatibility with existing records and routes.

#### Methods:

- **`getAllBarcodes(string $search = '', string $category = 'all'): object`**
  - Gets all QR inventory identities (assets + supplies) with optional search and category filtering
  - Category: `all`, `supply`, or `asset`
  - Returns collection of barcodes with item type

- **`processScan(string $barcode, array $data): array`**
  - Processes a decoded QR value for a stock transaction
  - Data array: `transaction_type` (IN/OUT), `quantity`, `supplier`, `remarks`
  - Returns array with: `success`, `message`, `barcode`, `item_type`, `item_name`, `current_stock`
  - Validates stock before OUT transaction

- **`getRecentScans(int $limit = 10): object`**
  - Returns recent barcode scans

- **`generateBarcode(string $itemType, int $itemId): ?GeneratedBarcode`**
  - Generates a registry record for an item's QR identity
  - itemType: `supply` or `asset`

- **`barcodeExists(string $barcode): bool`**
  - Checks if the decoded QR value exists in the system

- **`findByBarcode(string $barcode): ?object`**
  - Finds an item by its decoded QR value
  - Returns object with: `type` (supply/asset), `item` (the model)

#### Usage Example:

```php
use App\Services\BarcodeService;

class BarcodeController extends Controller
{
    private BarcodeService $barcodeService;

    public function __construct(BarcodeService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    public function processScan(Request $request)
    {
        $result = $this->barcodeService->processScan($request->barcode, [
            'transaction_type' => $request->type,
            'quantity' => $request->quantity,
            'remarks' => $request->remarks
        ]);

        return response()->json($result);
    }
}
```

---

### 5. **TransactionService** (`app/Services/TransactionService.php`)

Handles complex transaction querying and reporting.

#### Methods:

- **`getAllTransactions(int $perPage = 15, array $filters = []): Paginator`**
  - Gets all transactions with complex grouping
  - Filters: `transaction_type`, `item_type`, `search`, `date_from`, `date_to`
  - Returns paginated results with grouped RIS transactions

- **`getTransactionById(int $id)`**
  - Retrieves single transaction with item details

- **`getItemTransactionHistory(int $itemId, string $itemType, int $limit = 50)`**
  - Gets transaction history for specific item
  - itemType: `assets` or `supplies`

- **`getRisItems(int $perPage = 20, string $search = '')`**
  - Gets all OUT transactions related to RIS
  - Useful for RIS reports

- **`getTransactionSummary(array $filters = []): array`**
  - Returns dashboard statistics
  - Returns: `total_transactions`, `total_in`, `total_out`, `total_by_type`

#### Usage Example:

```php
use App\Services\TransactionService;

class TransactionController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $transactions = $this->transactionService->getAllTransactions(
            $request->input('per_page', 15),
            [
                'transaction_type' => $request->input('type'),
                'search' => $request->input('search')
            ]
        );

        return view('transactions.index', compact('transactions'));
    }
}
```

---

## Service Registration

Services are automatically registered as singletons in `AppServiceProvider.php`. They are injected via constructor injection:

```php
class YourController extends Controller
{
    private AssetService $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }
}
```

---

## How to Refactor Existing Controllers

### Step 1: Add Service Injection to Constructor

```php
class YourController extends Controller
{
    private YourService $yourService;

    public function __construct(YourService $yourService)
    {
        $this->yourService = $yourService;
    }
}
```

### Step 2: Replace Business Logic with Service Calls

**Before:**
```php
try {
    DB::beginTransaction();
    // ... lots of business logic
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
}
```

**After:**
```php
try {
    $result = $this->yourService->createItem($data);
} catch (\Exception $e) {
    // Handle error
}
```

### Step 3: Keep View Logic in Controller

Controllers should handle:
- Request validation
- Service calling
- Response formatting (redirects, JSON, views)

Services handle:
- Business logic
- Database transactions
- Logging and auditing
- Error handling

---

## Benefits

1. **Testability**: Services can be easily unit tested in isolation
2. **Reusability**: Services can be used by multiple controllers, CLI commands, events, etc.
3. **Maintainability**: Business logic is centralized and easier to update
4. **Separation of Concerns**: Controllers focus on HTTP concerns only
5. **Consistency**: Standardized error handling and logging across operations
6. **Transaction Management**: Services handle database transactions consistently

---

## Next Steps

1. **Refactor Admin Controllers**: Apply the same pattern to:
   - `Admin/AssetController`
   - `Admin/SupplyController`
   - `Admin/RisController`

2. **Refactor User Controllers**: Apply pattern to:
   - `User/RisController`
   - `User/DashboardController`

3. **Create Additional Services** (as needed):
   - `NotificationService` for email/SMS notifications
   - `ReportService` for complex report generation
   - `UserService` for user management

4. **Add Service Contracts/Interfaces**: Create interfaces in `app/Services/Contracts/` for better abstraction and testing

5. **Add Comprehensive Tests**: Create feature tests for each service method

---

## File Locations

```
app/
├── Services/
│   ├── AssetService.php
│   ├── SupplyService.php
│   ├── RisService.php
│   ├── BarcodeService.php
│   └── TransactionService.php
├── Http/
│   └── Controllers/
│       ├── AssetController.php (refactored)
│       └── ... (more to refactor)
└── Providers/
    └── AppServiceProvider.php (updated)
```

---

## Error Handling

All services throw exceptions on errors. Controllers should catch and handle appropriately:

```php
try {
    $asset = $this->assetService->create($data);
} catch (\Illuminate\Database\QueryException $e) {
    // Database error
    return back()->with('error', 'Database error occurred');
} catch (\Exception $e) {
    // General error
    return back()->with('error', $e->getMessage());
}
```

---

Created: 2026-08-14
Last Updated: 2026-08-14
