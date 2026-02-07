@extends('layouts.app')

@section('content')
    <h2>{{ $name->users }}のTodoリスト</h2>

    <!-- タブでTodoを切り替え -->
    <div class="tabs">
        <a href="#incomplete">未完了のTodo</a>
        <a href="#completed">完了したTodo</a>
    </div>

    <div class="todo-list">

        <!-- 未完了のTodoを表示 -->
        <h3 id="incomplete">Todo</h3>
        <ul>
            @foreach ($todos as $todo)
                <li>
                    <div class="todo-item">{{ $todo->title }}</div>
                </li>
                <li>
                    <form action="{{ route('users.todos.tips', ['id' => $todo->id]) }}" method="GET">
                            @csrf
                            <button class="message_button" type="submit">tips</button>
                    </form>
            @endforeach
        </ul>

        <!-- 完了したTodoを表示 -->
        <h3 id="completed">完了したTodo</h3>
        <ul>
            @foreach ($todos as $todo)
                <li>
                    <div class="todo-item">{{ $todo->title }}</div>
                </li>
                <li>
                    <form action="{{ route('users.todos.tips', ['id' => $todo->id]) }}" method="GET">
                            @csrf
                            <button class="message_button" type="submit">tips</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
@endsection