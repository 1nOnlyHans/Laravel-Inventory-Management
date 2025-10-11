<?php

namespace App\Http\Controllers;

use App\Events\StockOut;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleTransaction;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class StaffController extends Controller
{
    //

    public function finalizedSale(Request $request)
    {

        $validated = $request->validate([
            'customer_name' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
            'address' => ['required'],
            'payment_method' => ['required'],
            'amount_received' => ['required']
        ]);

        $total_price = 0;
        $reference_no = uniqid('SOLD-', false);


        foreach ($request->items as $item) {
            $id = Crypt::decryptString($item['product_id']);
            $product = Product::findOrFail($id);
            $newQuantity = $product->product_quantity - $item['quantity'];

            if ($newQuantity < 0) {
                return response()->json(['icon' => 'error', 'title' => 'Transaction Failed', 'text' => 'Insufficient stock for ' . $product->product_name], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Update status before saving
            if ($newQuantity <= 0) {
                $product->status = 'Out of Stock';
            } elseif ($newQuantity <= $product->reorder_level) {
                $product->status = 'Low Stock';
            }

            // Apply new quantity in one go
            $product->product_quantity = $newQuantity;
            $product->save();

            $total_price += floatval($item['total_price']);
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::user()->id,
                'reference_no' => $reference_no,
                'movement_type' => 'Stock Out',
                'quantity' => $item['quantity'],
                'reason' => 'Sold item'
            ]);
        }

        $customer = Customer::firstOrCreate([
            'registered_by' => Auth::user()->id,
            'name' => $validated['customer_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address']
        ]);

        $transaction = SaleTransaction::firstOrCreate([
            'customer_id' => $customer->id,
            'reference_no' => $reference_no,
            'amount_paid' => $validated['amount_received'],
            'total_amount' => $total_price,
            'notes' => $request->notes
        ]);

        return response()->json(['icon' => 'success', 'title' => 'Transaction Success', 'text' => 'Items Sold'], Response::HTTP_OK);
    }

    public function getSaleTransactions()
    {
        $transaction = SaleTransaction::with(['customer'])->latest()->get();
        return response()->json($transaction, Response::HTTP_OK);
    }
}
