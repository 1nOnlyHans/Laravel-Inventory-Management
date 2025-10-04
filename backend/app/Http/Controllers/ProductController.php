<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    //

    public function index()
    {
        $products = Product::with(['supplier', 'category', 'brand'])->latest()->get();

        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'supplier_id' => ['required'],
            'category_id' => ['required'],
            'brand_id' => ['required'],
            'SKU' => ['required', Rule::unique('products', 'SKU')],
            'model' => ['required'],
            'product_name' => ['required', Rule::unique('products', 'product_name')],
            'product_description' => ['required'],
            'product_quantity' => ['required'],
            'unit_price' => ['required'],
            'reorder_level' => ['required'],
            'photos' => ['required', 'array'],
            'photos.*' => [File::types(['jpg', 'jpeg', 'png', 'svg', 'webp'])]
        ]);

        $supplier_id = Crypt::decryptString($validated['supplier_id']);
        $category_id = Crypt::decryptString($validated['category_id']);
        $brand_id = Crypt::decryptString($validated['brand_id']);

        $status = intval($validated['product_quantity']) === 0
            ? 'Out of Stock'
            : (intval($validated['product_quantity']) <= $validated['reorder_level']
                ? 'Low Stock'
                : 'Available');

        $product = Product::create([
            'supplier_id' => $supplier_id,
            'category_id' => $category_id,
            'brand_id' => $brand_id,
            'SKU' => $validated['SKU'],
            'model' => $validated['model'],
            'product_name' => $validated['product_name'],
            'product_description' => $validated['product_description'],
            'product_quantity' => $validated['product_quantity'],
            'unit_price' => $validated['unit_price'],
            'reorder_level' => $validated['reorder_level'],
            'status' => $status
        ]);

        $files = $request->file('photos');

        foreach ($files as $file) {
            $file->store('brand_photos', 'public');
        }

        return response()->json(['icon' => 'success', 'title' => 'Added Successfully', 'text' => $product->product_name . ' has been added'], Response::HTTP_OK);
    }

    public function show(Request $request)
    {
        $id = Crypt::decryptString($request->route('product_id'));
        $product = Product::findOrFail($id);
        $product->makeHidden(['id']);
        $product->encrypted_id = Crypt::decrypt($product->id);
        response($product, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $id = Crypt::decryptString($request->product_id);
        $validated = $request->validate([
            'supplier_id' => ['required'],
            'category_id' => ['category_id'],
            'SKU' => ['required', Rule::unique('products', 'SKU')->ignore($id, 'id')],
            'product_name' => ['required', Rule::unique('products', 'product_name')],
            'product_description' => ['required'],
            'product_quantity' => ['required'],
            'unit_price' => ['required'],
            'reorder_level' => ['required']
        ]);

        $product = Product::findOrFail($id);
        $product->update($validated);

        return response()->json(['icon' => 'success', 'title' => 'Updated Successfully', 'text' => $product . ' has been updated'], Response::HTTP_OK);
    }

    public function softDelete(Request $request)
    {
        $id = Crypt::decryptString($request->product_id);

        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json(['icon' => 'success', 'title' => 'Item has been Archived', 'text' => 'item has been stored in archived section'], Response::HTTP_OK);
    }

    public function getProductsBySupplier(Request $request)
    {
        $products = Product::with('supplier')->where('supplier_id', Crypt::decryptString($request->supplier_id))->get();
        foreach ($products as $product) {
            $product->makeHidden(['id']);
            $product->encrypted_id = Crypt::encryptString($product->id);
        }
        return response()->json($products, Response::HTTP_OK);
    }
}
