<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    //
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'category_id',
        'brand_id',
        'SKU',
        'model',
        'product_name',
        'product_description',
        'product_quantity',
        'unit_price',
        'reorder_level',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function productOrders()
    {
        return $this->hasMany(PurchaseOrderItems::class);
    }

    public function photos()
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function movement()
    {
        return $this->hasMany(StockMovement::class);
    }
}
