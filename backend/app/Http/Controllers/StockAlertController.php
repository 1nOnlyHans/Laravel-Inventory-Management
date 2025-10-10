<?php

namespace App\Http\Controllers;

use App\Models\StockAlert;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StockAlertController extends Controller
{
    //
    public function index()
    {
        //Only Fetch Products that is not deleted or softdeleted
        $alerts = StockAlert::whereHas('product')->with(['product'])->latest()->get();
        return response()->json($alerts, Response::HTTP_OK);
    }
}
