<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
   //المصادقة
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

Route::post('/login', [AuthController::class, 'login'])->middleware('IsApproved');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// في routes/api.php

// عرض الشقق مع الفلاتر
Route::get('/apartments', [ApartmentController::class, 'index']);

// عرض شقة محددة 
Route::get('/apartments/{id}', [ApartmentController::class, 'show']);

// إضافة شقة جديدة (
Route::post('/apartments', [ApartmentController::class, 'store']);
    

  

    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);

    Route::post('/ratings/{booking}', [RatingController::class, 'store']);
});
