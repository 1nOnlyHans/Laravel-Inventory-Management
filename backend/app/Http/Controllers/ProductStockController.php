<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProductStockController extends Controller
{
    //
    public function index(){
        
    }

    public function stockIn(Request $request)
    {
        $products = $request->product_id;

        foreach ($products as $product) {
            $stock = Product::findOrFail($product['product']['id']);
            $stock->product_quantity = $stock->product_quantity + $product['quantity'];

            if ($stock->product_quantity + $product['quantity'] > $stock->reorder_level) {
                $stock->status = 'Available';
            } else if ($stock->product_quantity + $product['quantity'] <= $stock->reorder_level) {
                $stock->status = 'Low Stock';
            }
            
            $stock->save();

            StockMovement::create([
                'product_id' => $product['product']['id'],
                'user_id' => Auth::user()->id,
                'movement_type' => 'Stock In',
                'quantity' => $product['quantity'],
                'reason' => 'Received From Supplier'
            ]);
        }


        return response()->json(['icon' => 'success', 'title' => 'Stock-In Successful', 'text' => 'Product quantities have been successfully updated in the inventory.'], Response::HTTP_OK);
    }
}
