<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleTransaction extends Model
{
    //
    protected $table = 'sales_transactions';

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
