<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckStatusMiddleware
{
    public function handle(Request $request, Closure $next, $extras = [])
    {

        $user = User::where('phone', $request->phone)->firstOrFail();

        // تحقق من حالة المستخدم
        switch ($user->status) {
            case 'pending':
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is pending approval. Please wait for admin approval.',
                ], 403);

            case 'rejected':
                return response()->json([
                    'success' => false,
                    'message' => 'Your account has been rejected. Contact administrator for more information.',
                ], 403);

            case 'approved':
                return $next($request);

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Your account status is invalid.',
                ], 403);
        }

    }
}
