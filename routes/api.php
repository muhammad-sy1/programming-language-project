<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('IsApproved');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


    // عرض الشقق مع الفلاتر
    Route::get('/apartments', [ApartmentController::class, 'index']);
    Route::get('/apartments/{id}', [ApartmentController::class, 'show']);
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
    /////////////
    Route::middleware('auth:api')->group(function () {
    
    // إدارة المحادثات
    Route::post('/conversations/start', [ChatController::class, 'startConversation']);
    Route::get('/conversations', [ChatController::class, 'getUserConversations']);
    Route::get('/conversations/unread-count', [ChatController::class, 'getUnreadCount']);
    Route::get('/conversations/find/{apartmentId}', [ChatController::class, 'findConversation']);
    Route::get('/conversations/{conversation}', [ChatController::class, 'getConversation']);
    Route::put('/conversations/{conversation}/read', [ChatController::class, 'markConversationAsRead']);
    Route::delete('/conversations/{conversation}', [ChatController::class, 'deleteConversation']);
    Route::get('/conversations/latest/messages', [ChatController::class, 'getLatestMessages']);
    
    // الرسائل
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);
    
});



});
