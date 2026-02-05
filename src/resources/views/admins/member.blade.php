@extends('layouts.app')

@section('content')
    <h2>{{ $name->admins }}の管理リスト</h2>

    <!-- ユーザーをリストで表示 -->
    <ul>
        @foreach ($users as $user)
            <li>
                <div class="user-name">{{ $user->name }}</div>

                <div class="button">
                    <form action="{{ route('admins.users.view', ['id' => $user->id]) }}" method="GET">
                        @csrf
                        <button class="view-button" type="submit">詳細</button>
                    </form>
            </li>
        @endforeach
    </ul>