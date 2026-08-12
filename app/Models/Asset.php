<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'item_code',
        'barcode_id',
        'name',
        'article',
        'category',
        'description',
        'unit_measure',
        'supplier',
        'unit_value',
        'status',
        'image'
    ];
}