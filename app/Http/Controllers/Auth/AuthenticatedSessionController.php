<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create(Request $request)
    {
        if($request->user('sanctum'))
            return response()->json('True');
        else
            return response()->json('False');
    }


    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $error = $request->authenticate();
        if($error != NULL)
            return response()->json($error);

        $token = Auth::user()->createToken('authToken')->plainTextToken;

        return response()->json(['True', 'authToken' => $token]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json('True');
    }
}
