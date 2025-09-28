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
        $employees = Employee::latest()->paginate(10);

        return response()->json([
            'employees' => $employees
        ], 200);
    }

    public function store(Request $request)
    {
        //Validate
        $validated = $request->validate(
            [
                'firstname' => ['required'],
                'middlename' => ['nullable'],
                'lastname' => ['required'],
                'email' => ['required', 'email', 'unique:employees'],
                'gender' => ['required'],
                'dob' => ['required']
            ]
        );

        //Make new
        $latest_employee_id = Employee::all();

        if (count($latest_employee_id) === 1) {
            $latest_employee_id = Employee::where('unique_employee_id', 'ADM-1001')->get();
        } else {
            $latest_employee_id = Employee::latest()->get();
        }

        $latest_employee_id = explode('-', $latest_employee_id[0]['unique_employee_id']);
        $latest_employee_id = $latest_employee_id[1];
        $latest_employee_id = intval($latest_employee_id) + 1;
        $unique_employee_id = 'EMP-' . $latest_employee_id;
        $validated['unique_employee_id'] = $unique_employee_id;
        $employee = Employee::create($validated);

        return response()->json([
            'icon' => 'success',
            'title' => 'New Employee Registered',
            'text' => 'Employee ' . $employee->firstname . ' ' . $employee->lastname . ' registered successfully'
        ], 200);
    }
}
