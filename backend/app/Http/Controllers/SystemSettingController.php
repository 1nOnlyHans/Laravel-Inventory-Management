<?php

namespace App\Http\Controllers;

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
        return response()->json(['icon' => 'success', 'title' => 'System Updated', 'text' => 'System Setting has been updated'], Response::HTTP_OK);
    }
}
