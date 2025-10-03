<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //
    protected $table = 'purchase_orders';

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItems::class, 'purchase_order_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
