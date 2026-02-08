<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminTodoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Todo;
use App\Models\User;

class AdminTodoController extends Controller
{
    // 管理者がユーザー一覧を取得する
    public function showUsers()
    {
        // ユーザー一覧を取得
        $users = User::all();
        $admin = auth()->guard('admin')->user();

        // ユーザー一覧を返す
        return view('admins.member', [
            'users' => $users,
            'admin' => $admin,
        ]);
    }

    // 管理者が特定ユーザーのTodo一覧を取得する
    public function index($id)
    {
        $user = User::findOrFail($id);

        // 指定されたユーザーIDのTodo一覧を取得
        $todos = $user->todos()->get();

        $admin = Auth::guard('admin')->user();

        // Todo一覧を返す
        return view('admins.show', compact('todos', 'user', 'admin'));
    }

    // 管理者が特定のTodoにTipsを追加する
    public function createTips(AdminTodoRequest $request, $id)
    {
        // 指定されたIDのTodoを取得
        $todo = Todo::findOrFail($id);

        // Tipsを追加
        $todo->tips = $request->input('tips');
        $todo->save();

        // 更新したTodoを返す
        return redirect()->route('admins.show', $todo->user_id)->with('message', 'Tipsが追加されました');
    }
}