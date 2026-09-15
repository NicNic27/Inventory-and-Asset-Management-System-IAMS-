<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supply;
use App\Models\SupplyBatch;
use App\Models\SupplySection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoDeliveryService
{
    public function __construct(private SupplyService $supplyService)
    {
    }

    /**
     * Receive a delivery against a Purchase Order (whole-PO sheet).
     *
     * $data expects:
     *  - po_id      (required) the Purchase Order being received
     *  - dr_number  (required) delivery receipt number covering this delivery
     *  - dr_date    (required) delivery date
     *  - remarks    (optional)
     *  - items[]    each: po_item_id, quantity (int, 0 = not delivered now)
     */
    public function receivePoDelivery(array $data): array
    {
        $po = PurchaseOrder::with('items')->findOrFail($data['po_id']);

        $received = collect($data['items'] ?? [])
            ->filter(fn ($row) => (int) ($row['quantity'] ?? 0) > 0);

        if ($received->isEmpty()) {
            throw new \DomainException('Enter at least one delivered quantity.');
        }

        $drNumber = trim((string) $data['dr_number']);
        $drDate   = $data['dr_date'];
        $supplier = trim((string) $po->supplier_name);

        return DB::transaction(function () use ($po, $received, $drNumber, $drDate, $supplier, $data): array {
            $receivedCount = 0;

            foreach ($received as $row) {
                /** @var PurchaseOrderItem|null $poItem */
                $poItem = $po->items->firstWhere('id', (int) $row['po_item_id']);

                if (!$poItem) {
                    throw new \DomainException('One of the selected items no longer exists on this P.O.');
                }

                // Asset lines are tracked per-unit via Asset Inventory, not via delivery receipts
                if (($poItem->item_type ?? 'supply') === 'asset') {
                    throw new \DomainException(
                        "Asset items are received through Asset Inventory — skipped \"{$poItem->description}\"."
                    );
                }

                // Resolve the destination Section + Classification for this line:
                // the receiving sheet's per-line choice wins, then whatever was
                // saved on the P.O. item in the wizard, then the existing link.
                $destSection = trim((string) ($row['dest_section'] ?? $poItem->dest_section ?? ''));
                $destClassification = trim((string) ($row['dest_classification'] ?? $poItem->dest_classification ?? ''));

                if ($destSection === '') {
                    throw new \DomainException(
                        "\"{$poItem->description}\" has no destination section. Pick a Section › Classification for it on the receiving sheet."
                    );
                }

                // Register the destination pair in the Manage Sections registry so
                // every dropdown (supplies, RIS, PO) offers it from now on.
                SupplySection::firstOrCreate(
                    ['name' => $destSection, 'classification' => $destClassification]
                );

                // Find or create the inventory item that lives under the chosen
                // Section › Classification. Matching ignores supplier — that is
                // metadata a PO introduces, not the item's identity.
                $supply = Supply::where('article', $destSection)
                    ->where('description', trim((string) $poItem->description))
                    ->where('unit_measure', trim((string) $poItem->unit))
                    ->when(
                        $destClassification !== '',
                        fn ($q) => $q->where('classification', $destClassification),
                        fn ($q) => $q->where(fn ($qq) => $qq->whereNull('classification')->orWhere('classification', ''))
                    )
                    ->first();

                if (!$supply) {
                    $supply = $this->supplyService->create([
                        'article'        => $destSection,
                        'description'    => trim((string) $poItem->description),
                        'unit_measure'   => trim((string) $poItem->unit),
                        'unit_value'     => (float) $poItem->unit_cost,
                        'supplier'       => $supplier ?: null,
                        'classification' => $destClassification,
                        'quantity'       => 0,
                        'status'         => 'Available',
                    ]);
                }

                $poItem->update([
                    'supply_id'           => $supply->id,
                    'dest_section'        => $destSection,
                    'dest_classification' => $destClassification,
                ]);
                $poItem->setRelation('supply', $supply);

                $quantity = (int) $row['quantity'];
                $delivered = (int) SupplyBatch::where('po_item_id', $poItem->id)->lockForUpdate()->sum('quantity');
                $remaining = (int) $poItem->qty - $delivered;

                if ($quantity > $remaining) {
                    throw new \DomainException(
                        "\"{$poItem->description}\": receiving {$quantity} exceeds the remaining " .
                        "{$remaining} of {$poItem->qty} ordered."
                    );
                }

                SupplyBatch::create([
                    'supply_id'   => $supply->id,
                    'po_item_id'  => $poItem->id,
                    'source_type' => 'procurement_stock',
                    'dr_number'   => $drNumber,
                    'dr_date'     => $drDate,
                    'quantity'    => $quantity,
                    'remaining_qty' => $quantity,
                    'unit_price'  => (float) $poItem->unit_cost,
                ]);

                // Post the goods into inventory immediately — even a partial delivery
                // is real, countable stock the moment it arrives.
                $this->supplyService->receiveSupply($supply, [
                    'quantity'         => $quantity,
                    'unit_price'       => (float) $poItem->unit_cost,
                    'supplier'         => $supplier,
                    'po_number'        => $po->po_no,
                    'delivery_receipt' => $drNumber,
                    'office'           => $po->place_of_delivery,
                    'receipt_status'   => $quantity >= (int) $poItem->qty ? 'Complete' : 'Partial',
                    'transaction_date' => $drDate,
                    'remarks'          => "Received from PO {$po->po_no}" . (!empty($data['remarks']) ? " — {$data['remarks']}" : ''),
                ]);

                $receivedCount++;
            }

            $poStatus = $po->recomputeStatus();

            ActivityLogProxy::log(
                "Received delivery {$drNumber} for PO {$po->po_no} ({$receivedCount} item(s), P.O. now {$poStatus})"
            );

            return [
                'received_count' => $receivedCount,
                'po_status'      => $poStatus,
            ];
        });
    }
}
