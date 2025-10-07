<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use function PHPSTORM_META\map;

class PaymongoSession extends Model
{
    //

    protected $fillable = [
        'purchase_id',
        'session_id'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
