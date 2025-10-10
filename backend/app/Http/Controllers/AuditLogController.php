<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogController extends Controller
{
    //

    public function index()
    {
        $logs = AuditLog::with(['user'])->latest()->get();
        return response()->json($logs, Response::HTTP_OK);
    }
}
