<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    protected function unauthenticated($request, array $guards) 
    {
        abort(response()->json([ 'errors' => ['login' => [0 => trans('auth.unauthorized')]]]), 401);
    }
}
