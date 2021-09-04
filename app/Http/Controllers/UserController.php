<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function update(Request $request): JsonResponse
    {
        $this->validate($request, [
            'username' => 'string|required|min:4',
        ]);

        auth()->user()->update([
            'username' => $request->input('username')
        ]);

        return response()->json([
            'message' => 'User updated successfully'
        ]);

    }

    public function snippets(): JsonResponse
    {
        return response()->json([
            'snippets' => auth()->user()->snippets()->with(['version', 'version.language'])->orderByDesc('id')->get()
        ]);
    }
}
