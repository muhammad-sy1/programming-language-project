<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//المصادقة
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');



Route::apiResource('/apartment', ApartmentController::class);

Route::middleware('IsAdmin')->group(function () {


    // إدارة المستخدمين
    Route::get('admin/users/pending', [UserController::class, 'pendingUsers']);
    Route::get('admin/users', [UserController::class, 'index']);
    Route::get('admin/users/{user}', [UserController::class, 'show']);
    Route::post('admin/users/{user}/approve', [UserController::class, 'approve']);
    Route::post('admin/users/{user}/reject', [UserController::class, 'reject']);
    Route::delete('admin/users/{user}', [UserController::class, 'destroy']);

    // إحصائيات (بكملها بعدين)
    //   Route::get('/stats', [DashboardController::class, 'stats']);

});
