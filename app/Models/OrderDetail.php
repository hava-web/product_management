<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $table = 'order_details';
    protected $fillable = [
        'order_id',
        'product_id',
        'brand_id',
        'size_id',
        'warehouse_id',
        'agent_id',
        'color_id',
        'quantity',
        'discount',
        'price',
    ];
}
