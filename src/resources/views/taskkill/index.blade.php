@extends('layouts.app')

@section('title', 'TaskKill')

@section('css')
<link rel="stylesheet" href="/css/taskkill.css">
@endsection

@section('content')

<div id="taskkill-root"
    data-tasks='@json($tasks->values())'
    data-execute-url="{{ route('taskkill.execute') }}">
</div>

@endsection