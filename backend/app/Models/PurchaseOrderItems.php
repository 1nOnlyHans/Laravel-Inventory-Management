<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItems extends Model
{
    //
    protected $table = 'purchase_order_items';

    protected $guarded = [];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
