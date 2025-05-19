<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;

use App\Http\Controllers\API\UserAPIController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// User API Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserAPIController::class, 'index']);
    Route::post('/', [UserAPIController::class, 'store']);
    Route::get('/{user}', [UserAPIController::class, 'show']);
    Route::put('/{user}', [UserAPIController::class, 'update']);
    Route::delete('/{user}', [UserAPIController::class, 'destroy']);
    Route::put('/{user}/change-password', [UserAPIController::class, 'changePassword']);
});

// Authenticated User (optional)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Password Reset Routes
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [ResetPasswordController::class, 'reset']);
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Inline Password Reset (alternative custom version)
Route::post('/password/forgot', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink($request->only('email'));

    return response()->json(['status' => __($status)], 200);
});

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::put('/users/{id}/change-password', [UserController::class, 'changePassword']);

Route::post('/password/reset', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'token' => 'required',
        'password' => 'required|confirmed|min:8',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password),
            ])->save();

            event(new \Illuminate\Auth\Events\PasswordReset($user));
        }
    );

    return response()->json(['status' => __($status)], 200);
});
