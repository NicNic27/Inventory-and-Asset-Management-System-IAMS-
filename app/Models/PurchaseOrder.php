<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'po_type', 'entity_name', 'po_no', 'supplier_name', 'supplier_address', 'po_date', 
        'procurement_mode', 'auth_official', 'chief_accountant', 'total_amount', 'status',
        'place_of_delivery', 'date_of_delivery', 'delivery_term', 'payment_term',
        'auth_official_designation', 'chief_accountant_designation'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /** Recompute Pending/Partial/Complete from each item's actual delivered vs ordered quantity */
    public function recomputeStatus(): string
    {
        $items = $this->items()->get();

        if ($items->isEmpty()) {
            return $this->status ?? 'Pending';
        }

        $statuses = $items->map(fn (PurchaseOrderItem $item) => $item->getDeliveryStatus());

        if ($statuses->every(fn ($s) => $s === 'complete')) {
            $status = 'Complete';
        } elseif ($statuses->every(fn ($s) => $s === 'pending')) {
            $status = 'Pending';
        } else {
            $status = 'Partial';
        }

        $this->update(['status' => $status]);

        return $status;
    }
}