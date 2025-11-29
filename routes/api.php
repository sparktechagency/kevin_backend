<?php

use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DreamController;
use App\Http\Controllers\Api\ManageUserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OpenAIContoller;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\TermConditionController;
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
            Route::post('logout','logout');
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
Route::prefix('coach')->group(function () {
     Route::group(['controller' => OpenAIContoller::class], function () {
         Route::middleware(['auth:sanctum', 'user'])->group(function () {
            Route::get('chat-history-view/{chat_id}',  'chatHistoryView');
            Route::get('chat-history', 'history');
            Route::get('index/{chat_id}',  'index');
            Route::post('store', 'store');
            Route::get('view/{chat_id}/{coach_id}','view');
        });
    });
});
Route::prefix('subscription')->group(function () {
     Route::group(['controller' => SubscriptionController::class], function () {
         Route::middleware(['auth:sanctum', 'user'])->group(function () {
            Route::get('plans','plans')->name('plans');
            Route::get('checkout/{plan}', 'checkout');
            Route::get('payment-intent/{plan}', 'paymentIntent');
            Route::get('success/{plan}', 'success');
            Route::post('cancel/{plan}', 'cancelSubscription');
            Route::post('resume/{plan}', 'resumeSubscription');
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
            Route::get('upcoming', 'upcoming');
            Route::get('dream-progress', 'dreamProgress');
            Route::get('productivity-boost', 'productivityBoost');
            Route::get('note/{dream_id}', 'note');
            Route::get('ai-feedback', 'aiFeedback');
            Route::get('smart-suggestion', 'smartSuggestion');
        });
    });
});
Route::prefix('voice-note')->group(function () {
    Route::group(['controller' => VoiceNoteController::class], function () {
        Route::middleware(['auth:sanctum', 'user'])->group(function () {
            Route::get('index', 'index');
            Route::post('store', 'store');
            Route::get('view/{id}', 'view');
            Route::get('voiceToText', 'voiceToText');
        });
    });
});
Route::prefix('post')->group(function () {
    Route::group(['controller' => PostController::class], function () {
        Route::middleware(['auth:sanctum', 'user'])->group(function () {
            Route::get('index', 'index');
            Route::get('single-post/{post_id}', 'singlePost');
            Route::post('store', 'store');
            Route::post('like/{post_id}', 'like');
            Route::post('comment/{post_id}', 'comment');
            Route::post('reply/{post_id}/{comment_id}', 'reply');
            Route::get('search-topics', 'searchTopices');
            Route::get('sWeekly-highlight', 'weeklyHghlight');
        });
    });
});
Route::prefix('notification')->group(function () {
    Route::group(['controller' => NotificationController::class], function () {
        Route::middleware(['auth:sanctum', 'user'])->group(function () {
            Route::get('index', 'index');
            Route::post('create', 'create');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'destroy');
            Route::get('get', 'getNotification');
            Route::post('read/{id}', 'markAsRead');
            Route::post('read-all', 'markAllAsRead');
        });
    });
});
//admin
Route::prefix('admin-dashboard')->group(function () {
    Route::group(['controller' => AdminDashboardController::class], function () {
        Route::middleware(['auth:sanctum', 'admin'])->group(function () {
            Route::get('index', 'index');
            Route::get('analytics', 'analytics');
            Route::get('get-report', 'getReport');
            Route::post('create-report', 'createReport');
            Route::put('update-report/{report_id}', 'updateReport');
            Route::delete('delete-report/{report_id}', 'deleteReport');
            Route::put('update-plan/{plan}', 'updatePlan');
            Route::get('roi', 'roi');
        });
    });
});
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
        Route::get('index', 'index')->middleware(['auth:sanctum', 'user']);
        Route::middleware(['auth:sanctum', 'admin'])->group(function () {
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::get('view/{id}', 'view');
            Route::delete('delete/{id}', 'destroy');
        });
    });
});
Route::prefix('term-condition')->group(function () {
    Route::group(['controller' => TermConditionController::class], function () {
        Route::get('index', 'index')->middleware(['auth:sanctum', 'user']);
        Route::middleware(['auth:sanctum', 'admin'])->group(function () {
            Route::post('store', 'store');
        });
    });
});
//Manager
Route::prefix('department')->group(function () {
    Route::group(['controller' => DepartmentController::class], function () {
        Route::middleware(['auth:sanctum', 'manager'])->group(function () {
            Route::get('index', 'index');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'destroy');
        });
    });
});
Route::prefix('manage-user')->group(function () {
    Route::group(['controller' => ManageUserController::class], function () {
        Route::middleware(['auth:sanctum', 'manager'])->group(function () {
            Route::get('index', 'index');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('delete/{id}', 'destroy');
        });
    });
});
