<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCustody extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'transaction_type',
        'holder_name',
        'holder_position',
        'department',
        'unit',
        'issued_at',
        'due_at',
        'returned_at',
        'condition_on_issue',
        'condition_on_return',
        'remarks',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'due_at' => 'date',
            'returned_at' => 'date',
        ];
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}