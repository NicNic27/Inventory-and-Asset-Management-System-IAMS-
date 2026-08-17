<?php

namespace App\Services;

use App\Models\RisRequest;
use App\Models\RisItem;
use App\Models\Supply;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RisService
{
    /**
     * Get all valid RIS statuses
     */
    public static function getValidStatuses(): array
    {
        return [
            'Pending Staff Review',
            'Forwarded to Admin',
            'Approved',
            'Declined',
            'Rejected',
            'Cancelled'
        ];
    }

    /**
     * Create a new RIS request
     */
    public function create(array $data): RisRequest
    {
        try {
            DB::beginTransaction();

            $risRequest = RisRequest::create([
                'ris_no' => $this->generateRisNumber(),
                'entity_name' => $data['entity_name'],
                'division' => $data['division'],
                'office' => $data['office'],
                'fund_cluster' => $data['fund_cluster'] ?? null,
                'rcc' => $data['rcc'] ?? null,
                'purpose' => $data['purpose'],
                'sig_requested_by' => $data['sig_requested_by'],
                'sig_requested_by_name' => $data['sig_requested_by_name'] ?? null,
                'desig_requested' => $data['desig_requested'] ?? null,
                'date_requested' => $data['date_requested'] ?? now()->toDateString(),
                'status' => 'Pending Staff Review'
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created',
                'description' => "Created RIS Request: {$risRequest->ris_no}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $risRequest;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update RIS request
     */
    public function update(RisRequest $risRequest, array $data): RisRequest
    {
        try {
            DB::beginTransaction();

            $status = $risRequest->status;

            // Determine new status based on action
            if (isset($data['action'])) {
                if ($data['action'] === 'forward') {
                    $status = 'Forwarded to Admin';
                } elseif ($data['action'] === 'return') {
                    $status = 'Pending Staff Review';
                } elseif ($data['action'] === 'approve') {
                    $status = 'Approved';
                } elseif ($data['action'] === 'decline') {
                    $status = 'Declined';
                } elseif ($data['action'] === 'reject') {
                    $status = 'Rejected';
                }
            }

            $risRequest->update([
                'entity_name' => $data['entity_name'] ?? $risRequest->entity_name,
                'division' => $data['division'] ?? $risRequest->division,
                'office' => $data['office'] ?? $risRequest->office,
                'fund_cluster' => $data['fund_cluster'] ?? $risRequest->fund_cluster,
                'rcc' => $data['rcc'] ?? $risRequest->rcc,
                'purpose' => $data['purpose'] ?? $risRequest->purpose,
                'sig_requested_by' => $data['sig_requested_by'] ?? $risRequest->sig_requested_by,
                'sig_requested_by_name' => $data['sig_requested_by_name'] ?? $risRequest->sig_requested_by_name,
                'sig_approved_by' => $data['sig_approved_by'] ?? $risRequest->sig_approved_by,
                'sig_approved_by_name' => $data['sig_approved_by_name'] ?? $risRequest->sig_approved_by_name,
                'sig_issued_by' => $data['sig_issued_by'] ?? $risRequest->sig_issued_by,
                'sig_issued_by_name' => $data['sig_issued_by_name'] ?? $risRequest->sig_issued_by_name,
                'sig_received_by' => $data['sig_received_by'] ?? $risRequest->sig_received_by,
                'sig_received_by_name' => $data['sig_received_by_name'] ?? $risRequest->sig_received_by_name,
                'desig_requested' => $data['desig_requested'] ?? $risRequest->desig_requested,
                'desig_approved' => $data['desig_approved'] ?? $risRequest->desig_approved,
                'desig_issued' => $data['desig_issued'] ?? $risRequest->desig_issued,
                'desig_received' => $data['desig_received'] ?? $risRequest->desig_received,
                'date_requested' => $data['date_requested'] ?? $risRequest->date_requested,
                'date_approved' => $data['date_approved'] ?? $risRequest->date_approved,
                'date_issued' => $data['date_issued'] ?? $risRequest->date_issued,
                'date_received' => $data['date_received'] ?? $risRequest->date_received,
                'status' => $status
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Updated',
                'description' => "Updated RIS Request: {$risRequest->ris_no} - Status: {$status}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $risRequest->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process RIS approval (Admin action)
     */
    public function approve(RisRequest $risRequest, array $data): RisRequest
    {
        try {
            DB::beginTransaction();

            $risRequest->update([
                'sig_approved_by' => $data['sig_approved_by'] ?? $risRequest->sig_approved_by,
                'sig_approved_by_name' => $data['sig_approved_by_name'] ?? $risRequest->sig_approved_by_name,
                'desig_approved' => $data['desig_approved'] ?? $risRequest->desig_approved,
                'date_approved' => $data['date_approved'] ?? now()->toDateString(),
                'status' => 'Approved'
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Approved',
                'description' => "Approved RIS Request: {$risRequest->ris_no}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $risRequest->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Decline RIS request
     */
    public function decline(RisRequest $risRequest, string $reason = null): RisRequest
    {
        try {
            DB::beginTransaction();

            $risRequest->update([
                'status' => 'Declined',
                'remarks' => $reason ?? $risRequest->remarks
            ]);

            // Log activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Declined',
                'description' => "Declined RIS Request: {$risRequest->ris_no}" . ($reason ? " - Reason: {$reason}" : ''),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            DB::commit();

            return $risRequest->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get current stock for items in RIS
     */
    public function getItemsWithStock(RisRequest $risRequest): array
    {
        $items = $risRequest->items;

        return $items->map(function ($item) {
            $supply = Supply::where('barcode_id', $item->stock_no)->first();
            
            return [
                'item' => $item,
                'current_stock' => $supply ? $supply->quantity : 0,
                'available' => $supply ? $supply->quantity >= $item->qty_requested : false,
                'supply' => $supply
            ];
        })->toArray();
    }

    /**
     * Generate unique RIS number
     */
    protected function generateRisNumber(): string
    {
        $lastRis = RisRequest::latest('id')->first();
        $nextNumber = ($lastRis?->id ?? 0) + 1;

        return 'RIS-' . date('Ymd') . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
