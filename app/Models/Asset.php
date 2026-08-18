<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'inventory_date',
        'item_code',
        'barcode_id',
        'name',
        'article',
        'category',
        'description',
        'model',
        'serial_number',
        'acquisition_date',
        'unit_measure',
        'person_accountable',
        'validation_signatory',
        'supplier',
        'unit_value',
        'status',
        'image'
    ];

    protected function casts(): array
    {
        return [
            'inventory_date' => 'date',
            'acquisition_date' => 'date',
            'unit_value' => 'decimal:2',
        ];
    }
}