@if ($paginator->hasPages())
    <nav class="pagination-wrapper">

        {{-- 前へ --}}
        @if ($paginator->onFirstPage())
            <span>＜</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">＜</a>
        @endif

        {{-- ページ番号 --}}
        @foreach ($elements as $element)

            {{-- "..." --}}
            @if (is_string($element))
                <span>{{ $element }}</span>
            @endif

            {{-- ページリンク --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span>{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif

        @endforeach

        {{-- 次へ --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">＞</a>
        @else
            <span>＞</span>
        @endif

    </nav>
@endif