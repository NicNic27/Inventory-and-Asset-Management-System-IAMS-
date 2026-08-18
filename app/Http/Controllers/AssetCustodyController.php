<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\AssetCustody;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetCustodyController extends Controller
{
    public function scan(Request $request)
    {
        $data = $request->validate(['barcode_id' => ['required', 'string', 'max:255']]);
        $asset = Asset::where('barcode_id', trim($data['barcode_id']))->first();

        if (!$asset) {
            return response()->json(['message' => 'Asset not found. Check the property number and scan again.'], 404);
        }

        $activeCustody = AssetCustody::where('asset_id', $asset->id)
            ->whereNull('returned_at')
            ->latest('issued_at')
            ->first();

        return response()->json([
            'asset' => [
                'id' => $asset->id,
                'barcode_id' => $asset->barcode_id,
                'article' => $asset->article,
                'description' => $asset->description,
                'status' => $asset->status,
            ],
            'active_custody' => $activeCustody,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'transaction_type' => ['required', 'in:Borrowed,Transferred'],
            'holder_name' => ['required', 'string', 'max:255'],
            'holder_position' => ['nullable', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:255'],
            'issued_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'condition_on_issue' => ['required', 'in:Serviceable,Unserviceable'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        return DB::transaction(function () use ($data, $request) {
            $asset = Asset::lockForUpdate()->findOrFail($data['asset_id']);

            if ($asset->status !== 'Serviceable') {
                return response()->json(['message' => 'Only serviceable assets can be issued or transferred.'], 422);
            }

            if (AssetCustody::where('asset_id', $asset->id)->whereNull('returned_at')->exists()) {
                return response()->json(['message' => 'This asset is already out of inventory. Scan it to transfer or return it.'], 422);
            }

            $custody = AssetCustody::create($data + ['processed_by' => Auth::id()]);
            Transaction::create([
                'item_id' => $asset->id,
                'item_type' => 'assets',
                'transaction_type' => $data['transaction_type'] === 'Borrowed' ? 'ISSUED' : 'TRANSFERRED',
                'quantity' => 1,
                'transaction_date' => $data['issued_at'],
                'remarks' => $data['transaction_type'] . ' to ' . $data['holder_name'] . ' (' . $data['department'] . ')',
                'date_time' => now(),
            ]);
            $this->log($asset, strtolower($data['transaction_type']) . ' to ' . $data['holder_name']);

            return response()->json(['message' => 'Asset ' . strtolower($data['transaction_type']) . ' record saved.', 'custody' => $custody]);
        });
    }

    public function returnAsset(Request $request, AssetCustody $custody)
    {
        $data = $request->validate([
            'returned_at' => ['required', 'date'],
            'condition_on_return' => ['required', 'in:Serviceable,Unserviceable'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($custody->returned_at) {
            return response()->json(['message' => 'This custody record has already been returned.'], 422);
        }

        return DB::transaction(function () use ($custody, $data) {
            $asset = Asset::lockForUpdate()->findOrFail($custody->asset_id);
            $custody->update($data);
            $asset->update(['status' => $data['condition_on_return']]);
            Transaction::create([
                'item_id' => $asset->id,
                'item_type' => 'assets',
                'transaction_type' => 'RETURNED',
                'quantity' => 1,
                'transaction_date' => $data['returned_at'],
                'remarks' => 'Returned by ' . ($custody->holder_name ?: 'assigned holder'),
                'date_time' => now(),
            ]);
            $this->log($asset, 'returned by ' . ($custody->holder_name ?: 'assigned holder'));

            return response()->json(['message' => 'Asset returned to inventory.']);
        });
    }

    public function transfer(Request $request, AssetCustody $custody)
    {
        $data = $request->validate([
            'holder_name' => ['required', 'string', 'max:255'],
            'holder_position' => ['nullable', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:255'],
            'issued_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($custody->returned_at) {
            return response()->json(['message' => 'This custody record is already closed.'], 422);
        }

        return DB::transaction(function () use ($custody, $data) {
            $asset = Asset::lockForUpdate()->findOrFail($custody->asset_id);
            $custody->update([
                'returned_at' => $data['issued_at'],
                'condition_on_return' => 'Serviceable',
                'remarks' => trim(($custody->remarks ? $custody->remarks . "\n" : '') . 'Transferred to ' . $data['holder_name']),
            ]);
            $newCustody = AssetCustody::create($data + [
                'asset_id' => $asset->id,
                'transaction_type' => 'Transferred',
                'condition_on_issue' => 'Serviceable',
                'processed_by' => Auth::id(),
            ]);
            Transaction::create([
                'item_id' => $asset->id,
                'item_type' => 'assets',
                'transaction_type' => 'TRANSFERRED',
                'quantity' => 1,
                'transaction_date' => $data['issued_at'],
                'remarks' => 'Transferred from ' . ($custody->holder_name ?: 'previous holder') . ' to ' . $data['holder_name'],
                'date_time' => now(),
            ]);
            $this->log($asset, 'transferred from ' . ($custody->holder_name ?: 'previous holder') . ' to ' . $data['holder_name']);

            return response()->json(['message' => 'Asset transfer record saved.', 'custody' => $newCustody]);
        });
    }

    private function log(Asset $asset, string $event): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Asset custody',
            'description' => "Asset {$asset->barcode_id} {$event}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}