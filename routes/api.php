<?php

use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\NpmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VehicleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/user/{id}', [UserController::class, 'getUserById']);
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');
    Route::get('/check-npm/{npm}', [NpmController::class, 'checkNpm']);
});

    Route::prefix('vehicle')->group(function () {
    Route::post('/entry', [VehicleController::class, 'vehicleEntry']);
    Route::post('/exit', [VehicleController::class, 'vehicleExit']);
    Route::post('/status/check', [VehicleController::class, 'checkVehicleStatus']);
    Route::get('/list', [VehicleController::class, 'vehicleList']);
});
