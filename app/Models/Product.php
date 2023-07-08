<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'category',
        'description',
        'quantity',
        'status',
        'imported_date',
        'expired_date',
        'warehouse_id',
        'delivered_from',
    ];

    public function category(){
        return $this->belongsTo(Category::class,'category','id');
    }

    public function productImages(){
        return $this->hasMany(ProductImage::class,'product_id','id');
    }

    public function productProperty(){
        return $this->hasMany(ProductProperty::class,'product_id','id');
    }

    public function orderDetail(){
        return $this->hasMany(OrderDetail::class,'product_id','id');
    }


}
