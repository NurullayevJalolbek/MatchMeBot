<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MatchMe — Dating App</title>

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
</head>
<body>

<div class="app-container">
    <!-- Initial Branded Splash Screen -->
    <div id="app-splash" class="app-splash">
        <div class="splash-logo-circle">
            <svg viewBox="0 0 24 24" class="splash-heart-svg">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </div>
        <div class="splash-brand-title">MatchMe</div>
        <div class="splash-brand-subtitle">DATING APP</div>
        <div class="splash-loader-bar">
            <div class="splash-loader-progress"></div>
        </div>
    </div>

    <div class="onboarding-screen-wrapper" id="onboarding-main-wrapper" style="display: none;">
        {{-- Header --}}
        @include('telegram_bot.mini_app.partials.header')

        {{-- Screen 0: Welcome --}}
        @include('telegram_bot.mini_app.steps.welcome')

        {{-- Screen 1: Name (16%) --}}
        @include('telegram_bot.mini_app.steps.step1-name')

        {{-- Screen 2: Age (33%) --}}
        @include('telegram_bot.mini_app.steps.step2-age')

        {{-- Screen 3: Gender & Looking For (50%) --}}
        @include('telegram_bot.mini_app.steps.step3-gender')

        {{-- Screen 4: City (67%) --}}
        @include('telegram_bot.mini_app.steps.step4-city')

        {{-- Screen 5: Bio (83%) --}}
        @include('telegram_bot.mini_app.steps.step5-bio')

        {{-- Screen 6: Photos (100%) --}}
        @include('telegram_bot.mini_app.steps.step6-photos')

        {{-- Screen 7: Success / Completed --}}
        @include('telegram_bot.mini_app.steps.success')
    </div>
</div>

{{-- Modals --}}
@include('telegram_bot.mini_app.partials.modals')

<!-- Global User Config -->
<script>
    window.APP_DEFAULT_USER_ID = {{ auth()->id() ?? 123456789 }};
</script>

<!-- External Compiled JS -->
<script src="/assets/js/miniapp.js"></script>

</body>
</html>
