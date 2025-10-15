<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPhoto;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class ProductController extends Controller
{
    //
    public function index()
    {
        $products = Product::with(['supplier', 'category', 'brand', 'photos'])->latest()->get();

        foreach ($products as $product) {
            // $product->makeHidden(['id']);
            $product->encrypted_id = Hashids::encode($product->id);
        }

        return response()->json($products, Response::HTTP_OK);
    }
    public function archived()
    {
        $products = Product::with(['supplier', 'category', 'brand', 'photos'])->onlyTrashed()->latest()->get();

        foreach ($products as $product) {
            // $product->makeHidden(['id']);
            $product->encrypted_id = Hashids::encode($product->id);
        }

        return response()->json($products, Response::HTTP_OK);
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

        $supplier_id = Hashids::decode($validated['supplier_id']);
        $category_id = Hashids::decode($validated['category_id']);
        $brand_id = Hashids::decode($validated['brand_id']);

        $status = intval($validated['product_quantity']) === 0
            ? 'Out of Stock'
            : (intval($validated['product_quantity']) <= $validated['reorder_level']
                ? 'Low Stock'
                : 'Available');

        $tax = SystemSetting::where('config_key', 'tax')->first();
        $profit = SystemSetting::where('config_key', 'profit')->first();

        $cost = $validated['unit_price'];
        $profitAmount = $cost * ($profit->config_value / 100); //30%
        $taxAmount = $cost * ($tax->config_value / 100); //5%
        $sellingPrice = $cost + $profitAmount + $taxAmount;

        $product = Product::create([
            'supplier_id' => $supplier_id[0],
            'category_id' => $category_id[0],
            'brand_id' => $brand_id[0],
            'SKU' => $validated['SKU'],
            'model' => $validated['model'],
            'product_name' => $validated['product_name'],
            'product_description' => $validated['product_description'],
            'product_quantity' => $validated['product_quantity'],
            'unit_price' => $sellingPrice,
            'reorder_level' => $validated['reorder_level'],
            'status' => $status
        ]);

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');

            foreach ($files as $file) {
                $path = $file->store('product_photos', 'public');
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'image' => $path
                ]);
            }
        }

        // if ($status === 'Low Stock') {
        //     broadcast(new LowStock($product))->toOthers();
        // }

        return response()->json(['icon' => 'success', 'title' => 'Added Successfully', 'text' => $product->product_name . ' has been added'], Response::HTTP_OK);
    }

    public function show(Request $request)
    {
        $id = Hashids::decode($request->route('product_id'));
        $product = Product::with(['supplier', 'category', 'brand', 'photos', 'purchases'])->findOrFail($id[0]);
        $product->makeHidden(['id']);
        // $product->supplier->makeHidden(['id']);
        $product->supplier->encrypted_id = Hashids::encode($product->supplier->id);
        if (count($product->purchases) > 0) {
            $product->latest_srp = $product->purchases[count($product->purchases) - 1]->unit_price;
        } else {
            $product->latest_srp = 0;
        }
        $product->encrypted_id = Hashids::encode($product->id);
        return response()->json($product, Response::HTTP_OK);
    }

    public function update(Request $request)
    {

        //Decrypt ID
        $id = Hashids::decode($request->product_id);

        //VALIDATE
        $validated = $request->validate([
            'supplier_id' => ['required'],
            'category_id' => ['required'],
            'brand_id' => ['required'],
            'SKU' => ['required', Rule::unique('products', 'SKU')->ignore($id[0], 'id')],
            'model' => ['required'],
            'product_name' => ['required', Rule::unique('products', 'product_name')->ignore($id[0], 'id')],
            'product_description' => ['required'],
            'product_quantity' => ['required'],
            'unit_price' => ['required'],
            'reorder_level' => ['required'],
        ]);

        //FIND INSTANCE
        $product = Product::findOrFail($id[0]);

        //Check Status
        $status = intval($validated['product_quantity']) === 0
            ? 'Out of Stock'
            : (intval($validated['product_quantity']) <= $validated['reorder_level']
                ? 'Low Stock'
                : 'Available');
        //UPDATE

        $product->update([
            'supplier_id' => $validated['supplier_id'],
            'category_id' => $validated['category_id'],
            'brand_id' => $validated['brand_id'],
            'SKU' => $validated['SKU'],
            'model' => $validated['model'],
            'product_name' => $validated['product_name'],
            'product_description' => $validated['product_description'],
            'product_quantity' => $validated['product_quantity'],
            'unit_price' => $validated['unit_price'],
            'reorder_level' => $validated['reorder_level'],
            'status' => $status
        ]);

        //Checks if there's an image
        if ($request->hasFile('photos')) {
            //Validate Image Type
            $validatedPhotos = $request->validate([
                'photos' => ['required', 'array'],
                'photos.*' => [File::types(['jpg', 'jpeg', 'png', 'svg', 'webp'])]
            ]);

            //Photos
            $photos = $request->file('photos');

            // Loop
            foreach ($photos as $photo) {
                $path = $photo->store('product_photos', 'public'); // Stores on Storage

                //Insert new in DB
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'image' => $path
                ]);
            }
        }

        // if ($status === 'Low Stock') {
        //     broadcast(new LowStock($product))->toOthers();
        // } else if ($status === 'Out of Stock') {
        //     broadcast(new OutOfStock($product))->toOthers();
        // }

        return response()->json(['icon' => 'success', 'title' => 'Updated Successfully', 'text' => $product . ' has been updated'], Response::HTTP_OK);
    }

    public function restore(Request $request)
    {
        $id =  Hashids::decode($request->product_id);

        $product = Product::withTrashed()->findOrFail($id[0])->restore();
        return response()->json(['icon' => 'success', 'title' => 'Item has been Restored', 'text' => 'item has been restored'], Response::HTTP_OK);
    }

    public function softDelete(Request $request)
    {
        $id = Hashids::decode($request->product_id);

        $product = Product::findOrFail($id[0]);

        $product->delete();

        return response()->json(['icon' => 'success', 'title' => 'Item has been Archived', 'text' => 'item has been stored in archived section'], Response::HTTP_OK);
    }

    public function forceDelete(Request $request)
    {
        $id =  Hashids::decode($request->product_id);
        $photos = ProductPhoto::where('product_id', $id[0])->get();

        foreach ($photos as $photo) {
            if (Storage::disk('public')->exists($photo->image)) {
                Storage::disk('public')->delete($photo->image);
            }
        }

        $product = Product::withTrashed()->findOrFail($id[0])->forceDelete();

        return response()->json(['icon' => 'success', 'title' => 'Item has been permanently deleted', 'text' => 'Item datas is permanently deleted'], Response::HTTP_OK);
    }

    public function getProductsBySupplier(Request $request)
    {
        $id = Hashids::decode($request->supplier_id);
        $products = Product::with('supplier')->where('supplier_id', $id[0])->get();
        foreach ($products as $product) {
            $product->makeHidden(['id']);
            $product->encrypted_id = Hashids::encode($product->id);
        }
        return response()->json($products, Response::HTTP_OK);
    }

    public function deleteProductPhoto(Request $request)
    {
        $photo = ProductPhoto::findOrFail($request->photo_id);
        if (Storage::disk('public')->exists($photo->image)) {
            Storage::disk('public')->delete($photo->image);
            $photo->delete();
            return response()->json(['icon' => 'success', 'title' => 'Photo Deleted', 'text' => 'Selected photo has been deleted'], Response::HTTP_OK);
        } else {
            return response()->json(['icon' => 'error', 'title' => 'Failed to Delete', 'text' => 'Failed to delete photo'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
