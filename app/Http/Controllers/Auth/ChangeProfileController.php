<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;   

class ChangeProfileController extends Controller
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
            'name' => ['nullable', 'string', 'max:32'],
            'avatar' => ['nullable', 'image', 'dimensions:min_width=100,min_height=100'],
            'password' => ['required', 'string', 'current_password'],
        ]);

        if($valid->fails())
            return response()->json(['errors' => $valid->errors()]);

        if($request->hasFile('avatar'))
        {
            $avatarName = Auth::user()->login.time().'.jpeg';
            $avatarContent = Image::make($request->file('avatar'))->encode('jpeg', 90)->resize(500, 500);
            Storage::disk('avatars')->put($avatarName, $avatarContent);
            Storage::disk('avatars')->delete(Auth::user()->avatar);
        }
        else
            $avatarName = Auth::user()->avatar;

        Auth::user()->fill([
            'name' => $request->name,
            'avatar' => $avatarName,
        ])->save();

        return response()->json('True');
    }
}
