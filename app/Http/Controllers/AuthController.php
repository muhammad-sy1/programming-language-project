<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'birth_date' => 'required',
            'phone' => 'required|unique:users,phone|string',
            'id_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'personal_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $idPhotoPath = null;
        $personalPhotoPath = null;

        if ($request->hasFile('id_photo')) {
            $idPhoto = $request->file('id_photo');
            $idPhotoName = 'id_photo_'.Str::random(20).'.'.$idPhoto->getClientOriginalExtension();
            $idPhotoPath = $idPhoto->storeAs('users/id_photos', $idPhotoName, 'public');
        }

        if ($request->hasFile('personal_photo')) {
            $personalPhoto = $request->file('personal_photo');
            $personalPhotoName = 'personal_photo_'.Str::random(20).'.'.$personalPhoto->getClientOriginalExtension();
            $personalPhotoPath = $personalPhoto->storeAs('users/personal_photos', $personalPhotoName, 'public');
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birth_date' => $request->birth_date,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'id_photo_path' => $idPhotoPath,
            'personal_photo_path' => $personalPhotoPath,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User Registered Successfully, now your request is waiting for the admin to approve',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->firstOrFail();

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid phone or password',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User login successfully',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successfully',
        ], 200);
    }
}
