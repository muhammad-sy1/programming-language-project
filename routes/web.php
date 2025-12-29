<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});




//Route::middleware('IsAdmin')->group(function () {
        
    Route::get('admin/users/pending', [UserController::class, 'pendingUsers'])->name('admin.users.pending');
    Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
    Route::post('admin/users/{user}/approve', [UserController::class, 'approve'])->name('admin.users.approve');
    Route::post('admin/users/{user}/reject', [UserController::class, 'reject'])->name('admin.users.reject');
    Route::delete('admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
    Route::delete('admin/users/checkImage', [UserController::class, 'checkImage'])->name('admin.users.check.image');

// }
