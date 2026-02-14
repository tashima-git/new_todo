<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'TaskKill')</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('css')
</head>

<body>

    {{-- ヘッダー --}}
    <header class="tk-header">
        <div class="tk-header__inner">
            <div class="tk-logo">
                <a href="{{ route('tasks.index') }}" class="tk-logo__link">TaskKill</a>
            </div>

            {{-- ナビ --}}
            <nav class="tk-nav">
                <a href="{{ route('tasks.index') }}" class="tk-nav__item">
                    <span class="tk-nav__en">Task</span>
                    <span class="tk-nav__ja">タスク</span>
                </a>

                <a href="{{ route('taskkill.index') }}" class="tk-nav__item">
                    <span class="tk-nav__en">TaskKill</span>
                    <span class="tk-nav__ja">討伐</span>
                </a>

                <a href="{{ route('status.index') }}" class="tk-nav__item">
                    <span class="tk-nav__en">Status</span>
                    <span class="tk-nav__ja">ステータス</span>
                </a>

                <a href="{{ route('stats.index') }}" class="tk-nav__item">
                    <span class="tk-nav__en">Record</span>
                    <span class="tk-nav__ja">戦績</span>
                </a>

                <a href="{{ route('achievements.index') }}" class="tk-nav__item">
                    <span class="tk-nav__en">Achievement</span>
                    <span class="tk-nav__ja">実績</span>
                </a>

                <a href="{{ route('plan.index') }}" class="tk-nav__item">
                    <span class="tk-nav__en">Contract</span>
                    <span class="tk-nav__ja">契約</span>
                </a>

                <a href="{{ route('help.index') }}" class="tk-nav__item">
                    <span class="tk-nav__en">Help</span>
                    <span class="tk-nav__ja">ヘルプ</span>
                </a>
            </nav>
        </div>
    </header>

    {{-- フラッシュメッセージ --}}
    <div class="tk-flash">
        @if (session('success'))
            <div class="tk-flash__success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="tk-flash__error">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="tk-flash__error">
                <div class="tk-flash__title">入力エラー</div>
                <ul class="tk-flash__list">
                    @foreach ($errors->all() as $error)
                        <li class="tk-flash__item">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    {{-- メイン --}}
    <main class="tk-main">
        @yield('content')
    </main>

    @yield('js')
</body>

</html>
