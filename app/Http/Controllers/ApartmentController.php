<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use function Pest\Laravel\json;

class ApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apartment = Apartment::all();
        return response()->json($apartment,200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $apartment = $request->validate([
            'description'=>"string",
            'governorate'=>'required',
            'city'=>'required|string',
            'price'=>'required|integer'

       ]);
       Apartment::create($apartment,201);
       return response()->json($apartment,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $apartment = Apartment::findOrFail($id);
        return response()->json($apartment,200);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $apartment = Apartment::findOrFail($id);
         $apartment->update ($request->only('governorate','city','price','description'));
        
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


