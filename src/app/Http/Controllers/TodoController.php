<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Http\Requests\TodoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TodoController extends Controller
{
    // Todo一覧を取得する
    public function index()
    {
        // user情報を取得
        $user = auth()->user();

        // userに紐づくTodo一覧を取得
        $todos = $user
        ? Todo::where('user_id', $user->id)->get()
        : collect();

        // Todo一覧を返す
        return view('index', compact('todos'));
    }


    // Todoを作る
    public function create()
    {
        // user情報を取得
        $user = auth()->user();

        // Todoを作成
        $todo = Todo::create([
            'user_id' => $user->id,
            'content' => request('content'),
            'is_completed' => false,
        ]);

        // 作成したTodoを返す
        return redirect()->route('users.todos')->with('message', 'Todoが作成されました');
    }


    // Todoを更新する
    public function update($id)
    {
        // user情報を取得
        $user = auth()->user();
        // Tips作成時は管理者情報を取得
        $admin = auth()->guard('admin')->user();


        // 指定されたIDのTodoを取得
        $todo = $user->todos()->findOrFail($id);

        // Todoを更新
        $todo->update([
            'content' => '更新されたTodo',
            'is_completed' => true,
        ]);

        // Tipsを追加（管理者のみ）
        if ($admin) {
            $todo->tips = 'これは管理者が追加したTipsです。';
            $todo->save();
        }

        // 更新したTodoを返す
        return redirect()->route('users.todos')->with('message', 'Todoが更新されました');
    }



    // Todoを削除する
    public function delete($id)
    {
        // user情報を取得
        $user = auth()->user();

        // 指定されたIDのTodoを取得
        $todo = $user->todos()->findOrFail($id);

        // Todoを削除
        $todo->delete();

        // 成功レスポンスを返す
        return redirect()->route('users.todos')->with('message', 'Todoが削除されました');
    }

    // Todoの完了状態を切り替える
    public function completed($id)
    {
        // user情報を取得
        $user = auth()->user();

        // 指定されたIDのTodoを取得
        $todo = $user->todos()->findOrFail($id);

        // 完了状態を切り替え
        $todo->is_completed = !$todo->is_completed;
        $todo->save();

        // 成功レスポンスを返す
        return redirect()->route('users.todos')->with('message', 'Todoの完了状態が更新されました');
    }
}
