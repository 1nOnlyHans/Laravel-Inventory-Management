<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    //
    protected $guarded = [];

    public function product()
    {
        // return $this->belongsTo(Product::class)->withTrashed(); // Fetch Products kahit softdeleted
        return $this->belongsTo(Product::class);
    }
}
