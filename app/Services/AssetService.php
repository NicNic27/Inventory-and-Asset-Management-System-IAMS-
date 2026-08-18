<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Transaction;
use App\Models\ActivityLog;
use App\Models\IcsRequest;
use App\Models\AssetCustody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AssetService
{
    /**
     * Check if asset barcode already exists
     */
    public function barcodeDuplicate(string $barcode): ?Asset
    {
        return Asset::where('barcode_id', trim($barcode))->first();
    }

    /**
     * Store image file and return filename
     */
    protected function storeImage(Request $request, ?string $article = null): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $filename = time() . '_' . Str::slug($article ?? 'asset') . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/assets', $filename);

        return $filename;
    }

    /**
     * Create a new asset with transaction and activity log
     */
    public function create(array $data): Asset
    {
        $imageName = null;
        
        try {
            DB::beginTransaction();

            $acquisitionDate = $data['acquisition_date'];
            $value = (float) $data['unit_value'];
            $prefix = $value >= 5000 ? 'HV' : 'LV';
            $datePart = date('Y-m-d', strtotime($acquisitionDate));
            $sequence = Asset::whereDate('acquisition_date', $acquisitionDate)
                ->lockForUpdate()
                ->count() + 1;
            $propertyNumber = sprintf('%s-%s-%04d', $prefix, $datePart, $sequence);

            while (Asset::where('barcode_id', $propertyNumber)->exists()) {
                $sequence++;
                $propertyNumber = sprintf('%s-%s-%04d', $prefix, $datePart, $sequence);
            }

            // Handle image upload if provided as file
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imageName = $this->storeImageFile($data['image'], $data['article'] ?? 'asset');
                $data['image'] = $imageName;
            }

            $asset = Asset::create([
                'inventory_date' => now()->toDateString(),
                'item_code' => $propertyNumber,
                'barcode_id' => $propertyNumber,
                'name' => $data['article'],
                'article' => $data['article'],
                'category' => $data['category'] ?? 'Assets',
                'description' => $data['description'],
                'model' => $data['model'] ?? null,
                'serial_number' => $data['serial_number'],
                'acquisition_date' => $acquisitionDate,
                'unit_measure' => $data['unit_measure'],
                'person_accountable' => $data['person_accountable'] ?? null,
                'validation_signatory' => $data['validation_signatory'] ?? null,
                'supplier' => $data['supplier'] ?? null,
                'unit_value' => $data['unit_value'] ?? 0,
                'status' => $data['status'] ?? 'Serviceable',
                'image' => $imageName
            ]);

            // Create opening transaction
            Transaction::create([
                'item_id' => $asset->id,
                'item_type' => 'assets',
                'transaction_type' => 'ADDED',
                'quantity' => 1,
                'supplier' => $data['supplier'] ?? null,
                'transaction_date' => date('Y-m-d'),
                'remarks' => 'Opening Balance / New Item',
                'date_time' => now()
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created',
                'description' => "Added new asset: {$asset->article}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $asset;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($imageName && Storage::exists('public/assets/' . $imageName)) {
                Storage::delete('public/assets/' . $imageName);
            }

            throw $e;
        }
    }

    /**
     * Update an existing asset
     */
    public function update(Asset $asset, array $data): Asset
    {
        $imageName = $asset->image;

        try {
            DB::beginTransaction();

            // Handle image update
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($imageName && Storage::exists('public/assets/' . $imageName)) {
                    Storage::delete('public/assets/' . $imageName);
                }
                
                $imageName = $this->storeImageFile($data['image'], $data['article'] ?? $asset->article);
                $data['image'] = $imageName;
            }

            $asset->update([
                'article' => $data['article'] ?? $asset->article,
                'category' => $data['category'] ?? $asset->category,
                'description' => $data['description'] ?? $asset->description,
                'model' => $data['model'] ?? $asset->model,
                'serial_number' => $data['serial_number'] ?? $asset->serial_number,
                'acquisition_date' => $data['acquisition_date'] ?? $asset->acquisition_date,
                'unit_measure' => $data['unit_measure'] ?? $asset->unit_measure,
                'person_accountable' => $data['person_accountable'] ?? $asset->person_accountable,
                'validation_signatory' => $data['validation_signatory'] ?? $asset->validation_signatory,
                'supplier' => $data['supplier'] ?? $asset->supplier,
                'unit_value' => $data['unit_value'] ?? $asset->unit_value,
                'status' => $data['status'] ?? $asset->status,
                'image' => $imageName
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated',
                'description' => "Updated asset: {$asset->article}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $asset;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete an asset
     */
    public function delete(Asset $asset): bool
    {
        try {
            DB::beginTransaction();

            if ($asset->image && Storage::exists('public/assets/' . $asset->image)) {
                Storage::delete('public/assets/' . $asset->image);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Deleted',
                'description' => "Deleted asset: {$asset->article}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            $asset->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get assignment information for an asset
     */
    public function getAssignmentInfo(Asset $asset): ?array
    {
        $activeCustody = AssetCustody::where('asset_id', $asset->id)
            ->whereNull('returned_at')
            ->latest('issued_at')
            ->first();

        if ($activeCustody) {
            return [
                'assigned_to' => $activeCustody->holder_name,
                'status' => $activeCustody->transaction_type,
                'request' => null,
            ];
        }

        if (AssetCustody::where('asset_id', $asset->id)->exists()) {
            return null;
        }

        $latestReq = IcsRequest::where('items_json', 'LIKE', '%"inv_no":"' . $asset->barcode_id . '"%')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestReq) {
            $items = is_string($latestReq->items_json) ? json_decode($latestReq->items_json, true) : $latestReq->items_json;
            $asset_item = collect($items)->firstWhere('inv_no', $asset->barcode_id);
            
            return [
                'assigned_to' => ($asset_item['transfer_status'] ?? 'Active') === 'Active' ? $latestReq->sig_received_by_name : null,
                'status' => $asset_item['transfer_status'] ?? 'Active',
                'request' => $latestReq
            ];
        }

        return null;
    }

    /**
     * Helper to store image file
     */
    private function storeImageFile($file, string $prefix): string
    {
        $filename = time() . '_' . Str::slug($prefix) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/assets', $filename);

        return $filename;
    }
}
