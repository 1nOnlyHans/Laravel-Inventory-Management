<?php

namespace App\Http\Controllers;

use App\Events\StockIn;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProductStockController extends Controller
{
    //
    public function index()
    {
        $stock = StockMovement::with(['product', 'user'])->latest()->get();
        return response()->json($stock, Response::HTTP_OK);
    }

    public function stockIn(Request $request)
    {


        $products = $request->product_id;
        $reference_no = $request->reference_no;

        foreach ($products as $product) {
            $stock = Product::findOrFail($product['product']['id']);
            $stock->product_quantity = $stock->product_quantity + $product['quantity']; // Refactor

            if ($stock->product_quantity + $product['quantity'] > $stock->reorder_level) {
                $stock->status = 'Available';
            } else if ($stock->product_quantity + $product['quantity'] <= $stock->reorder_level) {
                $stock->status = 'Low Stock';
            }

            $stock->save();

            StockMovement::create([
                'product_id' => $product['product']['id'],
                'user_id' => Auth::user()->id,
                'reference_no' => $reference_no,
                'movement_type' => 'Stock In',
                'quantity' => $product['quantity'],
                'reason' => 'Received From Supplier'
            ]);

            broadcast(new StockIn($stock))->toOthers();
        }

        //Price Adjustment
        $tax = SystemSetting::where('config_key', 'tax')->first();
        $profit = SystemSetting::where('config_key', 'profit')->first();
        $items = Purchase::with(['items'])->where('reference_no', $request->reference_no)->get();

        foreach ($items as $item) {
            foreach ($item->items as $i) {
                $cost = $i->unit_price;
                $profitAmount = $cost * ($profit->config_value / 100); //30%
                $taxAmount = $cost * ($tax->config_value / 100); //5%
                $newSellingPrice = $cost + $profitAmount + $taxAmount;
                $product = Product::findOrFail($i->product_id);
                $product->unit_price = $newSellingPrice;
                $product->save();
            }
        }

        return response()->json(['icon' => 'success', 'title' => 'Stock-In Successful', 'text' => 'Product quantities have been successfully updated in the inventory.'], Response::HTTP_OK);
    }
}
