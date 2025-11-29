<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\HotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log; 


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/debug-photos', function (Request $request) {
    Log::info('Debug photos endpoint called'); 
    
    try {
        $debugInfo = [
            'has_id_photo' => $request->hasFile('id_photo'),
            'has_personal_photo' => $request->hasFile('personal_photo'),
            'all_request_data' => $request->except(['password']), 
        ];

        if ($request->hasFile('id_photo')) {
            $idPhoto = $request->file('id_photo');
            $debugInfo['id_photo'] = [
                'original_name' => $idPhoto->getClientOriginalName(),
                'extension' => $idPhoto->getClientOriginalExtension(),
                'size' => $idPhoto->getSize(),
                'mime_type' => $idPhoto->getMimeType(),
                'is_valid' => $idPhoto->isValid(),
            ];
        }

        if ($request->hasFile('personal_photo')) {
            $personalPhoto = $request->file('personal_photo');
            $debugInfo['personal_photo'] = [
                'original_name' => $personalPhoto->getClientOriginalName(),
                'extension' => $personalPhoto->getClientOriginalExtension(),
                'size' => $personalPhoto->getSize(),
                'mime_type' => $personalPhoto->getMimeType(),
                'is_valid' => $personalPhoto->isValid(),
            ];
        }

        Log::info('Photo debug info', $debugInfo);

        return response()->json([
            'success' => true,
            'debug_info' => $debugInfo
        ]);

    } catch (\Exception $e) {
        Log::error('Debug photos failed', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
