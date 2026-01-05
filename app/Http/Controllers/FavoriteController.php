<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class FavoriteController extends Controller
{

    public function addToFavorites(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id'
        ]);

        $userId = auth('sanctum')->id();
        $apartmentId = $request->apartment_id;

        $existingFavorite = Favorite::where('user_id', $userId)
            ->where('apartment_id', $apartmentId)
            ->first();

        if ($existingFavorite) {
            return response()->json([
                'success' => false,
                'message' => 'هذه الشقة موجودة بالفعل في المفضلة',
                'is_favorite' => true
            ], 400);
        }

        $favorite = Favorite::create([
            'user_id' => $userId,
            'apartment_id' => $apartmentId
        ]);

         return success(
                [  'favorites_count' => Favorite::countUserFavorites($userId)],
                HttpResponse::HTTP_CREATED 
            );
    }


    public function removeFromFavorites(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id'
        ]);

        $userId = Auth::id();
        $apartmentId = $request->apartment_id;

        $deleted = Favorite::where('user_id', $userId)
            ->where('apartment_id', $apartmentId)
            ->delete();

        if ($deleted) {
            return success(
                [  'favorites_count' => Favorite::countUserFavorites($userId)]
            );
        }

        return response()->json([
            'success' => false,
            'message' => 'الشقة غير موجودة في المفضلة',
            'is_favorite' => false
        ], 404);
    }


    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id'
        ]);

        $userId = Auth::id();
        $apartmentId = $request->apartment_id;

        $existingFavorite = Favorite::where('user_id', $userId)
            ->where('apartment_id', $apartmentId)
            ->first();

        if ($existingFavorite) {
            $existingFavorite->delete();
            $isFavorite = false;
            $message = 'تم إزالة الشقة من المفضلة';
        } else {
            Favorite::create([
                'user_id' => $userId,
                'apartment_id' => $apartmentId
            ]);
            $isFavorite = true;
            $message = 'تم إضافة الشقة إلى المفضلة';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorite' => $isFavorite,
            'favorites_count' => Favorite::countUserFavorites($userId)
        ]);
    }


    public function getUserFavorites()
    {
        $userId = Auth::id();
        $favorites = Favorite::with(['apartment' => function($query) {
            $query->select('id', 'title', 'description', 'price', 'location', 'images');
        }])
        ->where('user_id', $userId)
        ->latest()
        ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => [
                'favorites' => $favorites->items(),
                'total' => $favorites->total(),
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage()
            ]
        ]);
    }

    public function clearAllFavorites()
    {
        $userId = Auth::id();
        $deleted = Favorite::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف جميع الشقق من المفضلة',
            'deleted_count' => $deleted
        ]);
    }
}
