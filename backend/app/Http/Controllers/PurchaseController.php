<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseOrder;
use App\Mail\TestMailer;
use App\Models\Purchase;
use App\Models\PurchaseOrderItems;
use App\Models\PurchasePaymentRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    //
    public function index()
    {
        $pos = Purchase::with(['supplier', 'items'])->latest()->get();

        foreach ($pos as $po) {
            $po->makeHidden(['id']);
            $po->makeHidden(['supplier_id']);
            $po->supplier->makeHidden(['id']);
            $po->supplier->supplier_encrypted_id = Crypt::encryptString($po->supplier->id);
            $po->encrypted_id = Crypt::encryptString($po->id);

            foreach ($po->items as $item) {
                $item->makeHidden(['id']);
                $item->makeHidden(['product_id']);
                $item->makeHidden(['purchase_order_id']);
                $item->encrypted_id = Crypt::encryptString($item->id);
                $item->product = $item->product;
            }
        }
        return response()->json($pos, Response::HTTP_OK);
    }

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
        return response()->json(['icon' => 'success', 'title' => 'Ordered Successfully', 'text' => 'Your order has been sent to their email'], Response::HTTP_OK);
    }

    public function show(Request $request)
    {
        $id = Crypt::decryptString($request->route('purchase_id'));
        $PO = Purchase::with(['supplier', 'items'])->findOrFail($id);
        return response()->json($PO, Response::HTTP_OK);
    }

    public function acceptOrder(Purchase $purchase)
    {
        $purchase->status = 'Approved';
        $purchase->save();
        return response()->view('mail.accept', compact('purchase'));
    }

    public function createPaymentRecord(Request $request)
    {
        $validated = $request->validate(['purchase_id' => ['required'], 'payment_method' => ['required'], 'amount_paid' => ['required'], 'total_amount' => ['required']]);
        $reference_no = uniqid('PAY - ', false);
        $payment_record = PurchasePaymentRecord::create([
            'purchase_id' => $validated['purchase_id'],
            'reference_no' => $reference_no,
            'payment_method' => ucfirst($validated['payment_method']),
            'amount_paid' => $validated['amount_paid'],
            'total_amount' => $validated['total_amount']
        ]);

        return response()->json($payment_record, 200);
    }

    public function updateStatus(Request $request)
    {
        $po = Purchase::findOrFail($request->purchase_id);
        $po->payment_status = 'Paid';
        $po->save();
        return response()->json(['success'], Response::HTTP_OK);
    }
}
