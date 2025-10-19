<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validate = $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        if (!Auth::attempt($validate)) {
            return response()->json([
                'message' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Invalid Credentials'
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = Auth::user();

        if ($user->employee->status !== 'Active') {
            return response()->json([
                'message' => [
                    'icon' => 'error',
                    'title' => 'Error',
                    'text' => 'Account is not active'
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'Logged In'
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => [
                'icon' => 'success',
                'title' => 'Login Success',
                'text' => 'User authenticated successfully'
            ],
        ], Response::HTTP_OK);
    }

    public function getAuthUser(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'Logged Out'
        ]);

        return response()->json([
            'message' => [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'User logged out successfully'
            ],
        ], Response::HTTP_OK);
    }
}
