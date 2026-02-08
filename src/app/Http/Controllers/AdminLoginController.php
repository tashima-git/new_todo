<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminLoginController extends Controller
{
    // 管理者ログインフォーム表示
    public function showLoginForm()
    {
        return view('auth.admin_login');
    }

    // 管理者ログイン処理
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            // 認証成功
            return redirect()->route('admins.member');
        }

        // 認証失敗
        return redirect()->back()->withErrors(['email' => '認証に失敗しました。']);
    }

    // 管理者ログアウト処理
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}