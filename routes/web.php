<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PasswordResetController;

Route::get('/', function (){return view('welcome');});
// Route::get('/forgot-password', function (){return view('auth.forgot-password');})->middleware('guest')->name('password.request');
// Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
// Route::get('/reset-password/{token}', function (string $token){return view('auth.reset-password', ['token' => $token]);})->middleware('guest')->name('password.reset');
// Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('guest')->name('password.update');



