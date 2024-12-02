<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
      if (Auth::guard('sanctum')->check()) {
        $user = Auth::guard('sanctum')->user();

        // Kullanıcı admin mi?
        if ($user->email=="prestgg56@gmail.com") {
            return $next($request);
        }

        // Yetkisiz kullanıcı
        return response()->json([
            'status' => false,
            'message' => 'Bu işlemi gerçekleştirmek için yetkiniz yok.',
        ], 403); // Forbidden
    }

    // Giriş yapılmamışsa
    return response()->json([
        'status' => false,
        'message' => 'Lütfen giriş yapın.',
    ], 401); // Unauthorized
    }
}
