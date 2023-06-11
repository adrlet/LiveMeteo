<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if($valid->fails())
            return response()->json(['errors' => $valid->errors()]);

        $status = Password::sendResetLink( $request->only('email'));

        return $status == Password::RESET_LINK_SENT 
        ? response()->json('True') 
        : response()->json(['errors' => ['email' => [0 => __($status)]]]);
    }
}
