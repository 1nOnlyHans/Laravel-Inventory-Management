<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\File;
use Symfony\Component\HttpFoundation\Response;

class CsvImportController extends Controller
{
    //
    public function employeeBulkImport(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|mimes:csv,txt',
            ]);
        } else {
            return response()->json(['icon' => 'error', 'title' => 'Invalid or Empty File', 'text' => 'Select csv file first'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file');

        $handle = fopen($file->getPathname(), "r");

        if ($handle === false) {
            return response()->json([
                'message' => [
                    'icon' => 'error',
                    'title' => 'Import Error',
                    'text' => 'Failed to open file.'
                ]
            ], 400);
        }

        $header = true;
        $count = 0;

        DB::beginTransaction();

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if ($header) {
                $header = false;
                continue;
            }

            if (isset($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6])) {
                $firstname = $row[0];
                $middlename = $row[1];
                $lastname = $row[2];
                $email = $row[3];
                $gender = ucfirst($row[4]);
                $dob = date('Y-m-d', strtotime($row[5]));
                $role = ucfirst($row[6]);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    DB::rollBack();
                    return response()->json([
                        'message' => [
                            'icon' => 'error',
                            'title' => 'Import Error',
                            'text' => "Email {$email} is invalid."
                        ]
                    ], 201);
                }

                if (Employee::where('email', $email)->exists()) {
                    DB::rollBack();
                    return response()->json([
                        'message' => [
                            'icon' => 'error',
                            'title' => 'Import Error',
                            'text' => "Email {$email} already exists."
                        ]
                    ], 201);
                }

                $last_digit = Employee::all()->count() + 1;
                if ($role === 'Admin') {
                    $unique_employee_id = 'ADM-' . $last_digit;
                } else if ($role === 'Staff') {
                    $unique_employee_id = 'EMP-' . $last_digit;
                }

                $employee = Employee::create([
                    'unique_employee_id' => $unique_employee_id,
                    'firstname' => $firstname,
                    'middlename' => $middlename,
                    'lastname' => $lastname,
                    'email' => $email,
                    'gender' => $gender,
                    'dob' => $dob
                ]);

                $username = $employee->unique_employee_id;
                $password = $role;

                $user = User::firstOrCreate(
                    [
                        'employee_id' => $employee->id,
                        'username' => $username,
                        'role' => $role,
                        'password' => $password
                    ]
                );
                $count++;
            } else {
                return response()->json(['icon' => 'error', 'title' => 'Missing Required Columns', 'text' => 'The submitted file is missing a required columns'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
        DB::commit();
        fclose($handle);
        return response()->json([
            'icon' => 'success',
            'title' => 'Import Success',
            'text' => "CSV imported successfully. {$count} records added."

        ], 200);
    }


    public function suppliersBulkImport(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate(['file' => 'required|mimes:csv,txt']);
        } else {
            return response()->json(['icon' => 'error', 'title' => 'Invalid or Empty File', 'text' => 'Select csv file first'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            return response()->json([
                'message' => [
                    'icon' => 'error',
                    'title' => 'Import Error',
                    'text' => 'Failed to open file.'
                ]
            ], 400);
        }

        $header = true;
        $count = 0;

        DB::beginTransaction(); // Transaction

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {

            if ($header) {
                $header = false;
                continue;
            }
            if (isset($row[0], $row[1], $row[2], $row[3], $row[4])) {

                $supplier_name = ucwords($row[0]);
                $contact_person = ucwords($row[1]);
                $phone = $row[2];
                $email = $row[3];
                $address = ucwords($row[4]);

                if (Supplier::where('supplier_name', $supplier_name)->exists()) {
                    DB::rollBack(); // rollback
                    return response()->json([
                        'message' => [
                            'icon' => 'error',
                            'title' => 'Import Error',
                            'text' => "Supplier {$supplier_name} already exists."
                        ]
                    ], 201);
                }

                if (!filter_var($email, FILTER_SANITIZE_EMAIL)) {
                    DB::rollBack();
                    return response()->json([
                        'message' => [
                            'icon' => 'error',
                            'title' => 'Import Error',
                            'text' => "Email {$email} is invalid."
                        ]
                    ], 201);
                }

                if (Supplier::where('email', $email)->exists()) {
                    DB::rollBack();
                    return response()->json([
                        'message' => [
                            'icon' => 'error',
                            'title' => 'Import Error',
                            'text' => "Supplier Email {$email} already exists."
                        ]
                    ], 201);
                }

                $supplier = Supplier::create([
                    'supplier_name' => $supplier_name,
                    'contact_person' => $contact_person,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => $address
                ]);

                $count++;
            } else {
                return response()->json(['icon' => 'error', 'title' => 'Missing Required Columns', 'text' => 'The submitted file is missing a required columns'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        DB::commit(); // Commit Changes
        fclose($handle);
        return response()->json([
            'icon' => 'success',
            'title' => 'Import Success',
            'text' => "CSV imported successfully. {$count} records added."

        ], 200);
    }

    public function categoriesBulkImport(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate(['file' => 'required|mimes:csv,txt']);
        } else {
            return response()->json(['icon' => 'error', 'title' => 'Invalid or Empty File', 'text' => 'Select csv file first'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            return response()->json([
                'message' => [
                    'icon' => 'error',
                    'title' => 'Import Error',
                    'text' => 'Failed to open file.'
                ]
            ], 400);
        }

        $header = true;
        $count = 0;

        DB::beginTransaction(); // Transaction

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {

            if ($header) {
                $header = false;
                continue;
            }
            if (isset($row[0], $row[1])) {

                $category_name = ucwords($row[0]);
                $category_description = $row[1];

                if (Category::where('category_name', $category_name)->exists()) {
                    DB::rollBack(); // rollback
                    return response()->json([
                        'message' => [
                            'icon' => 'error',
                            'title' => 'Import Error',
                            'text' => "Category {$category_name} already exists."
                        ]
                    ], 201);
                }

                $category = Category::create([
                    'category_name' => $category_name,
                    'category_description' => $category_description,
                ]);

                $count++;
            } else {
                return response()->json(['icon' => 'error', 'title' => 'Missing Required Columns', 'text' => 'The submitted file is missing a required columns'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        DB::commit(); // Commit Changes
        fclose($handle);
        return response()->json([
            'icon' => 'success',
            'title' => 'Import Success',
            'text' => "CSV imported successfully. {$count} records added."

        ], 200);
    }

    public function brandsBulkImport(Request $request)
    {
        if ($request->hasFile('file')) {
            $request->validate(['file' => 'required|mimes:csv,txt']);
        } else {
            return response()->json(['icon' => 'error', 'title' => 'Invalid or Empty File', 'text' => 'Select csv file first'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');

        if ($handle === false) {
            return response()->json([
                'message' => [
                    'icon' => 'error',
                    'title' => 'Import Error',
                    'text' => 'Failed to open file.'
                ]
            ], 400);
        }

        $header = true;
        $count = 0;

        DB::beginTransaction(); // Transaction

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {

            if ($header) {
                $header = false;
                continue;
            }
            if (isset($row[0], $row[1])) {

                $brand_name = ucwords($row[0]);
                $brand_description = $row[1];

                if (Brand::where('brand_name', $brand_name)->exists()) {
                    DB::rollBack(); // rollback
                    return response()->json([
                        'message' => [
                            'icon' => 'error',
                            'title' => 'Import Error',
                            'text' => "Brand {$brand_name} already exists."
                        ]
                    ], 201);
                }

                $brand = Brand::create([
                    'brand_name' => $brand_name,
                    'brand_description' => $brand_description,
                ]);

                $count++;
            } else {
                return response()->json(['icon' => 'error', 'title' => 'Missing Required Columns', 'text' => 'The submitted file is missing a required columns'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        DB::commit(); // Commit Changes
        fclose($handle);
        return response()->json([
            'icon' => 'success',
            'title' => 'Import Success',
            'text' => "CSV imported successfully. {$count} records added."

        ], 200);
    }
}
