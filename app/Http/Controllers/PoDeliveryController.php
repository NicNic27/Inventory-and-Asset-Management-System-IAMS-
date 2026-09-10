<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceivePoDeliveryRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SupplyBatch;
use App\Services\PoDeliveryService;
use Illuminate\Http\JsonResponse;

class PoDeliveryController extends Controller
{
    /**
     * Data for the whole-PO receiving sheet: every line with ordered /
     * already received / remaining, plus each line's delivery history.
     */
    public function show($poId): JsonResponse
    {
        $po = PurchaseOrder::with(['items' => function ($q) {
            $q->withDeliveredQuantity();
        }])->findOrFail($poId);

        $items = $po->items->map(function (PurchaseOrderItem $item) {
            $ordered   = (int) $item->qty;
            $delivered = (int) $item->delivered_quantity;

            return [
                'po_item_id' => $item->id,
                'description' => $item->description,
                'unit'       => $item->unit,
                'ordered'    => $ordered,
                'received'   => $delivered,
                'remaining'  => max(0, $ordered - $delivered),
                'is_asset'   => ($item->item_type ?? 'supply') === 'asset',
                'complete'   => $ordered > 0 && $delivered >= $ordered,
                'history'    => SupplyBatch::where('po_item_id', $item->id)
                    ->orderByDesc('id')
                    ->get(['dr_number', 'dr_date', 'quantity'])
                    ->map(fn ($b) => [
                        'dr_number' => $b->dr_number,
                        'dr_date'   => $b->dr_date?->format('M d, Y'),
                        'quantity'  => (int) $b->quantity,
                    ])
                    ->values()
                    ->all(),
            ];
        });

        return response()->json([
            'id'             => $po->id,
            'po_no'          => $po->po_no,
            'supplier_name'  => $po->supplier_name,
            'status'         => $po->status,
            'items'          => $items,
        ]);
    }

    public function store(ReceivePoDeliveryRequest $request, PoDeliveryService $service): JsonResponse
    {
        try {
            $result = $service->receivePoDelivery($request->validated());
        } catch (\DomainException|\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'success'   => true,
            'message'   => "Delivery received. P.O. status is now {$result['po_status']}.",
            'po_status' => $result['po_status'],
        ]);
    }
}
