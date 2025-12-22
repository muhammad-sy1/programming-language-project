<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, $bookingId)
    {
        $user = $request->user(); 

        $booking = Booking::with('apartment')->findOrFail($bookingId);

        if ($booking->apartment->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $rating = Rating::create([
            'booking_id' => $bookingId,
            'owner_id'   => $user->id,
            'rating'     => $data['rating'],
            'comment'    => $data['comment'] ?? null,
        ]);

        return response()->json($rating, 201);
    }
}
