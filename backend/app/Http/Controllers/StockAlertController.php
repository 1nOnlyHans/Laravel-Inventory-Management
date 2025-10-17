<?php

namespace App\Http\Controllers;

use App\Models\StockAlert;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Vinkla\Hashids\Facades\Hashids;

class StockAlertController extends Controller
{
    //
    public function index()
    {
        //Only Fetch Products that is not deleted or softdeleted
        $alerts = StockAlert::whereHas('product')->with(['product'])->latest()->get();
        foreach ($alerts as $alert) {
            $alert->product->makeHidden(['id']);
            $alert->product->encrypted_id = Hashids::encode($alert->product->id);
        }
        return response()->json($alerts, Response::HTTP_OK);
    }
}
