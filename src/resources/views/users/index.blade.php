@extends('layouts.app')

@section('content')
    <h2>{{ $name->users }}のTodoリスト</h2>

    <!-- ユーザーのTodoリストを表示 -->
    <form action="{{ route('users.todos.create') }}" method="GET">
        @csrf
        <button class="create-button" type="submit">作成</button>

    <h3>Todo</h3>
    <ul>
        @foreach ($todos as $todo)
            @if (!$todo->is_completed)
            <li>
                <div class="todo-item">{{ $todo->title }}</div>
                <div class="button">
                    <form action="{{ route('users.todos.edit', ['id' => $todo->id]) }}" method="GET">
                        @csrf
                        <button class="update-button" type="submit">更新</button>

                    <form action="{{ route('users.todos.delete', ['id' => $todo->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="delete-button" type="submit">削除</button>
                    </form>

                    <form action="{{ route('users.todos.completed', ['id' => $todo->id]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button class="completed-button" type="submit">完了</button>
                    </form>

            </li>

            <li>
                <div class="todo-tips">Tips: {{ $todo->tips }}</div>
            </li>
        @endforeach
    </ul>

    <script src="{{ asset('js/app.js') }}"></script>
@endsection