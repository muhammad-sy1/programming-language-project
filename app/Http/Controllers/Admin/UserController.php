<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        $pendingCount = User::where('status', 'pending')->count();

        return view('admin.users.index', compact('users', 'pendingCount'));
    }

    public function pendingUsers()
    {
        $pendingUsers = User::where('status', 'pending')->get();
        $pendingCount = $pendingUsers->count();

        return view('admin.users.pending', compact('pendingUsers', 'pendingCount'));
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function approve(User $user)
    {
        $user->status = 'approved';
        $user->status_updated_at = now();

        $saved = $user->save();

        if ($saved) {
            return back()->with('success', 'تمت الموافقة على المستخدم بنجاح');
        } else {
            return back()->with('error', 'فشل في تحديث حالة المستخدم');
        }
    }

    public function reject(User $user)
    {
        $user->status = 'rejected';
        $user->status_updated_at = now();

        $saved = $user->save();

        return back()->with('success', 'تم رفض المستخدم بنجاح');
    }

    public function destroy(User $user)
    {
        $this->deleteUserFiles($user);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * حذف ملفات المستخدم من التخزين
     */
    private function deleteUserFiles(User $user)
    {
        if ($user->personal_photo_path) {
            $path = str_replace('storage/', '', $user->personal_photo_path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        if ($user->id_photo_path) {
            $path = str_replace('storage/', '', $user->id_photo_path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

    }

    public function checkImage(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:personal,id',
        ]);

        $exists = file_exists($request->path);

        Log::info('Image check result:', [
            'path' => $request->path,
            'exists' => $exists,
            'user_id' => $request->user_id,
            'type' => $request->type,
        ]);

        return response()->json([
            'exists' => $exists,
            'path' => $request->path,
        ]);
    }

    public function getProfile($id)
    {
        User::find($id)->delete();
    }
}
