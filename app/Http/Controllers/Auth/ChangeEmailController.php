<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ChangeEmailController extends Controller
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        if($valid->fails())
            return response()->json(['errors' => $valid->errors()]);

        $block_date = Carbon::createFromFormat('Y-m-d H:i:s', Auth::user()->email_updated_at);
        if(Carbon::now()->lt($block_date))
            return response()->json(
            ['errors' => 
                ['email' => 
                    [0 => trans('auth.email_block', ['date' => $block_date])],
                ]
            ]);

        Auth::user()->fill([
            'email' => $request->email,
            'email_verified_at' => Null,
            'email_updated_at' => Carbon::now()->addWeek()->format('Y-m-d H:i:s'),
        ])->save();

        Auth::user()->sendEmailVerificationNotification();

        return response()->json('True');
    }
}
