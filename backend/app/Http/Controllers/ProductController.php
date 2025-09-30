<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //

    public function index()
    {
        $products = Product::with(['supplier,category'])->latest()->get();

        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        //
    }
}
