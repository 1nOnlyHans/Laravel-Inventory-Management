<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $fillable = [
        'registered_by',
        'name',
        'email',
        'phone',
        'address'
    ];

    public function payments()
    {
        return $this->hasMany(SaleTransaction::class);
    }
}
