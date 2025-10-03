<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    //

    public function index()
    {
        $products = Product::with(['supplier,category'])->latest()->paginate(10);

        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        //
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
