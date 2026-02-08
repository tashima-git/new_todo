<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // 管理者ルート
        if ($role === 'admins') {
            if (!Auth::guard('admin')->check()) {
                return redirect()->route('admins.login');
            }
            return $next($request);
        }

        // ユーザールート
        if ($role === 'users') {
            if (!Auth::guard('web')->check()) {
                return redirect()->route('login');
            }
            return $next($request);
        }

        // role指定ミス
        abort(403);
    }
}
