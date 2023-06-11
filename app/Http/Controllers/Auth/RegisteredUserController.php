<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:32', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'name' => ['nullable', 'string', 'max:32'],
            'avatar' => ['nullable', 'image', 'dimensions:min_width=100,min_height=100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        if($valid->fails())
            return response()->json(['errors' => $valid->errors()]);

        if($request->hasFile('avatar'))
        {
            $avatarName = $request->login.time().'.jpeg';
            $avatarContent = Image::make($request->file('avatar'))->encode('jpeg', 90)->resize(500, 500);
            Storage::disk('avatars')->put($avatarName, $avatarContent);
        }
        else
            $avatarName = null;

        $user = User::create([
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'avatar' => $avatarName,
            'email' => $request->email,
            'email_updated_at' => Carbon::now()->addWeek()->format('Y-m-d H:i:s'),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $token = Auth::user()->createToken('authToken')->plainTextToken;

        return response()->json(['True', 'authToken' => $token]);
    }
}
