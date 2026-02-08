<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\AdminTodoController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


// ログイン・登録機能
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/admins/login', [AdminLoginController::class, 'showLoginForm'])->name('admins.login');

Route::post('/admins/login', [AdminLoginController::class, 'login'])->name('admins.login.post');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// トップページ
Route::get('/', function () {
    return view('index');
});

// ユーザーページ
Route::middleware(['auth'])->group(function () {
    Route::get('/', [TodoController::class, 'index'])->name('users.todos');
    Route::get('/users.todos.create', [TodoController::class, 'create'])->name('users.todos.create');
    Route::put('/users.todos.update/{id}', [TodoController::class, 'update'])->name('users.todos.update');
    Route::delete('/users.todos.delete/{id}', [TodoController::class, 'delete'])->name('users.todos.delete');
    Route::patch('/users.todos.completed/{id}', [TodoController::class, 'completed'])->name('users.todos.completed');
});

// 管理者ページ
Route::middleware(['auth:admin'])->group(function () {

    Route::get('/admin', [AdminTodoController::class, 'showUsers'])->name('admins.member');

    Route::get('/admins/show/{id}', [AdminTodoController::class, 'index'])->name('admins.show');

    Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admins.logout');

    Route::get('/users.todos.createTips/{id}', [AdminTodoController::class, 'createTips'])->name('admins.todos.createTips');

});