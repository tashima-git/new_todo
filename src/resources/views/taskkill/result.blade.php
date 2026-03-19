@extends('layouts.app')

@section('title', '討伐結果')

@section('content')
<div
    id="taskkill-result-root"
    data-logs='@json($logs)'
    data-total='@json($totalGained)'
>
</div>
@endsection

{{-- JS読み込み: Laravel Mixを使用している場合 --}}
<script>
console.log(@json($logs));
</script>
<script src="{{ mix('js/app.js') }}"></script>
