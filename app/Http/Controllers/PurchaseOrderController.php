<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ActivityLog;
use App\Models\Supply;
use App\Models\PoItemReferral;
use App\Models\PrReferral;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use App\Services\SupplyService;
use App\Services\PoDeliveryService;
use App\Models\RisRequest;
use App\Models\RisItem;
use App\Models\SystemSetting;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('po_no', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_filter') && $request->status_filter !== 'All') {
            $query->where('status', $request->status_filter);
        }

        $sort = $request->input('sort', 'date_desc');
        if ($sort === 'supplier_asc') {
            $query->orderBy('supplier_name', 'asc');
        } elseif ($sort === 'supplier_desc') {
            $query->orderBy('supplier_name', 'desc');
        } elseif ($sort === 'date_asc') {
            $query->orderBy('po_date', 'asc'); 
        } else {
            $query->orderBy('po_date', 'desc');
        }

        $purchaseOrders = $query->get();
        $supplies = Supply::orderBy('article')->get(['id', 'article', 'description', 'unit_measure']);

        return view('po.index', compact('purchaseOrders', 'supplies'));
    }

    public function store(Request $request, SupplyService $supplyService, PoDeliveryService $poDeliveryService)
    {
        if (!Schema::hasColumn('purchase_orders', 'po_type')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->string('po_type')->nullable()->default('Supply')->after('id');
            });
        }

        try {
            DB::beginTransaction();

            $totalItems = count($request->items ?? []);
            $deliveredItems = 0;
            foreach ($request->items ?? [] as $item) {
                if (!empty($item['is_delivered']) && ($item['is_delivered'] === true || $item['is_delivered'] === 'true')) {
                    $deliveredItems++;
                }
            }

            $calculatedStatus = 'Pending';
            if ($totalItems > 0) {
                if ($deliveredItems == 0) $calculatedStatus = 'Pending';
                elseif ($deliveredItems == $totalItems) $calculatedStatus = 'Complete';
                else $calculatedStatus = 'Partial';
            }

            $po = PurchaseOrder::create([
                'po_type' => $request->po_type,
                'entity_name' => $request->entity_name,
                'po_no' => $request->po_no,
                'supplier_name' => $request->supplier_name,
                'supplier_address' => $request->supplier_address,
                'po_date' => $request->po_date,
                'procurement_mode' => $request->procurement_mode,
                'auth_official' => $request->auth_official,
                'auth_official_designation' => $request->auth_official_designation,
                'chief_accountant' => $request->chief_accountant,
                'chief_accountant_designation' => $request->chief_accountant_designation,
                'place_of_delivery' => $request->place_of_delivery,
                'date_of_delivery' => $request->date_of_delivery,
                'delivery_term' => $request->delivery_term,
                'payment_term' => $request->payment_term,
                'total_amount' => $request->total_amount,
                'status' => $calculatedStatus,
            ]);

            foreach ($request->items as $item) {
                $poItem = PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'unit' => $item['unit'],
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'unit_cost' => $item['cost'],
                    'amount' => $item['qty'] * $item['cost'],
                    'is_delivered' => (!empty($item['is_delivered']) && ($item['is_delivered'] === true || $item['is_delivered'] === 'true')),
                    'item_type' => $item['item_type'] ?? 'supply',
                    'supply_id' => $item['supply_id'] ?? null,
                    'source_type' => $item['source_type'] ?? 'procurement_stock',
                    'requesting_office' => $item['requesting_office'] ?? null,
                ]);

                $supplyService->syncDeliveredPurchaseOrderItem($poItem);
            }

            $this->autoCreateRisForDirectIssuance($po);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created',
                'description' => "Created Purchase Order: {$po->po_no}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Purchase Order Saved!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $po = PurchaseOrder::with(['items' => function ($q) {
            $q->withDeliveredQuantity();
        }])->findOrFail($id);

        $po->items->each(function (PurchaseOrderItem $item) {
            $item->delivery_status = $item->getDeliveryStatus();

            $linkedReferral = $item->referrals()->with('risRequest')->first();
            $item->pr_referral_id = $linkedReferral->id ?? null;
            $item->requesting_ris_no = $linkedReferral->risRequest->ris_no ?? null;
        });

        return response()->json($po);
    }

    public function update(Request $request, $id, SupplyService $supplyService, PoDeliveryService $poDeliveryService)
    {
        if (!Schema::hasColumn('purchase_orders', 'po_type')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->string('po_type')->nullable()->default('Supply')->after('id');
            });
        }

        try {
            DB::beginTransaction();

            $totalItems = count($request->items ?? []);
            $deliveredItems = 0;
            foreach ($request->items ?? [] as $item) {
                if (!empty($item['is_delivered']) && ($item['is_delivered'] === true || $item['is_delivered'] === 'true')) {
                    $deliveredItems++;
                }
            }

            $calculatedStatus = 'Pending';
            if ($totalItems > 0) {
                if ($deliveredItems == 0) $calculatedStatus = 'Pending';
                elseif ($deliveredItems == $totalItems) $calculatedStatus = 'Complete';
                else $calculatedStatus = 'Partial';
            }

            $po = PurchaseOrder::findOrFail($id);
            $po->update([
                'po_type' => $request->po_type,
                'entity_name' => $request->entity_name,
                'po_no' => $request->po_no,
                'supplier_name' => $request->supplier_name,
                'supplier_address' => $request->supplier_address,
                'po_date' => $request->po_date,
                'procurement_mode' => $request->procurement_mode,
                'auth_official' => $request->auth_official,
                'auth_official_designation' => $request->auth_official_designation,
                'chief_accountant' => $request->chief_accountant,
                'chief_accountant_designation' => $request->chief_accountant_designation,
                'place_of_delivery' => $request->place_of_delivery,
                'date_of_delivery' => $request->date_of_delivery,
                'delivery_term' => $request->delivery_term,
                'payment_term' => $request->payment_term,
                'total_amount' => $request->total_amount,
                'status' => $calculatedStatus,
            ]);

            $keptItemIds = [];

            foreach ($request->items as $item) {
                $payload = [
                    'unit' => $item['unit'],
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'unit_cost' => $item['cost'],
                    'amount' => $item['qty'] * $item['cost'],
                    'is_delivered' => (!empty($item['is_delivered']) && ($item['is_delivered'] === true || $item['is_delivered'] === 'true')),
                    'item_type' => $item['item_type'] ?? 'supply',
                    'supply_id' => $item['supply_id'] ?? null,
                    'source_type' => $item['source_type'] ?? 'procurement_stock',
                    'requesting_office' => $item['requesting_office'] ?? null,
                ];

                // Update existing items in place so recorded delivery batches stay linked
                if (!empty($item['id'])) {
                    $poItem = $po->items()->whereKey($item['id'])->firstOrFail();
                    $poItem->update($payload);
                } else {
                    $payload['purchase_order_id'] = $po->id;
                    $poItem = PurchaseOrderItem::create($payload);
                }

                $keptItemIds[] = $poItem->id;

                $supplyService->syncDeliveredPurchaseOrderItem($poItem);
            }

            $po->items()->whereNotIn('id', $keptItemIds)->delete();

            // Re-create RIS for direct issuance items (clear old auto-generated ones first)
            $autoRisNos = RisRequest::where('purpose', 'like', "%from PO {$po->po_no}%")
                ->where('status', 'Pending Staff Review')
                ->pluck('id');
            RisItem::whereIn('ris_id', $autoRisNos)->delete();
            RisRequest::whereIn('id', $autoRisNos)->delete();
            $this->autoCreateRisForDirectIssuance($po);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated',
                'description' => "Updated Purchase Order: {$po->po_no}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Purchase Order Updated!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Auto-create a RIS for all direct-issuance items in a PO.
     * One RIS per unique requesting office, with all direct-issuance items grouped under it.
     * Two default signatories (Approved By, Issued By) are pre-filled per the RIS form convention.
     */
    private function autoCreateRisForDirectIssuance(PurchaseOrder $po): void
    {
        $directItems = $po->items()
            ->where('source_type', 'direct_issuance')
            ->get()
            ->groupBy('requesting_office');

        $user = Auth::user();

        foreach ($directItems as $office => $items) {
            if (empty($office)) continue;

            $risNo = $this->generateRisNumber();

            $risRequest = RisRequest::create([
                'user_id' => $user?->id,
                'ris_no' => $risNo,
                'entity_name' => $po->entity_name,
                'office' => $office,
                'purpose' => "Direct issuance from PO {$po->po_no}",
                'date_requested' => now()->toDateString(),
                'status' => 'Pending Staff Review',
                // Two default signatories pre-filled from the RIS form
                'sig_approved_by' => 'JEFFREY B. PAGATPAT',
                'desig_approved' => 'Admin, Officer V (Supply Officer)',
                'sig_issued_by' => 'ALDRIN RELLAMA',
                'desig_issued' => 'AA-VI (Storekeeper II)',
            ]);

            foreach ($items as $item) {
                RisItem::create([
                    'ris_id' => $risRequest->id,
                    'stock_no' => $item->supply->barcode_id ?? null,
                    'unit' => $item->unit,
                    'description' => $item->description,
                    'req_quantity' => $item->qty,
                    'stock_avail' => 'no',
                    'issue_quantity' => 0,
                    'remarks' => "Auto-generated from PO {$po->po_no}",
                ]);
            }
        }
    }

    /**
     * Generate a unique RIS number in format: RIS-YYYY-MM-NNNN.
     * Uses the SystemSetting counter (same as the user-facing RIS form) to stay in sync
     * and avoid collisions with manually-created RIS entries.
     */
    private function generateRisNumber(): string
    {
        $seqSetting = SystemSetting::firstOrCreate(
            ['key' => 'seq_ris_no'],
            ['value' => '1']
        );

        $yearMonth = now()->format('Y-m');
        $currentNumber = (int) $seqSetting->value;
        $sequenceFormatted = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
        $generatedRisNo = 'RIS-' . $yearMonth . '-' . $sequenceFormatted;

        // Safety: skip any already-used number (edge case if manual & auto run simultaneously)
        while (RisRequest::where('ris_no', $generatedRisNo)->exists()) {
            $currentNumber++;
            $sequenceFormatted = str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
            $generatedRisNo = 'RIS-' . $yearMonth . '-' . $sequenceFormatted;
        }

        $seqSetting->update(['value' => $currentNumber + 1]);

        return $generatedRisNo;
    }

    public function destroy($id)
    {
        $po = PurchaseOrder::findOrFail($id);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted',
            'description' => "Deleted Purchase Order: {$po->po_no}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $po->delete(); 
        return redirect()->back()->with('success', 'Purchase Order Deleted');
    }
}