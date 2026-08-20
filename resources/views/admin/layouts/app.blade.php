<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />

    <title>@yield('title', 'Duralux || Dashboard')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.min.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/daterangepicker.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/sweetalert2.min.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/theme.min.css') }}" />

    <style>
        .nxl-content .page-header {
            position: sticky;
            top: 80px;
            z-index: 99;
            background-color: #ffffff;
            padding: 16px 24px;
            margin-left: 0px;
            margin-right: 0px;
            margin-top: -24px;
            margin-bottom: 24px;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        [data-theme="dark"] .nxl-content .page-header,
        .app-dark .nxl-content .page-header,
        .dark-theme .nxl-content .page-header,
        body.dark .nxl-content .page-header {
            background-color: #111827 !important;
            border-bottom-color: #1f2937 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
        }

        /* Swal Custom Enhancements */
        .swal2-popup {
            border-radius: 20px !important;
            padding: 24px !important;
            font-family: inherit !important;
        }
        .swal2-actions {
            gap: 12px;
        }
    </style>

    @stack('css')
</head>

<body>

    @include('admin.layouts.sidebar')

    @include('admin.layouts.header')

    <main class="nxl-container">
        <div class="nxl-content">
            @yield('content')
        </div>
    </main>


    {{-- ===================== JS ===================== --}}

    <script src="{{ asset('assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/js/sweetalert2.min.js') }}"></script>

    <script src="{{ asset('assets/js/common-init.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard-init.min.js') }}"></script>

    <script src="{{ asset('assets/js/theme-customizer-init.min.js') }}"></script>

    @stack('js')
</body>

</html>
