<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::group(['controller'=>AuthController::class],function(){
        Route::post('register',  'register');
        Route::post('verify-otp',  'verifyOtp');
        Route::post('resend-otp',  'resendOtp');
        Route::post('login',  'login');
        Route::middleware(['auth:sanctum','user'])->group(function () {
            Route::get('profile','getProfile');
            Route::put('profile-update','updateProfile');
            Route::put('password-reset', 'resetPassword');
            Route::post('check-token','checkToken');
        });
    });
});
//admin
Route::prefix('company')->group(function () {
    Route::group(['controller' => CompanyController::class], function () {
        Route::middleware(['auth:sanctum', 'admin'])->group(function () {
            Route::get('index', 'index');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::get('view/{id}', 'view');
            Route::delete('delete/{id}', 'destroy');
        });
    });
});
