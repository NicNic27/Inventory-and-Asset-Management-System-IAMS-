<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\IcsRequest;
use App\Models\PurchaseOrderItem;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class IcsAutoIssueService
{
    /**
     * Auto-generate the ICS (Low/High-Valued) or PAR record for an asset that was
     * delivered as a "direct issuance" PO item, classified purely by unit value:
     * >= 50,000 -> PAR, >= 5,000 -> ICS High-Valued, otherwise ICS Low-Valued.
     */
    public function issueAsset(Asset $asset, PurchaseOrderItem $poItem): IcsRequest
    {
        return DB::transaction(function () use ($asset, $poItem): IcsRequest {
            $unitValue = (float) $asset->unit_value;
            $category = $unitValue >= 50000 ? 'PPE' : ($unitValue >= 5000 ? 'High - Valued' : 'Low - Valued');

            $generatedNo = $this->nextIcsNumber($category);

            $po = $poItem->purchaseOrder;

            $ics = IcsRequest::create([
                'ics_no' => $generatedNo,
                'category' => $category,
                'po_type' => $po->po_type ?? 'Asset',
                'po_no' => $po->po_no ?? null,
                'po_date' => $po->po_date ?? null,
                'status' => 'Pending',
                'items_json' => [[
                    'qty' => 1,
                    'unit' => $asset->unit_measure ?? 'Unit',
                    'article' => $asset->article,
                    'desc' => $asset->description,
                    'specs' => $asset->model,
                    'inv_no' => $asset->barcode_id,
                    'est_life' => null,
                    'unit_cost' => $unitValue,
                    'total_cost' => $unitValue,
                    'transfer_status' => 'Active',
                ]],
            ]);

            $asset->update(['status' => 'Issued']);

            return $ics;
        });
    }

    private function nextIcsNumber(string $category): string
    {
        $yearMonth = date('Y-m');

        if ($category === 'PPE') {
            $settingKey = 'seq_par_no';
            $prefix = 'PAR-';
        } elseif ($category === 'High - Valued') {
            $settingKey = 'seq_sphv_no';
            $prefix = 'SPHV-';
        } else {
            $settingKey = 'seq_splv_no';
            $prefix = 'SPLV-';
        }

        $seqSetting = SystemSetting::firstOrCreate(['key' => $settingKey], ['value' => '1']);
        $currentNumber = (int) $seqSetting->value;
        $generatedNo = $prefix . $yearMonth . '-' . str_pad($currentNumber, 4, '0', STR_PAD_LEFT);

        while (IcsRequest::where('ics_no', $generatedNo)->exists()) {
            $currentNumber++;
            $generatedNo = $prefix . $yearMonth . '-' . str_pad($currentNumber, 4, '0', STR_PAD_LEFT);
        }

        $seqSetting->update(['value' => $currentNumber + 1]);

        return $generatedNo;
    }
}
