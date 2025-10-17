<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePaymentRecord extends Model
{
    //

    protected $fillable = [
        'purchase_id',
        'reference_no',
        'payment_method',
        'amount_paid',
        'total_amount'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
