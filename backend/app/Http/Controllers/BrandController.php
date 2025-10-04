<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class BrandController extends Controller
{
    //
    public function index()
    {

        $brands = Brand::with('products')->latest()->get();

        foreach ($brands as $brand) {
            $brand->makeHidden(['id']);
            $brand->encrypted_id = Crypt::encryptString($brand->id);
        }

        return response()->json($brands, 200);
    }
}
