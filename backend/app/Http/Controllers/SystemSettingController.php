<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseOrderItems;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SystemSettingController extends Controller
{
    //
    public function index()
    {
        $config = SystemSetting::with(['user'])->get();

        return response()->json($config, Response::HTTP_OK);
    }

    public function create(Request $request)
    {
        $validated = $request->validate(['config_key' => ['required'], 'config_value' => ['required']]);
        $config = SystemSetting::updateOrCreate(['config_key' => $validated['config_key']], ['user_id' => Auth::user()->id, 'config_value' => $validated['config_value']]);

        $products = Product::all();
        $tax = SystemSetting::where('config_key', 'tax')->first();
        $profit = SystemSetting::where('config_key', 'profit')->first();

        foreach ($products as $product) {
            $latest = PurchaseOrderItems::where('product_id', $product->id)
                ->orderByDesc('created_at')
                ->first();

            if (!$latest || !$latest->unit_price) {
                $cost = $product->unit_cost ?? 0;
            } else {
                $cost = $latest->unit_price;
            }

            if ($cost > 0) {
                $profitAmount = $cost * ($profit->config_value / 100); //30%
                $taxAmount = $cost * ($tax->config_value / 100); //5%
                $sellingPrice = $cost + $profitAmount + $taxAmount;
                $product->unit_price = $sellingPrice;
                $product->save();
            }
        }

        return response()->json(['icon' => 'success', 'title' => 'System Updated', 'text' => 'System Setting has been updated'], Response::HTTP_OK);
    }
}
