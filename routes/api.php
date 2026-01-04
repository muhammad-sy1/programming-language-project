<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('check.approved:18');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


    // display all apartments
    Route::get('/apartments', [ApartmentController::class, 'index']);
    Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
    Route::get('/apartments/{apartment}/bookings', [ApartmentController::class, 'showApparBookings']);
    Route::post('/apartments', [ApartmentController::class, 'store']);




    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::post('/ratings/{booking}', [RatingController::class, 'store']);


    // إضافة/إزالة من المفضلة
    Route::post('/favorites/add', [FavoriteController::class, 'addToFavorites']);
    Route::post('/favorites/remove', [FavoriteController::class, 'removeFromFavorites']);
    //هاد مشان الزر تبع الاضافة والازالة بنفس الكبسة
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggleFavorite']);


    Route::get('/favorites', [FavoriteController::class, 'getUserFavorites']);
    Route::delete('/favorites/clear', [FavoriteController::class, 'clearAllFavorites']);

});
