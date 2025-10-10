<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AuditLogController extends Controller
{
    //

    public function index()
    {
        $logs = AuditLog::whereDate('created_at', Carbon::today())->with(['user'])->latest()->get();
        return response()->json($logs, Response::HTTP_OK);
    }
}
