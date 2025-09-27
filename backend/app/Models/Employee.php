<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    protected $table = 'employees';

    protected $fillable = [
        'unique_employee_id',
        'firstname',
        'middlename',
        'lastname',
        'email',
        'gender',
        'dob',
        'status'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
