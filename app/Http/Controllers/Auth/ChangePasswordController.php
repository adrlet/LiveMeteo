<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'password' => ['required', 'string', 'current_password'],
            'newpassword' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if($valid->fails())
            return response()->json(['errors' => $valid->errors()]);

        Auth::user()->update([
            'password' => Hash::make($request->newpassword),
            'remember_token' => Str::random(60),
        ]);

        return response()->json('True');
    }
}
