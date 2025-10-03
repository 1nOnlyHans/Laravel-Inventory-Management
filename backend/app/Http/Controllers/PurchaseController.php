<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseOrder;
use App\Models\Purchase;
use App\Models\PurchaseOrderItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    //
    // public function mail(Request $request)
    // {
    //     Mail::to('carlo.mendoza@primehardwareimports.com')->send(new PurchaseOrder());
    //     return response()->json(['success' => true, 'message' => 'Email sent successfully!']);
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required'],
            'order_date' => ['required', 'date'],
            'expected_date' => ['required', 'date']
        ]);

        $items = $request->items;

        //Checks if there's an order items
        if (count($items) <= 0) {
            return response()->json(['icon' => 'error', 'title' => 'Order Failed', 'text' => 'Please select an item first'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Insert PO
        $PO = Purchase::create([
            'supplier_id' => Crypt::decryptString($validated['supplier_id']),
            'order_date' => $validated['order_date'],
            'expected_date' => $validated['expected_date']
        ]);

        //Insert Items
        foreach ($items as $item) {
            PurchaseOrderItems::create([
                'purchase_order_id' => $PO->id,
                'product_id' => Crypt::decryptString($item['product_id']),
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'total' => $item['unit_price'] * $item['quantity']
            ]);
        }
        
        //Make this a QUEUE 
        Mail::to($PO->supplier->email)->send(new PurchaseOrder($PO));
        return response()->json(['icon' => 'success', 'title' => 'Order Successfully', 'text' => 'Your new order has been generated'], Response::HTTP_OK);
    }
}
