<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MatchMe — Dating App')</title>

    <!-- Telegram WebApp SDK -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 & App Bundle (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- External Compiled Mobile Native CSS (Animations, Keyframes, Glassmorphism) -->
    <link rel="stylesheet" href="/assets/css/miniapp.css">

    <!-- Canvas Confetti for Celebration -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>

    @stack('styles')
</head>
<body>

<div class="app-container">
    {{-- Universal Top Header Bar (if not hidden by page) --}}
    @if(!isset($hideTopBar) || !$hideTopBar)
        {{-- top bar --}}
    @endif

    {{-- Main Dynamic Page Content --}}
    <main class="app-main-content">
        @yield('content')
    </main>

    {{-- Universal Bottom Navigation Bar --}}
    @include('telegram_bot.mini_app.partials.bottom-nav')
</div>

{{-- Universal Modals --}}
@include('telegram_bot.mini_app.partials.modals')
@include('telegram_bot.mini_app.partials.filter-modal')
@include('telegram_bot.mini_app.partials.wallet-modal')
@include('telegram_bot.mini_app.partials.boost-modal')
@include('telegram_bot.mini_app.partials.vip-modal')

<!-- Global User Config -->
<script>
    window.APP_DEFAULT_USER_ID = {{ auth()->id() ?? 123456789 }};
</script>

<!-- External Compiled JS -->
<script src="/assets/js/miniapp.js"></script>

@stack('scripts')

</body>
</html>
