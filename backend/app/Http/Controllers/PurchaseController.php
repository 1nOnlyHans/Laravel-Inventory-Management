<?php

namespace App\Http\Controllers;

use App\Events\SupplierOrderAccepted;
use App\Mail\PurchaseOrder;
use App\Models\Purchase;
use App\Models\PurchaseOrderItems;
use App\Models\PurchasePaymentRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class PurchaseController extends Controller
{
    //
    public function index()
    {
        $pos = Purchase::with(['supplier', 'items', 'paymentRecord'])->latest()->get();

        foreach ($pos as $po) {
            $po->makeHidden(['id']);
            $po->makeHidden(['supplier_id']);
            $po->supplier->makeHidden(['id']);
            $po->supplier->supplier_encrypted_id = Hashids::encode($po->supplier->id);
            $po->encrypted_id = Hashids::encode($po->id);

            foreach ($po->items as $item) {
                $item->makeHidden(['id']);
                $item->makeHidden(['product_id']);
                $item->makeHidden(['purchase_order_id']);
                $item->encrypted_id = Hashids::encode($item->id);
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
        $reference_no = 'PO-' . date('Y') . '-' . bin2hex(random_bytes(8));
        $items = $request->items;

        //Checks if there's an order items
        if (count($items) <= 0) {
            return response()->json(['icon' => 'error', 'title' => 'Order Failed', 'text' => 'Please select an item first'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //Insert PO
        $PO = Purchase::create([
            'supplier_id' => Hashids::decode($validated['supplier_id'])[0],
            'reference_no' => $reference_no,
            'order_date' => $validated['order_date'],
            'expected_date' => $validated['expected_date']
        ]);

        //Insert Items
        foreach ($items as $item) {
            PurchaseOrderItems::create([
                'purchase_order_id' => $PO->id,
                'product_id' => Hashids::decode($item['product_id'])[0],
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
        $id = Hashids::decode($request->route('purchase_id'));
        $PO = Purchase::with(['supplier', 'items'])->findOrFail($id[0]);
        return response()->json($PO, Response::HTTP_OK);
    }

    public function acceptOrder(Purchase $purchase)
    {
        $purchase->status = 'Approved';
        $purchase->save();

        broadcast(new SupplierOrderAccepted($purchase))->toOthers();

        return response()->view('mail.accept', compact('purchase'));
    }

    public function createPaymentRecord(Request $request)
    {
        $validated = $request->validate(['purchase_id' => ['required'], 'payment_method' => ['required'], 'amount_paid' => ['required'], 'total_amount' => ['required']]);

        $id = null;
        $purchase_id = null;
        $reference_no = null;

        if ($request->order_id && $request->order_id !== '') {
            $id = Hashids::decode($request->order_id)[0];
        } else {
            $id = $request->purchase_id;
        }

        $purchase_id = $id;
        $payment_record = DB::table('purchase_payment_records')->where('purchase_id', $id)->get()->count();
        if ($payment_record <= 0) {
            if ($validated['payment_method'] === 'Card') {
                $request->validate(['reference_no' => ['required']]);
                $reference_no = $request->reference_no;
            } else {
                $reference_no = uniqid('PAY - ', false);
            }
            $newPaymentRecord = PurchasePaymentRecord::firstOrCreate([
                'purchase_id' => $purchase_id,
                'reference_no' => $reference_no,
                'payment_method' => ucfirst($validated['payment_method']),
                'amount_paid' => $validated['amount_paid'],
                'total_amount' => $validated['total_amount']
            ]);
        }

        return response()->json(['icon' => 'success', 'title' => 'Transaction Success', 'text' => 'Payment Record created successfully'], Response::HTTP_OK);
    }

    public function updateStatus(Request $request)
    {
        $id = Hashids::decode((string) $request->purchase_id);
        if (count($id) <= 0) {
            $id = $request->purchase_id;
        } else {
            $id = $id[0];
        }

        $po = Purchase::findOrFail($id);
        $po->payment_status = 'Paid';
        $po->save();
        return response()->json($id, Response::HTTP_OK);
    }

    public function markAsDelivered(Request $request)
    {
        $po = Purchase::findOrFail(Hashids::decode($request->purchase_id)[0]);
        $po->status = 'Delivered';
        $po->save();
        return response()->json(['icon' => 'success', 'title' => 'Updated Successfully', 'text' => 'Marked as Delivered!'], Response::HTTP_OK);
    }
}
