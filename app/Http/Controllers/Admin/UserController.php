<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * عرض جميع المستخدمين الذين ينتظرون الموافقة
     */
    public function pendingUsers()
    {
        $users = User::where('status', 'pending')
            ->with(['apartments' => function ($query) {
                $query->select('id', 'owner_id', 'title');
            }])
            ->get([
                'id',
                'first_name',
                'last_name',
                'phone',
                'email',
                'role',
                'status',
                'profile_image',
                'id_image',
                'created_at'
            ]);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * عرض تفاصيل مستخدم معين
     */
    public function show($id)
    {
        $user = User::with(['apartments' => function ($query) {
            $query->select('id', 'owner_id', 'title', 'governorate', 'city', 'price');
        }])
            ->findOrFail($id, [
                'id',
                'first_name',
                'last_name',
                'phone',
                'email',
                'birth_date',
                'role',
                'status',
                'profile_image',
                'id_image',
                'created_at'
            ]);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * الموافقة على مستخدم
     */
    public function approve($id)
    {
        $user = User::findOrFail($id);

        if ($user->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'User is not in pending status'
            ], 400);
        }

        $user->status = 'approved';
        $user->save();

        // هنا يمكنك إضافة إشعار للمستخدم

        return response()->json([
            'success' => true,
            'message' => 'User approved successfully',
            'data' => $user
        ]);
    }

    /**
     * رفض مستخدم
     */
    public function reject($id, Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string'
        ]);

        $user = User::findOrFail($id);

        if ($user->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'User is not in pending status'
            ], 400);
        }

        $user->status = 'rejected';
        $user->rejection_reason = $request->reason; // إذا أردت إضافة حقل للسبب
        $user->save();

        // هنا يمكنك إضافة إشعار للمستخدم مع سبب الرفض

        return response()->json([
            'success' => true,
            'message' => 'User rejected successfully',
            'data' => $user
        ]);
    }

    /**
     * عرض جميع المستخدمين (بجميع الحالات)
     */
    public function index()
    {
        $users = User::with(['apartments' => function ($query) {
            $query->select('id', 'owner_id', 'title');
        }])
            ->get([
                'id',
                'first_name',
                'last_name',
                'phone',
                'email',
                'role',
                'status',
                'created_at'
            ]);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * حذف مستخدم
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // لا يمكن حذف مدير آخر
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin user'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
