@extends('layouts.app')

@section('content')

    @if (Auth::check())
        <h2>{{ Auth::user()->name }}のTodoリスト</h2>
    @else
        <h2>テスト用Todoリスト</h2>
    @endif

    <!-- ユーザーのTodoリストを表示 -->
    <form action="{{ route('users.todos.create') }}" method="GET">
        @csrf
        <input class="create-input" type="text" name="content" placeholder="新しいTodoを追加">
        <button class="create-button" type="submit">作成</button>
    </form>

    <!-- タブでTodoを切り替え -->

    <div class="tabs">
        <a href="#incomplete">未完了のTodo</a>
        @if (Auth::check())
        <a href="#completed">完了したTodo</a>
        @elseif (!Auth::check())
        <a href="#completed" onclick="alert('ログインすると完了したTodoが表示されます。')">完了したTodo</a>
        @endif
    </div>

    <!-- 未完了のTodoを表示 -->
    <div class="todo-list">
        <h3 id="incomplete">Todo</h3>
        <ul>
        @if (Auth::check())
            @foreach ($todos as $todo)
                @if (!$todo->is_completed)
                <li>
                    <div class="todo-item">{{ $todo->content }}</div>

                    <div class="button">
                        <form action="{{ route('users.todos.update', ['id' => $todo->id]) }}" method="GET">
                            @csrf
                            <button class="update-button" type="submit">更新</button>
                        </form>

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
                @endif
            @endforeach

        @else
            <li>
                <div class="todo-item">ログインすると、あなたのTodoリストが表示されます。</div>

                <div class="button">
                    <button class="update-button" type="button" disabled>更新</button>

                    <button class="delete-button" type="button" disabled>削除</button>

                    <button class="completed-button" type="button" disabled>完了</button>
                </div>
            </li>
        @endif
        </ul>

    <!-- 完了したTodoを表示 -->
        <h3 id="completed">完了したTodo</h3>
        @if (Auth::check())
        <ul>
            @foreach ($todos as $todo)
                @if ($todo->is_completed)
                <li>
                    <div class="todo-item">{{ $todo->content }}</div>
                    <div class="completed-date">{{ $todo->completed_at }}</div>
                </li>
                <li>
                    <div class="todo-tips">Tips: {{ $todo->tips }}</div>
                </li>
                @endif
            @endforeach
        </ul>
        @else
            <p>ログインすると、完了したTodoが表示されます。</p>
        @endif
    </div>

@endsection