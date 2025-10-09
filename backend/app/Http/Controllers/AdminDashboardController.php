<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockAlert;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminDashboardController extends Controller
{
    //

    public function getDashboardDatas()
    {
        $stocks = Product::with(['category'])->get();
        $orders = Purchase::where('status', 'Pending')->count();
        $low_stock = StockAlert::where('alert_message', 'Low Stock')->count();
        $movements = StockMovement::with(['product', 'user'])->get();
        $total_stocks_per_category = [];
        $total_stocks = 0;
        $inventory_value = 0;
        foreach ($stocks as $stock) {
            $total_stocks += intval($stock->product_quantity);
            $inventory_value += floatval($stock->unit_price * $stock->product_quantity);
            $category_name = $stock->category->category_name;

            $total_stocks_per_category[$category_name] = 0;
            $total_stocks_per_category[$category_name] += intval($stock->product_quantity);
        }

        return response()->json([
            'total_stocks' => $total_stocks,
            'orders' => $orders,
            'low_stock' => $low_stock,
            'inventory_value' => $inventory_value,
            'movements' => $movements,
            'total_stocks_per_category' => $total_stocks_per_category,
        ], Response::HTTP_OK);
    }
}
