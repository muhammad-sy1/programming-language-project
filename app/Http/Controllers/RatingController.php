<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function store(Request $request, $bookingId)
    {
        $booking = Booking::with('apartment')->findOrFail($bookingId);

        if ($booking->apartment->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

            $rating = Rating::create(array_merge([
                'booking_id' => $bookingId,
                'owner_id' => auth()->id(),
            ], $data));

        return response()->json($rating, 201);
    }
}

