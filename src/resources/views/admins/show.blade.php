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

    <script src="{{ asset('js/app.js') }}"></script>
@endsection