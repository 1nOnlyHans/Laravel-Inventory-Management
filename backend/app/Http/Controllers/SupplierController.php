<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class SupplierController extends Controller
{
    //
    public function index()
    {
        // $suppliers = Supplier::with('products')->latest()->paginate(10);

        // $suppliers->getCollection()->transform(function ($supplier) {
        //     $supplier->encrypted_id = Crypt::encryptString($supplier->id);
        //     $supplier->makeHidden(['id']);
        //     return $supplier;
        // });

        $suppliers = Supplier::with('products')->latest()->get();

        foreach ($suppliers as $supplier) {
            $supplier->makeHidden(['id']);
            $supplier->encrypted_id = Crypt::encryptString($supplier->id);
        }

        return response()->json($suppliers, 200);
    }

    public function store(Request $request)
    {
        //

        $validated = $request->validate(
            [
                'supplier_name' => ['required', Rule::unique('suppliers', 'supplier_name')],
                'contact_person' => ['required'],
                'phone' => ['required'],
                'email' => ['required', Rule::unique('suppliers', 'email')],
                'address' => ['required']
            ]
        );

        $supplier = Supplier::create($validated);

        return response()->json(
            [
                'icon' => 'success',
                'title' => 'Added Successfully',
                'text' => $supplier->supplier_name . ' has been added'
            ],
            Response::HTTP_OK
        );
    }

    public function show(Request $request)
    {
        $id = Crypt::decryptString($request->route('supplier_id'));
        $supplier = Supplier::with('products')->findOrFail($id);
        $supplier->makeHidden(['id']);
        $supplier->encrypted_id = $request->encrypted_id;
        if ($supplier->products && count($supplier->products) > 0) {
            foreach ($supplier->products as $product) {
                $product->makehidden['id'];
                $product->encrypted_id = Crypt::encryptString($product->id);
            }
        }
        return response()->json($supplier, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $id = Crypt::decryptString($request->encrypted_id);
        $validated = $request->validate([
            'encrypted_id' => ['required'], // or supplier_id
            'supplier_name' => ['required', Rule::unique('suppliers', 'supplier_name')->ignore($id, 'id')],
            'contact_person' => ['required'],
            'phone' => ['required'],
            'email' => ['required', Rule::unique('suppliers', 'email')->ignore($id, 'id')],
            'address' => ['required']
        ]);
        $supplier = Supplier::where('id', $id)->update(['supplier_name' => $validated['supplier_name'], 'contact_person' => $validated['contact_person'], 'phone' => $validated['phone'], 'email' => $validated['email'], 'address' => $validated['address']]);
        return response()->json(['icon' => 'success', 'title' => 'Updated Successfully', 'text' => $supplier . ' supplier has been updated'], Response::HTTP_OK);
    }
    public function destroy(Request $request)
    {
        $supplier = Supplier::destroy(Crypt::decryptString($request->encrypted_id));

        return response()->json(
            [
                'icon' => 'success',
                'title' => 'Deleted Successfully',
                'text' => $supplier . ' has been deleted'
            ],
            Response::HTTP_OK
        );
    }
}
