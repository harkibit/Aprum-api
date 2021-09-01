<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

use App\Models\User;
class AuthController extends Controller
{
    //

    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'email' => 'email|required',
            'password' => 'string|required|min:8'
        ]);

        if(!$token = Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'message' => 'Login failed'
            ], 401);
        }
        return $this->respondWithToken($token);
    }

    public function register(Request $request): JsonResponse
    {
        $this->validate($request, [
            'username' => 'string|required|min:4',
            'email' => 'email|required|unique:users',
            'password' => 'string|required|min:8'
        ]);

        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
}
