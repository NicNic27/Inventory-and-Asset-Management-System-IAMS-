<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Transaction;
use App\Models\ActivityLog;
use App\Models\IcsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

            // Handle image upload if provided as file
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imageName = $this->storeImageFile($data['image'], $data['article'] ?? 'asset');
                $data['image'] = $imageName;
            }

            $asset = Asset::create([
                'item_code' => $data['barcode_id'],
                'barcode_id' => $data['barcode_id'],
                'name' => $data['article'],
                'article' => $data['article'],
                'category' => $data['category'] ?? 'Assets',
                'description' => $data['description'],
                'unit_measure' => $data['unit_measure'],
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
                'unit_measure' => $data['unit_measure'] ?? $asset->unit_measure,
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
        return time() . '_' . Str::slug($prefix) . '.' . $file->getClientOriginalExtension();
    }
}
