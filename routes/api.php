<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DreamController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VoiceNoteController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::group(['controller'=>AuthController::class],function(){
        Route::post('register',  'register');
        Route::post('verify-otp',  'verifyOtp');
        Route::post('resend-otp',  'resendOtp');
        Route::post('login',  'login');
        Route::post('employee-login',  'EmployeeLogin');
        Route::middleware(['auth:sanctum','user'])->group(function () {
            Route::get('profile','getProfile');
            Route::put('profile-update','updateProfile');
            Route::put('password-reset', 'resetPassword');
            Route::put('create-password', 'createPassword');
            Route::post('check-token','checkToken');
        });
    });
});
Route::prefix('user')->group(function () {
     Route::group(['controller' => UserController::class], function () {
         Route::middleware(['auth:sanctum', 'user'])->group(function () {
            Route::get('my-profile',  'myProfile');
            Route::post('support-request',  'supportRequest');
            Route::post('notification-status',  'notificationStatus');
        });
    });
});
Route::prefix('subscription')->group(function () {
     Route::group(['controller' => SubscriptionController::class], function () {
         Route::middleware(['auth:sanctum', 'user'])->group(function () {
            Route::get('plans',  'plans')->name('plans');
            Route::get('checkout/{plan}',  'checkout');
            Route::get('payment-intent/{plan}',  'paymentIntent');
            Route::get('success/{plan}',  'success');
            Route::post('cancel/{plan}',  'cancelSubscription');
            Route::post('resume/{plan}',  'resumeSubscription');
        });
    });
});
Route::prefix('dream')->group(function () {
    Route::group(['controller' => DreamController::class], function () {
        Route::middleware(['auth:sanctum', 'user'])->group(function () {
            Route::get('index', 'index');
            Route::post('store', 'store');
            Route::get('view/{id}', 'view');
            Route::post('check-in/{id}', 'checkIn');
            Route::get('dream-progress', 'dreamProgress');
            Route::get('productivity-boost', 'productivityBoost');
        });
    });
});
Route::prefix('voice-note')->group(function () {
    Route::group(['controller' => VoiceNoteController::class], function () {
        Route::middleware(['auth:sanctum', 'user'])->group(function () {
            Route::get('index', 'index');
            Route::post('store', 'store');
            Route::get('view/{id}', 'view');
        });
    });
});
Route::prefix('post')->group(function () {
    Route::group(['controller' => PostController::class], function () {
        Route::middleware(['auth:sanctum', 'user'])->group(function () {
            Route::get('index', 'index');
            Route::post('store', 'store');
            Route::post('like/{post_id}', 'like');
            Route::post('comment/{post_id}', 'comment');
            Route::post('reply/{post_id}/{comment_id}', 'reply');
            // Route::post('view/{post_id}', 'view');
            // Route::post('update/{post_id}', 'update');
            // Route::post('delete/{post_id}', 'delete');
            Route::get('search-topics', 'searchTopices');
            Route::get('sWeekly-highlight', 'weeklyHghlight');
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
Route::prefix('category')->group(function () {
    Route::group(['controller' => CategoryController::class], function () {
        Route::middleware(['auth:sanctum', 'admin'])->group(function () {
            Route::get('index', 'index');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::get('view/{id}', 'view');
            Route::delete('delete/{id}', 'destroy');
        });
    });
});
