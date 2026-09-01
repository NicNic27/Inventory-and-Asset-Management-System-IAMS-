<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecordPoDeliveryRequest;
use App\Models\PurchaseOrderItem;
use App\Models\SupplyBatch;
use App\Services\PoDeliveryService;

class PoDeliveryController extends Controller
{
    public function store(RecordPoDeliveryRequest $request, PoDeliveryService $service)
    {
        try {
            $batch = $service->recordDelivery($request->validated());
        } catch (\InvalidArgumentException|\DomainException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $poItem = PurchaseOrderItem::findOrFail($batch->po_item_id);
        $poStatus = $poItem->purchaseOrder->recomputeStatus();

        return response()->json([
            'success' => true,
            'message' => 'Delivery recorded successfully.',
            'delivered_quantity' => $poItem->getDeliveredQuantity(),
            'ordered_quantity' => (int) $poItem->qty,
            'delivery_status' => $poItem->getDeliveryStatus(),
            'po_status' => $poStatus,
        ]);
    }

    public function history($poItemId)
    {
        $poItem = PurchaseOrderItem::findOrFail($poItemId);

        $batches = SupplyBatch::where('po_item_id', $poItemId)
            ->orderByDesc('dr_date')
            ->orderByDesc('id')
            ->get(['id', 'dr_number', 'dr_date', 'quantity', 'unit_price', 'source_type', 'requesting_office']);

        return response()->json([
            'delivered_quantity' => $poItem->getDeliveredQuantity(),
            'ordered_quantity' => (int) $poItem->qty,
            'delivery_status' => $poItem->getDeliveryStatus(),
            'batches' => $batches,
        ]);
    }
}
