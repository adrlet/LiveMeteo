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
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        if($user->avatar == null)
            $user->avatar = 'avatars/default.jpg';
        else
            $user->avatar = 'avatars/'.$user->avatar;
        unset($user['created_at'], $user['updated_at'], $user['deleted_at']);
        
        return response()->json($user);
    }
}
