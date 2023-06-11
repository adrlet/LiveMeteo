<?php

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
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthenticatedSessionController::class, 'create']);

Route::group(['middleware' => ['guest']], function () {

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store']);

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create']);

    Route::post('reset-password', [NewPasswordController::class, 'store']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
    ->name('verify.email');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);

    Route::get('profile', [ProfileController::class, 'create']);

    Route::post('change-profile', [ChangeProfileController::class, 'store']);

    Route::post('change-password', [ChangePasswordController::class, 'store']);

    Route::post('change-email', [ChangeEmailController::class, 'store']);
});
