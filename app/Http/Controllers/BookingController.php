<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after:start_date',
        ]);

        // منع التعارض
        $conflict = Booking::where('apartment_id', $data['apartment_id'])
            ->where('status', 'active')
            ->where(function ($q) use ($data) {
                $q->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                  ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']]);
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Apartment already booked in this period'
            ], 409);
        }

        $booking = Booking::create([
            'user_id'      => $user->id,
            'apartment_id' => $data['apartment_id'],
            'start_date'   => $data['start_date'],
            'end_date'     => $data['end_date'],
        ]);

        return response()->json($booking, 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        $booking = Booking::where('user_id', $user->id)->findOrFail($id);

        $booking->update(
            $request->only('start_date', 'end_date')
        );

        return response()->json($booking, 200);
    }

    public function cancel(Request $request, $id)
    {
        $user = $request->user();

        $booking = Booking::where('user_id', $user->id)->findOrFail($id);

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Booking cancelled'
        ], 200);
    }

    public function myBookings(Request $request)
    {
        $user = $request->user();

        return response()->json(
            Booking::where('user_id', $user->id)
                ->with('apartment')
                ->get(),
            200
        );
    }
}
