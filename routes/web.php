<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

Route::get('/users', function () {
    return view('users.index');
})->name('users');


Auth::routes();
