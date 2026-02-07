<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiggleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (Auth::guard('admin')->check()) {
            $request->merge(['role' => 'admins']);
            $user = Auth::guard('admin')->user();
        } else {
            $request->merge(['role' => 'users']);
            $user = Auth::guard('web')->user();
        }

        if ($user && $user->role !== $role) {
            return redirect('/'); // ロールが一致しない場合はホームにリダイレクト
        }

        return $next($request);
    }
}