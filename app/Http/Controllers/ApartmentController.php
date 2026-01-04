<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (! $request->anyFilled(['governorate', 'city', 'price_min', 'price_max'])) {
            $apartments = Apartment::all();

            return response()->json([
                'success' => true,
                'data' => $apartments,
                'count' => $apartments->count(),
                'message' => 'All apartments loaded (no filters applied)',
            ]);
        }

        try {
            $query = Apartment::query();

            if ($request->filled('governorate')) {
                $query->where('governorate', $request->governorate);
            }

            if ($request->filled('city')) {
                $query->where('city', $request->city);
            }

            if ($request->filled('price_min')) {
                $query->where('price', '>=', (float) $request->price_min);
            }

            if ($request->filled('price_max')) {
                $query->where('price', '<=', (float) $request->price_max);
            }

            $apartments = $query->get();

            return response()->json([
                'success' => true,
                'data' => $apartments,
                'count' => $apartments->count(),
                'filters_applied' => $request->only(['governorate', 'city', 'price_min', 'price_max']),
            ]);

        } catch (\Exception $e) {
            Log::error('Apartment filter error: '.$e->getMessage());

            $apartments = Apartment::all();

            return response()->json([
                'success' => false,
                'data' => $apartments,
                'message' => 'Filter failed, showing all apartments',
                'error' => $e->getMessage(),
                'count' => $apartments->count(),
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'governorate' => 'required|string',
            'city' => 'required|string',
            'price' => 'required|integer|min:1',
            'apartment_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photo = $request->file('apartment_photo');
        $photoName = 'apartment_'.time().'.'.$photo->getClientOriginalExtension();
        $photo_path = $photo->storeAs('apartments', $photoName, 'public');

        $apartment = Apartment::create([
            'description' => $validated['description'],
            'governorate' => $validated['governorate'],
            'city' => $validated['city'],
            'price' => $validated['price'],
            'photo_path' => $photo_path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Apartment added successfully',
            'data' => $apartment,
            'photo_url' => asset('storage/'.$photo_path),
        ], 201);
    }

    public function filter(Request $request)
    {
        try {
            $query = Apartment::all();

            if ($request->filled('governorate')) {
                $query->where('governorate', 'like', '%'.$request->governorate.'%');
            }

            if ($request->filled('city')) {
                $query->where('city', 'like', '%'.$request->city.'%');
            }

            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            if ($request->filled('bedrooms')) {
                $query->where('bedrooms', $request->bedrooms);
            }

            if ($request->filled('bathrooms')) {
                $query->where('bathrooms', $request->bathrooms);
            }

            $perPage = $request->per_page ?? 20;
            $apartments = $query->with(['owner:id,first_name,last_name,phone'])
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Apartments retrieved successfully',
                'data' => $apartments->items(),
                'meta' => [
                    'current_page' => $apartments->currentPage(),
                    'per_page' => $apartments->perPage(),
                    'total' => $apartments->total(),
                    'last_page' => $apartments->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to filter apartments',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $apartment = Apartment::findOrFail($id);

        return response()->json($apartment, 200);

    }

    public function showApparBookings(Apartment $apartment)
    {

        // return response()->json(['data' => ['bookings' => $apartment->bookings]], 200);
        return success(['bookings' => $apartment->bookings]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $apartment = Apartment::findOrFail($id);
        $apartment->update($request->only('governorate', 'city', 'price', 'title'));

        return response()->json($apartment, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $apartment = Apartment::findOrFail($id);
        $apartment->delete();

        return response()->json('deleted', 204);

    }
}
