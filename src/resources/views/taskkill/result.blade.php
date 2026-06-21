@extends('layouts.app')

@section('title', '討伐結果')

@section('css')
<link rel="stylesheet" href="/css/taskkill.css">
@endsection

@section('content')
<div
    id="taskkill-result-root"
    data-logs='@json($logs)'
    data-total='@json($totalGained)'
    data-tasks-url="{{ route('tasks.index') }}"
    data-record-url="{{ route('record.index') }}"
>
</div>
@endsection
