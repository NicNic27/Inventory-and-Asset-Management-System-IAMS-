<?php

namespace App\Services;

use App\Models\Supply;
use App\Models\Transaction;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
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
     * Generate next sequence number for supply
     */
    protected function getNextSequenceNumber(): string
    {
        $setting = SystemSetting::firstOrCreate(
            ['key' => 'seq_stock_no'],
            ['value' => '1']
        );

        $nextSeq = (int)$setting->value + 1;
        $setting->update(['value' => $nextSeq]);

        return 'SUP-' . date('Ymd') . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
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

            $barcode = $this->getNextSequenceNumber();

            $supply = Supply::create([
                'barcode_id' => $barcode,
                'article' => $data['article'],
                'description' => $data['description'],
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
                    'transaction_date' => date('Y-m-d'),
                    'remarks' => 'Opening Balance / Initial Stock',
                    'date_time' => now()
                ]);
            }

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created',
                'description' => "Added new supply: {$supply->article} (Barcode: {$barcode})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $supply;
        } catch (\Exception $e) {
            DB::rollBack();

            if ($imageName && Storage::exists('public/supplies/' . $imageName)) {
                Storage::delete('public/supplies/' . $imageName);
            }

            throw $e;
        }
    }

    /**
     * Update an existing supply item
     */
    public function update(Supply $supply, array $data): Supply
    {
        $imageName = $supply->image;

        try {
            DB::beginTransaction();

            // Handle image update
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($imageName && Storage::exists('public/supplies/' . $imageName)) {
                    Storage::delete('public/supplies/' . $imageName);
                }

                $file = $data['image'];
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('supplies', $imageName, 'public');
                $data['image'] = $imageName;
            }

            $supply->update([
                'article' => $data['article'] ?? $supply->article,
                'description' => $data['description'] ?? $supply->description,
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

            return $supply;
        } catch (\Exception $e) {
            DB::rollBack();
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

            if ($supply->image && Storage::exists('public/supplies/' . $supply->image)) {
                Storage::delete('public/supplies/' . $supply->image);
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
        try {
            DB::beginTransaction();

            $quantity = $data['quantity'];
            $transactionType = strtoupper($data['type'] ?? 'IN'); // IN or OUT

            // Update supply quantity
            if ($transactionType === 'OUT') {
                $newQuantity = $supply->quantity - $quantity;
            } else {
                $newQuantity = $supply->quantity + $quantity;
            }

            $supply->update(['quantity' => $newQuantity]);

            // Create transaction record
            $transaction = Transaction::create([
                'item_id' => $supply->id,
                'item_type' => 'supplies',
                'transaction_type' => $transactionType,
                'quantity' => $quantity,
                'supplier' => $data['supplier'] ?? $supply->supplier,
                'transaction_date' => $data['transaction_date'] ?? date('Y-m-d'),
                'remarks' => $data['remarks'] ?? '',
                'date_time' => now()
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Transaction',
                'description' => "Stock {$transactionType}: {$supply->article} ({$quantity} units)",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
