<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePaymentRecord extends Model
{
    //

    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
