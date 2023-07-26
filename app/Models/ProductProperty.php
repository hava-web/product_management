<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductProperty extends Model
{
    use HasFactory;
    protected $table = 'product_properties';
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'agent_id',
        'color_id',
        'brand_id',
        'size_id',
        'expired_date',
        'original_price',
        'selling_price',
        'status',
        'sale_percentage',
        'quantity',
    ];
}
