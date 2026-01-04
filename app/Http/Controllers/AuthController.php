<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        if ($request->hasFile('id_photo')) {
            $idPhotoPath = $request->file('id_photo')->storePublicly('/users/id_photos');
            // $idPhotoName = 'id_photo_' . $request->phone . '_' . time() . '.' . $idPhoto->extension();

            // // حفظ مباشرة في public/storage
            // $destinationPath = public_path('storage/users/id_photos');

            // $idPhoto->move($destinationPath, $idPhotoName);
            // $idPhotoPath = 'users/id_photos/' . $idPhotoName;
        }

        $personalPhotoPath = null;
        if ($request->hasFile('personal_photo')) {
            $personalPhoto = $request->file('personal_photo');
            $personalPhotoName = 'personal_photo_'.$request->phone.'_'.time().'.'.$personalPhoto->extension();

            // حفظ مباشرة في public/storage
            $destinationPath = public_path('storage/users/personal_photos');

            // إنشاء المجلد إذا لم يكن موجوداً
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // نقل الملف
            $personalPhoto->move($destinationPath, $personalPhotoName);
            $personalPhotoPath = 'users/personal_photos/'.$personalPhotoName;
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name.' '.$request->last_name,
            'birth_date' => $request->birth_date,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'id_photo_path' => $idPhotoPath,
            'personal_photo_path' => $personalPhotoPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم التسجيل بنجاح',
            'user' => $user,
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
