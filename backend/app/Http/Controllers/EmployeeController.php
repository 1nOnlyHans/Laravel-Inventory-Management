<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    //

    public function index()
    {
        $employees = Employee::with('user') ->latest()->paginate(10);

        // map each employee and replace id with encrypted one
        $employees->getCollection()->transform(function ($employee) {
            $employee->encrypted_id = Crypt::encryptString($employee->id);
            return $employee;
        });

        //Same shit
        // foreach ($employees as $employee) {
        //     $employee->encrypted_id = Crypt::encryptString($employee->id);
        // }

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
                'dob' => ['required'],
                'role' => ['required']
            ]
        );

        //Make new
        $latest_employee_id = Employee::all();

        if (count($latest_employee_id) === 1) {
            $latest_employee_id = Employee::where('unique_employee_id', 'ADM-1001')->get();
        } else {
            $latest_employee_id = Employee::latest()->get();
        }

        // Fetching for latest Employee ID
        $latest_employee_id = explode('-', $latest_employee_id[0]['unique_employee_id']);
        $latest_employee_id = $latest_employee_id[1];
        $latest_employee_id = intval($latest_employee_id) + 1;
        $unique_employee_id = 'EMP-' . $latest_employee_id;
        $validated['unique_employee_id'] = $unique_employee_id;
        $employee = Employee::create($validated);

        //Employee Account Creation
        $username = $employee->unique_employee_id;
        $password = $validated['role'];

        $user = User::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'username' => $username,
                'role' => $validated['role'],
                'password' => $password
            ]
        );

        return response()->json([
            'icon' => 'success',
            'title' => 'New Employee Registered',
            'text' => 'Employee ' . $employee->firstname . ' ' . $employee->lastname . ' registered successfully'
        ], 200);
    }

    public function show(Request $request)
    {
        $employee_id = Crypt::decryptString($request->route('employee_id'));
        //QUERY BUILDER
        // $employee = DB::table('employees')->select('unique_employee_id')->where('id', $employee_id)->get();
        $employee = Employee::with(['user'])->findOrFail($employee_id);
        $employee->makeHidden(['id']);          // hides employee id
        if ($employee->user) {
            $employee->user->makeHidden(['id']);    // hides user id
            $employee->user->makeHidden(['employee_id']);
            $employee->user->encrypted_id = Crypt::encryptString($employee->user->id);
        }
        $employee->encrypted_id = Crypt::encryptString($employee_id);
        return response()->json($employee, 200);
    }

    public function update(Request $request)
    {
        $id = Crypt::decryptString($request->encrypted_id);

        $validated = $request->validate(
            [
                'firstname' => ['required'],
                'middlename' => ['nullable'],
                'lastname' => ['required'],
                'email' => ['required', 'email', Rule::unique('employees')->ignore($id, 'id')],
                'gender' => ['required'],
                'dob' => ['required']
            ]
        );

        $employee = Employee::where('id', $id)->update(['firstname' => $validated['firstname'], 'middlename' => $validated['middlename'], 'lastname' => $validated['lastname'], 'email' => $validated['email'], 'gender' => $validated['gender'], 'dob' => $request['dob'], 'status' => $request->status]);

        return response()->json([
            'icon' => 'success',
            'title' => 'Update Success',
            'text' => $employee . ' has been updated'
        ], 200);
    }

    public function updateAccount(Request $request)
    {
        $id = Crypt::decryptString($request->encrypted_id);
        $validated = $request->validate(
            [
                'role' => ['required']
            ]
        );

        $user = User::with('employee')->where('id', $id)->update(['role' => $validated['role']]);
        return response()->json([
            'icon' => 'success',
            'title' => 'Update Success',
            'text' => $user . ' has been updated'
        ], 200);

        return response()->json($user, 200);
    }
}
