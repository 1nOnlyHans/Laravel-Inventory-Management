<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class CategoryController extends Controller
{
    //

    public function index()
    {
        // $categories = Category::with('products')->latest()->paginate(10);

        // $categories->getCollection()->transform(function ($category) {
        //     $category->encrypted_id = Hashids::decode($category->id);
        //     $category->makeHidden(['id']);
        //     return $category;
        // });

        $categories = Category::with('products')->latest()->get();

        foreach ($categories as $category) {
            $category->makeHidden(['id']);
            $category->encrypted_id = Hashids::encode($category->id);
        }

        return response()->json($categories, 200);
    }

    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'category_name' => ['required', Rule::unique('categories', 'category_name')],
            'category_description' => ['required']
        ]);

        $category = Category::firstOrCreate($validated);

        return response()->json(
            [
                'icon' => 'success',
                'title' => 'Added Successfully',
                'text' => $category->category_name . ' has been added to the category list'
            ],
            Response::HTTP_OK
        );
    }

    public function show(Request $request)
    {
        $id = Hashids::decode($request->encrypted_id)[0];

        $category = Category::with('products')->findOrFail($id);
        $category->makeHidden(['id']);
        if ($category->products) {
            foreach ($category->products as $product) {
                $product->encrypted_id = Hashids::encode($product->id);
                $product->makeHidden(['id']);
            }
        }
        return response()->json($category, Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $id = Hashids::decode($request->encrypted_id)[0];
        $validated = $request->validate(
            [
                'category_name' => [
                    'required',
                    Rule::unique('categories', 'category_name')->ignore($id, 'id'),
                ],
                'category_description' => ['required']
            ]
        );

        $category = Category::findOrFail($id);
        $category->update($validated);
        return response(['icon' => 'success', 'title' => 'Updated Successfully', 'text' => $category . ' has been updated'], Response::HTTP_OK);
    }

    public function destroy(Request $request)
    {
        $category = Category::destroy(Hashids::decode($request->encrypted_id)[0]);

        return response(['icon' => 'success', 'title' => 'Deleted Successfully', 'text' => $category . ' has been deleted'], Response::HTTP_OK);
    }
}
