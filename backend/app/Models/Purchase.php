<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //
    protected $table = 'purchase_orders';

    protected $fillable = [
        'supplier_id',
        'reference_no',
        'order_date',
        'expected_date',
        'status',
        'payment_status'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItems::class, 'purchase_order_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function paymongo()
    {
        return $this->hasOne(PaymongoSession::class);
    }

    public function paymentRecord()
    {
        return $this->hasOne(PurchasePaymentRecord::class);
    }
}
