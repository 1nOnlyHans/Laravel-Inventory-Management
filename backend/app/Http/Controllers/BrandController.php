<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class BrandController extends Controller
{
    //
    public function index()
    {

        $brands = Brand::with('products')->latest()->get();

        foreach ($brands as $brand) {
            // $brand->makeHidden(['id']);
            $brand->encrypted_id = Hashids::encode($brand->id);
        }

        return response()->json($brands, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => ['required', Rule::unique('brands', 'brand_name')],
            'brand_description' => ['nullable'],
        ]);

        $brand = Brand::firstOrCreate($validated);

        return response()->json(['icon' => 'success', 'title' => 'Added Successfully', 'text' => $brand->brand_name . ' has been added'], Response::HTTP_OK);
    }

    public function show(Request $request)
    {
        $id = Hashids::decode($request->route('brand_id'));
        $brand = Brand::with('products')->findOrFail($id);
        $brand->makeHidden(['id']);
        $brand->encrypted_id = Hashids::encode($brand->id);
        if (count($brand->products) > 0) {
            foreach ($brand->products as $product) {
                $product->makeHidden(['id']);
                $product->encrypted_id = Hashids::encode($product->id);
            }
        }

        return response()->json($brand, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $id = Hashids::decode($request->encrypted_id);
        $validated = $request->validate(['brand_name' => Rule::unique('brands', 'brand_name')->ignore($id[0], 'id'), 'brand_description' => ['nullable']]);
        $brand = Brand::findOrFail($id[0]);
        $brand->update($validated);

        return response(['icon' => 'success', 'title' => 'Updated Successfully', 'text' => $brand . ' has been updated'], Response::HTTP_OK);
    }

    public function destroy(Request $request)
    {
        $brand = Brand::findOrFail(Hashids::decode($request->brand_id)[0]);
        $brand->delete();
        return response(['icon' => 'success', 'title' => 'Deleted Successfully', 'text' => $brand . ' has been deleted'], Response::HTTP_OK);
    }
}
