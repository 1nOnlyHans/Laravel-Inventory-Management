<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleTransaction extends Model
{
    //
    protected $table = 'sales_transactions';

    protected $fillable = [
        'customer_id',
        'reference_no',
        'payment_method',
        'amount_paid',
        'total_amount',
        'notes'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
