<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Supply;
use App\Models\Transaction;
use App\Models\GeneratedBarcode;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BarcodeService
{
    /**
     * Get registered asset barcodes with search and filtering.
     */
    public function getAllBarcodes(string $search = '', string $category = 'all'): object
    {
        $assets = collect();
        if ($category === 'all' || $category === 'asset') {
            $assetQuery = Asset::whereNotNull('barcode_id')
                ->select('id', 'barcode_id as barcode_code', 'article', 'description', 'supplier');

            if (!empty($search)) {
                $assetQuery->where(function($q) use ($search) {
                    $q->where('article', 'LIKE', "%{$search}%")
                      ->orWhere('barcode_id', 'LIKE', "%{$search}%");
                });
            }

            $assets = $assetQuery->get()->map(function ($item) {
                $item->item_type = 'asset';
                $item->generated_at = null;
                return $item;
            });
        }

        return $assets->sortByDesc('id')->values();
    }

    /**
     * Process barcode scan for stock transaction
     */
    public function processScan(string $barcode, array $data): array
    {
        $barcode = trim($barcode);
        $transactionType = strtoupper($data['transaction_type'] ?? 'IN');

        try {
            DB::beginTransaction();

            // Check if barcode exists
            $supply = Supply::where('barcode_id', $barcode)->first();
            $asset = Asset::where('barcode_id', $barcode)->first();

            if (!$supply && !$asset) {
                return [
                    'success' => false,
                    'message' => 'Barcode not found in the system.',
                    'barcode' => $barcode
                ];
            }

            if ($supply) {
                // Process supply scan
                $quantity = $data['quantity'] ?? 1;
                
                if ($transactionType === 'OUT' && $supply->quantity < $quantity) {
                    return [
                        'success' => false,
                        'message' => "Insufficient stock. Available: {$supply->quantity}",
                        'barcode' => $barcode,
                        'item_type' => 'supply'
                    ];
                }

                // Update quantity
                if ($transactionType === 'OUT') {
                    $supply->decrement('quantity', $quantity);
                } else {
                    $supply->increment('quantity', $quantity);
                }

                // Record transaction
                Transaction::create([
                    'item_id' => $supply->id,
                    'item_type' => 'supplies',
                    'transaction_type' => $transactionType,
                    'quantity' => $quantity,
                    'supplier' => $data['supplier'] ?? $supply->supplier,
                    'transaction_date' => date('Y-m-d'),
                    'remarks' => $data['remarks'] ?? 'Scanned Transaction',
                    'date_time' => now()
                ]);

                $response = [
                    'success' => true,
                    'message' => "{$transactionType} transaction recorded successfully.",
                    'barcode' => $barcode,
                    'item_type' => 'supply',
                    'item_name' => $supply->article,
                    'quantity' => $quantity,
                    'current_stock' => $supply->fresh()->quantity
                ];
            } else {
                // Process asset scan
                $response = [
                    'success' => true,
                    'message' => "Asset scanned: {$asset->article}",
                    'barcode' => $barcode,
                    'item_type' => 'asset',
                    'item_name' => $asset->article,
                    'status' => $asset->status
                ];
            }

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Scanned',
                'description' => "Scanned barcode: {$barcode} - {$transactionType}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $response;
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Error processing scan: ' . $e->getMessage(),
                'barcode' => $barcode
            ];
        }
    }

    /**
     * Get recent scans
     */
    public function getRecentScans(int $limit = 10): object
    {
        return Transaction::with(['supply', 'asset'])
            ->latest('date_time')
            ->limit($limit)
            ->get();
    }

    /**
     * Generate barcode for item
     */
    public function generateBarcode(string $itemType, int $itemId): ?GeneratedBarcode
    {
        try {
            DB::beginTransaction();

            if ($itemType !== 'asset') {
                throw new \InvalidArgumentException('Only assets can have generated barcodes.');
            }

            $item = Asset::findOrFail($itemId);

            if (!$item->barcode_id) {
                return null;
            }

            $barcode = GeneratedBarcode::firstOrCreate(
                ['item_type' => $itemType, 'item_id' => $itemId],
                [
                    'barcode_code' => $item->barcode_id,
                    'generated_at' => now()
                ]
            );

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Generated',
                'description' => "Generated barcode for {$itemType}: {$item->article}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $barcode;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if barcode exists
     */
    public function barcodeExists(string $barcode): bool
    {
        return Asset::where('barcode_id', trim($barcode))->exists();
    }

    /**
     * Find item by barcode
     */
    public function findByBarcode(string $barcode): ?object
    {
        $barcode = trim($barcode);

        $asset = Asset::where('barcode_id', $barcode)->first();
        if ($asset) {
            return (object)[
                'type' => 'asset',
                'item' => $asset
            ];
        }

        return null;
    }
}
