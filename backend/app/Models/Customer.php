<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $guarded = [];

    public function payments()
    {
        return $this->hasMany(SaleTransaction::class);
    }
}
