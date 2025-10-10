<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockAlert;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AdminDashboardController extends Controller
{
    //

    public function getDashboardDatas()
    {
        $stocks = Product::with(['category'])->get();
        $orders = Purchase::where('status', 'Pending')->count();
        $low_stock = StockAlert::where('alert_message', 'Low Stock')->where('status', 'Pending')->count();
        $no_stock = StockAlert::where('alert_message', 'Out of Stock')->where('status', 'Pending')->count();
        $movements = StockMovement::whereHas('product')->with(['product', 'user'])->get();
        $stock_ins = StockMovement::with('product')->where('movement_type', 'Stock In')->whereDate('created_at', Carbon::today())->get();
        $total_stocks_per_category = [];
        $total_received_stocks = 0;
        $total_stocks = 0;
        $inventory_value = 0;

        foreach ($stocks as $stock) {
            $total_stocks += intval($stock->product_quantity);
            $inventory_value += floatval($stock->unit_price * $stock->product_quantity);
            $category_name = $stock->category->category_name;

            $total_stocks_per_category[$category_name] = 0;
            $total_stocks_per_category[$category_name] += intval($stock->product_quantity);
        }

        foreach ($stock_ins as $stock) {
            $total_received_stocks += intval($stock->quantity);
        }

        return response()->json([
            'total_stocks' => $total_stocks,
            'orders' => $orders,
            'low_stock' => $low_stock,
            'no_stock' => $no_stock,
            'inventory_value' => $inventory_value,
            'received_stocks' => $total_received_stocks,
            'movements' => $movements,
            'total_stocks_per_category' => $total_stocks_per_category,
        ], Response::HTTP_OK);
    }
}
