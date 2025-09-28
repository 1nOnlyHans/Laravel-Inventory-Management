<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    //
    use HasFactory;
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

    //One account for employee
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
