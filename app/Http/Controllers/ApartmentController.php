<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    $query = Apartment::query();

    if ($request->filled('governorate')) {
        $query->where('governorate', $request->governorate);
    }

    if ($request->filled('city')) {
        $query->where('city', $request->city);
    }

    if ($request->filled('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    return response()->json($query->get(), 200);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $apartment = $request->validate([
            'description' => 'string',
            'governorate' => 'required|string',
            'city' => 'required|string',
            'price' => 'required|integer',

        ]);
        Apartment::create($apartment, 201);

        return response()->json($apartment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $apartment = Apartment::findOrFail($id);

        return response()->json($apartment, 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $apartment = Apartment::findOrFail($id);
        $apartment->update($request->only('governorate', 'city', 'price', 'description'));

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
