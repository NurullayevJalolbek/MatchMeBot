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

    <!-- External Compiled CSS -->
    <link rel="stylesheet" href="/assets/css/miniapp.css">

    <!-- Canvas Confetti for Celebration -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.3/dist/confetti.browser.min.js"></script>
</head>
<body>

<div class="app-container">
    <div class="onboarding-screen-wrapper">
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
