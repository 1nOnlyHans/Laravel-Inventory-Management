<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    //

    public function index()
    {
        $employees = Employee::paginate(10);

        return response()->json([
            'employees' => $employees
        ], 200);
    }
}
