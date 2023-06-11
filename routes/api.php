<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\ChangeProfileController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ChangeEmailController;
use App\Http\Controllers\Auth\VerifyEmailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/// Auth
require __DIR__.'/auth.php';

/// Reports
require __DIR__.'/reports.php';

Route::get('/', function () {
    return response()->json(['errors' => [0 => [0 => trans('auth.unauthorized')]]]);
});

Route::fallback(function () {
    return response()->json(['errors' => [0 => [0 => trans('auth.unauthorized')]]]);
});

/*
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
*/