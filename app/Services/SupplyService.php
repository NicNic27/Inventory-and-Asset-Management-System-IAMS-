<?php

namespace App\Services;

use App\Models\Supply;
use App\Models\Transaction;
use App\Models\ActivityLog;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupplyService
{
    /**
     * Check if supply item already exists
     */
    public function checkDuplicate(array $data): ?Supply
    {
        $query = Supply::where('article', trim($data['article']))
            ->where('description', trim($data['description']))
            ->where('unit_measure', trim($data['unit_measure']))
            ->where('unit_value', $data['unit_value'] ?? 0);

        if (!empty($data['supplier'])) {
            $query->where('supplier', trim($data['supplier']));
        } else {
            $query->where(function($q) {
                $q->whereNull('supplier')->orWhere('supplier', '');
            });
        }

        // Brand/Model distinguish items that otherwise share the same article/description
        if (!empty($data['brand'])) {
            $query->where('brand', trim($data['brand']));
        } else {
            $query->where(function($q) {
                $q->whereNull('brand')->orWhere('brand', '');
            });
        }

        if (!empty($data['model'])) {
            $query->where('model', trim($data['model']));
        } else {
            $query->where(function($q) {
                $q->whereNull('model')->orWhere('model', '');
            });
        }

        if (!empty($data['classification'])) {
            $query->where('classification', trim($data['classification']));
        } else {
            $query->where(function($q) {
                $q->whereNull('classification')->orWhere('classification', '');
            });
        }

        return $query->first();
    }

    /**
     * Store image file for supply
     */
    protected function storeImage(Request $request): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('supplies', $filename, 'public');

        return $filename;
    }

    /**
     * Create a new supply item
     */
    public function create(array $data): Supply
    {
        $imageName = null;

        try {
            DB::beginTransaction();

            // Handle image upload if provided as file
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $data['image'];
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('supplies', $imageName, 'public');
                $data['image'] = $imageName;
            }

            $supply = Supply::create([
                'article' => $data['article'],
                'description' => $data['description'],
                'brand' => $data['brand'] ?? null,
                'model' => $data['model'] ?? null,
                'classification' => $data['classification'] ?? null,
                'unit_measure' => $data['unit_measure'],
                'unit_value' => $data['unit_value'] ?? 0,
                'supplier' => $data['supplier'] ?? null,
                'quantity' => $data['quantity'] ?? 0,
                'low_stock_threshold' => $data['low_stock_threshold'] ?? 10,
                'status' => $data['status'] ?? 'Active',
                'image' => $imageName
            ]);

            // Create opening transaction
            if (($data['quantity'] ?? 0) > 0) {
                Transaction::create([
                    'item_id' => $supply->id,
                    'item_type' => 'supplies',
                    'transaction_type' => 'IN',
                    'quantity' => $data['quantity'],
                    'supplier' => $data['supplier'] ?? null,
                    'unit_price' => $supply->unit_value,
                    'transaction_date' => date('Y-m-d'),
                    'remarks' => 'Opening Balance / Initial Stock',
                    'date_time' => now()
                ]);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created',
                'description' => "Added new supply: {$supply->article}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $supply;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($imageName && Storage::disk('public')->exists('supplies/' . $imageName)) {
                Storage::disk('public')->delete('supplies/' . $imageName);
            }

            throw $e;
        }
    }

    /**
     * Update an existing supply item
     */
    public function update(Supply $supply, array $data): Supply
    {
        $oldImageName = $supply->image;
        $imageName = $oldImageName;
        $newImageStored = false;

        try {
            DB::beginTransaction();

            // Handle image update
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $data['image'];
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('supplies', $imageName, 'public');
                $newImageStored = true;
                $data['image'] = $imageName;
            }

            $supply->update([
                'article' => $data['article'] ?? $supply->article,
                'description' => $data['description'] ?? $supply->description,
                'brand' => $data['brand'] ?? $supply->brand,
                'model' => $data['model'] ?? $supply->model,
                'classification' => $data['classification'] ?? $supply->classification,
                'unit_measure' => $data['unit_measure'] ?? $supply->unit_measure,
                'unit_value' => $data['unit_value'] ?? $supply->unit_value,
                'supplier' => $data['supplier'] ?? $supply->supplier,
                'low_stock_threshold' => $data['low_stock_threshold'] ?? $supply->low_stock_threshold,
                'status' => $data['status'] ?? $supply->status,
                'image' => $imageName
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated',
                'description' => "Updated supply: {$supply->article}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            if ($newImageStored && $oldImageName && Storage::disk('public')->exists('supplies/' . $oldImageName)) {
                Storage::disk('public')->delete('supplies/' . $oldImageName);
            }

            return $supply;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($newImageStored && $imageName && Storage::disk('public')->exists('supplies/' . $imageName)) {
                Storage::disk('public')->delete('supplies/' . $imageName);
            }

            throw $e;
        }
    }

    /**
     * Delete a supply item
     */
    public function delete(Supply $supply): bool
    {
        try {
            DB::beginTransaction();

            if ($supply->image && Storage::disk('public')->exists('supplies/' . $supply->image)) {
                Storage::disk('public')->delete('supplies/' . $supply->image);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Deleted',
                'description' => "Deleted supply: {$supply->article}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            $supply->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process stock transaction (IN/OUT)
     */
    public function processStockTransaction(Supply $supply, array $data): Transaction
    {
        $quantity = $data['quantity'] ?? null;
        $transactionType = strtoupper((string) ($data['type'] ?? 'IN'));

        if (filter_var($quantity, FILTER_VALIDATE_INT) === false || (int) $quantity < 1) {
            throw new \InvalidArgumentException('Transaction quantity must be a positive whole number.');
        }

        if (!in_array($transactionType, ['IN', 'OUT'], true)) {
            throw new \InvalidArgumentException('Transaction type must be either IN or OUT.');
        }

        $quantity = (int) $quantity;

        $unitPrice = $data['unit_price'] ?? null;
        $hasUnitPrice = $transactionType === 'IN' && is_numeric($unitPrice) && (float) $unitPrice >= 0;

        return DB::transaction(function () use ($supply, $data, $quantity, $transactionType, $unitPrice, $hasUnitPrice): Transaction {
            $lockedSupply = Supply::whereKey($supply->id)->lockForUpdate()->firstOrFail();

            if ($transactionType === 'OUT' && $lockedSupply->quantity < $quantity) {
                throw new \DomainException("Insufficient stock. Available: {$lockedSupply->quantity}");
            }

            $newQuantity = $transactionType === 'OUT'
                ? $lockedSupply->quantity - $quantity
                : $lockedSupply->quantity + $quantity;

            $updateData = ['quantity' => $newQuantity];

            // Weighted-average unit value: Price = (Σ quantity * price) / (Σ quantity)
            if ($hasUnitPrice) {
                $existingQuantity = max(0, (int) $lockedSupply->quantity);
                $existingValue = $existingQuantity * (float) $lockedSupply->unit_value;
                $newUnitValue = ($existingValue + ($quantity * (float) $unitPrice)) / ($existingQuantity + $quantity);
                $updateData['unit_value'] = round($newUnitValue, 2);
            }

            $lockedSupply->update($updateData);

            $transaction = Transaction::create([
                'item_id' => $lockedSupply->id,
                'item_type' => 'supplies',
                'transaction_type' => $transactionType,
                'quantity' => $quantity,
                'supplier' => $data['supplier'] ?? $lockedSupply->supplier,
                'unit_price' => $hasUnitPrice ? (float) $unitPrice : null,
                'transaction_date' => $data['transaction_date'] ?? date('Y-m-d'),
                'remarks' => $data['remarks'] ?? '',
                'date_time' => now()
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Transaction',
                'description' => "Stock {$transactionType}: {$lockedSupply->article} ({$quantity} units)",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return $transaction;
        });
    }

    /**
     * Receive a supply delivery and update its moving weighted-average cost.
     */
    public function receiveSupply(Supply $supply, array $data): Transaction
    {
        $quantity = $data['quantity'] ?? null;
        $unitPrice = $data['unit_price'] ?? null;

        if (filter_var($quantity, FILTER_VALIDATE_INT) === false || (int) $quantity < 1) {
            throw new \InvalidArgumentException('Received quantity must be a positive whole number.');
        }

        if (!is_numeric($unitPrice) || (float) $unitPrice < 0) {
            throw new \InvalidArgumentException('Received unit price must be zero or greater.');
        }

        $quantity = (int) $quantity;
        $unitPrice = (float) $unitPrice;

        return DB::transaction(function () use ($supply, $data, $quantity, $unitPrice): Transaction {
            $lockedSupply = Supply::whereKey($supply->id)->lockForUpdate()->firstOrFail();
            $existingQuantity = max(0, (int) $lockedSupply->quantity);
            $existingValue = $existingQuantity * (float) $lockedSupply->unit_value;
            $newUnitValue = ($existingValue + ($quantity * $unitPrice)) / ($existingQuantity + $quantity);

            $lockedSupply->update([
                'quantity' => $existingQuantity + $quantity,
                'unit_value' => round($newUnitValue, 2),
                'supplier' => $data['supplier'] ?? $lockedSupply->supplier,
                'status' => 'Available',
            ]);

            $transaction = Transaction::create([
                'item_id' => $lockedSupply->id,
                'item_type' => 'supplies',
                'transaction_type' => 'IN',
                'quantity' => $quantity,
                'supplier' => $data['supplier'] ?? $lockedSupply->supplier,
                'po_number' => $data['po_number'] ?? null,
                'delivery_receipt' => $data['delivery_receipt'] ?? null,
                'office' => $data['office'] ?? null,
                'unit_price' => $unitPrice,
                'receipt_status' => $data['receipt_status'] ?? 'Complete',
                'transaction_date' => $data['transaction_date'] ?? date('Y-m-d'),
                'remarks' => $data['remarks'] ?? 'Supply delivery received',
                'date_time' => now(),
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Received',
                'description' => "Received supply: {$lockedSupply->article} ({$quantity} units)",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $transaction;
        });
    }

    /**
     * Import a delivered supply PO item into its matching stock card once.
     */
    public function syncDeliveredPurchaseOrderItem(PurchaseOrderItem $item): ?Transaction
    {
        if (!$item->is_delivered || $item->inventory_synced) {
            return null;
        }

        $purchaseOrder = $item->purchaseOrder;
        if (!$purchaseOrder || $purchaseOrder->po_type === 'Asset') {
            return null;
        }

        $supplier = trim((string) $purchaseOrder->supplier_name);
        $description = trim((string) $item->description);
        $unit = trim((string) $item->unit);

        $supply = Supply::where('description', $description)
            ->where('unit_measure', $unit)
            ->first();

        $transaction = $supply
            ? $this->receiveSupply($supply, [
                'quantity' => $item->qty,
                'unit_price' => $item->unit_cost,
                'supplier' => $supplier,
                'po_number' => $purchaseOrder->po_no,
                'office' => $purchaseOrder->place_of_delivery,
                'receipt_status' => 'Complete',
                'remarks' => "Received from PO {$purchaseOrder->po_no}",
            ])
            : $this->create([
                'article' => $description,
                'description' => $description,
                'unit_measure' => $unit,
                'unit_value' => $item->unit_cost,
                'supplier' => $supplier ?: null,
                'quantity' => $item->qty,
                'status' => 'Available',
            ]);

        if (!$supply) {
            $transaction = Transaction::where('item_id', $transaction->id)
                ->where('item_type', 'supplies')
                ->where('transaction_type', 'IN')
                ->latest('id')
                ->first();

            if ($transaction) {
                $transaction->update([
                    'po_number' => $purchaseOrder->po_no,
                    'office' => $purchaseOrder->place_of_delivery,
                    'receipt_status' => 'Complete',
                    'remarks' => "Received from PO {$purchaseOrder->po_no}",
                ]);
            }
        }

        $item->forceFill(['inventory_synced' => true])->save();

        return $transaction instanceof Transaction ? $transaction : null;
    }

    /**
     * Merged stock card for one classification group: all supplies sharing the
     * same Section (article) + Classification, interleaved chronologically into
     * a single running-balance ledger.
     *
     * @return array{supplies: Collection, rows: Collection, total_quantity: int, unit_measure: string, unit_value: float|null}
     */
    public function classificationStockCardData(string $section, string $classification): array
    {
        $section = trim($section);
        $classification = trim($classification);

        $supplies = Supply::where('article', $section)
            ->when(
                $classification !== '',
                fn ($q) => $q->where('classification', $classification),
                fn ($q) => $q->where(fn ($qq) => $qq->whereNull('classification')->orWhere('classification', ''))
            )
            ->orderBy('description')
            ->get();

        $supplyIds = $supplies->pluck('id');

        $transactions = Transaction::whereIn('item_id', $supplyIds)
            ->where('item_type', 'supplies')
            // Process order (insertion sequence): every new transaction — add,
            // PO receipt, RIS deduction — always lands on the next row, never
            // above earlier ones even when a document carries a backdated or
            // future transaction_date.
            ->orderBy('id')
            ->get();

        $balance = 0;
        $runningQty = 0;
        $runningValue = 0.0;
        $avgUnitValue = (float) ($supplies->avg('unit_value') ?? 0);
        $descriptionById = $supplies->pluck('description', 'id');
        $rows = $transactions->map(function (Transaction $transaction) use (&$balance, &$runningQty, &$runningValue, $avgUnitValue, $descriptionById) {
            $type = strtoupper((string) $transaction->transaction_type);
            $isReceipt = in_array($type, ['IN', 'ADDED', 'RETURNED'], true);
            $balance += $isReceipt ? (int) $transaction->quantity : -(int) $transaction->quantity;

            // Weighted-average unit value as of this process, so each row —
            // the most recent one especially — shows the value synced to it.
            if ($isReceipt) {
                $price = $transaction->unit_price !== null
                    ? (float) $transaction->unit_price
                    : $avgUnitValue; // legacy rows recorded without a price
                $runningValue += (int) $transaction->quantity * $price;
                $runningQty += (int) $transaction->quantity;
            } else {
                $avg = $runningQty > 0 ? $runningValue / $runningQty : $avgUnitValue;
                $runningValue -= (int) $transaction->quantity * $avg;
                $runningQty = max(0, $runningQty - (int) $transaction->quantity);
            }
            $unitValueNow = $runningQty > 0 ? round($runningValue / $runningQty, 2) : null;

            return [
                'date' => $transaction->transaction_date ?: ($transaction->date_time ? $transaction->date_time->toDateString() : null),
                'reference' => $transaction->po_number,
                'receipt_quantity' => $isReceipt ? (int) $transaction->quantity : null,
                'issue_quantity' => $isReceipt ? null : (int) $transaction->quantity,
                // Receipt rows show the supplier the stock came from (a PO);
                // issuance rows show the requesting office from the RIS.
                'office' => $isReceipt
                    ? ($transaction->supplier ?: $transaction->office)
                    : ($transaction->office ?: 'Issuance'),
                'balance' => $balance,
                'days_to_consume' => null,
                'unit_price' => $transaction->unit_price,
                'unit_value_at_process' => $unitValueNow,
                'remarks' => $transaction->remarks,
                'description' => $descriptionById->get($transaction->item_id),
            ];
        });

        // Align the running balance with actual on-hand stock across the group.
        if ($rows->isNotEmpty()) {
            $totalQuantity = (int) $supplies->sum('quantity');
            $offset = $totalQuantity - (int) $balance;
            if ($offset !== 0) {
                $rows = $rows->map(function (array $row) use ($offset) {
                    $row['balance'] += $offset;
                    return $row;
                });
            }
        }

        return [
            'supplies' => $supplies,
            'rows' => $rows,
            'total_quantity' => (int) $supplies->sum('quantity'),
            'unit_measure' => $supplies->first()->unit_measure ?? '',
            'unit_value' => $supplies->avg('unit_value'),
        ];
    }

    /**
     * Get current stock information for a supply
     */
    public function getStockInfo(Supply $supply): array
    {
        $totalInput = Transaction::where('item_id', $supply->id)
            ->where('item_type', 'supplies')
            ->whereIn('transaction_type', ['IN', 'Added'])
            ->sum('quantity');

        return [
            'current_quantity' => $supply->quantity,
            'total_input' => $totalInput,
            'total_output' => $totalInput - $supply->quantity,
            'low_stock_threshold' => $supply->low_stock_threshold,
            'is_low_stock' => $supply->quantity <= $supply->low_stock_threshold,
            'is_out_of_stock' => $supply->quantity <= 0
        ];
    }
}
