<?php

namespace App\Http\Controllers;

use App\Models\PaymongoSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class PaymongoController extends Controller
{
    //
    protected $secretKey;
    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key');
    }

    public function createPaymentIntent(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $amount = $request->input('amount'); // e.g. ₱100
        $contact = $request->input('contact');
        $currency = 'PHP';
        $items = $request->items;
        $line_items = [];
        foreach ($items as $item) {
            $line_items[] = [
                'name' => $item['product']['product_name'],
                'amount' => $item['unit_price'] * 100,
                'currency' => $currency,
                'quantity' => $item['quantity']
            ];
        }

        $response = Http::withBasicAuth($this->secretKey, '')
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => $line_items,
                        'payment_method_types' => ['gcash', 'paymaya', 'card'],
                        "metadata" => [
                            "customer_name" => $name,
                            "customer_email" => $email
                        ],
                        "billing" => [
                            "name" => $name,
                            "email" => $email,
                            "phone" => $contact
                        ],
                        "success_url" => "http://localhost:5173/payment/success",
                        "cancel_url" => "http://localhost:5173/payment/failed"
                    ],
                ]
            ]);

        return response()->json($response->json(), 200);
    }

    public function storeSession(Request $request)
    {

        $validate = $request->validate([
            'purchase_id' => ['required'],
            'session_id' => ['required']
        ]);

        $purchase_id = Crypt::decryptString($validate['purchase_id']);

        $session = PaymongoSession::create([
            'purchase_id' => $purchase_id,
            'session_id' => $validate['session_id']
        ]);

        return response()->json(['icon' => 'success', 'title' => 'session stored', 'text' => 'checkout session stored'], Response::HTTP_OK);
    }

    public function getLatestSession()
    {
        $session = PaymongoSession::latest()->limit(1)->get();

        return response($session, Response::HTTP_OK);
    }

    public function checkTransactionStatus(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
            'Content-Type' => 'application/json',
        ])->get("https://api.paymongo.com/v1/checkout_sessions/{$request->session_id}");

        return response()->json($response->json());
    }
}
