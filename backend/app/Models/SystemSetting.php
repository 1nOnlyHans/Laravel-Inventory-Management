<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    //

    protected $fillable = [
        'user_id',
        'config_key',
        'config_value',
        'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
