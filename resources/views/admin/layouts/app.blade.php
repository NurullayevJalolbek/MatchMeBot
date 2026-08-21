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

        /* Barcha jadvallarning thead (bosh qismi) ko'k (Primary / Qo'shish tugmasi rangida) */
        .table thead tr,
        .table thead th {
            background-color: #3454d1 !important;
            color: #ffffff !important;
            border-color: #3454d1 !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            font-size: 11.5px;
            letter-spacing: 0.6px;
            vertical-align: middle;
            padding-top: 13px !important;
            padding-bottom: 13px !important;
        }

        .card .table thead th:first-child {
            padding-left: 20px !important;
        }

        .card .table thead th:last-child {
            padding-right: 20px !important;
        }

        .card .table tbody td:first-child {
            padding-left: 20px !important;
        }

        .card .table tbody td:last-child {
            padding-right: 20px !important;
        }

        /* Sidebar Submenu miltillash (flicker) ning oldini olish */
        .nxl-navbar .nxl-item.nxl-hasmenu:not(.nxl-trigger) > .nxl-submenu {
            display: none !important;
        }
        .nxl-navbar .nxl-item.nxl-hasmenu.nxl-trigger > .nxl-submenu {
            display: block !important;
        }

        /* Clean and sleek Bootstrap 5 Pagination */
        .pagination {
            margin-bottom: 0;
            gap: 4px;
        }
        .page-item .page-link {
            border-radius: 8px !important;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 500;
            font-size: 13px;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            box-shadow: none;
        }
        .page-item.active .page-link {
            background-color: #3454d1 !important;
            border-color: #3454d1 !important;
            color: #ffffff !important;
        }
        .page-item.disabled .page-link {
            background-color: #f8fafc;
            color: #94a3b8;
            border-color: #e2e8f0;
        }
        .page-item .page-link:hover:not(.disabled) {
            background-color: #f1f5f9;
            color: #3454d1;
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // Axios Global CSRF Setup
        if (window.axios) {
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }
        }
    </script>

    <!-- Global Avtomatik Narx va Summa Formatlash Scripti -->
    <script>
        (function ($) {
            'use strict';

            function formatPriceValue(val) {
                if (!val && val !== 0) return '';
                let clean = val.toString().replace(/[^\d]/g, '');
                if (!clean) return '';
                return clean.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            }

            function initPriceInputs() {
                $('.price-format, input[name="price"], input[name="original_price"], input[name="amount"]').each(function () {
                    $(this).attr('type', 'text');
                    $(this).attr('inputmode', 'numeric');
                    if ($(this).val()) {
                        $(this).val(formatPriceValue($(this).val()));
                    }
                });
            }

            $(document).ready(function () {
                initPriceInputs();
            });

            // Real-time yozganda probellar bilan ajratish
            $(document).on('input', '.price-format, input[name="price"], input[name="original_price"], input[name="amount"]', function () {
                let originalVal = this.value;
                let cursorPosition = this.selectionStart;
                let digitsBeforeCursor = originalVal.slice(0, cursorPosition).replace(/[^\d]/g, '').length;

                let formatted = formatPriceValue(originalVal);
                this.value = formatted;

                // Kursorni to'g'ri joyiga qaytarish
                let newCursorPos = 0;
                let digitCount = 0;
                for (let i = 0; i < formatted.length; i++) {
                    if (/\d/.test(formatted[i])) {
                        digitCount++;
                    }
                    if (digitCount === digitsBeforeCursor) {
                        newCursorPos = i + 1;
                        break;
                    }
                }
                if (digitsBeforeCursor === 0) newCursorPos = 0;
                this.setSelectionRange(newCursorPos, newCursorPos);
            });

            // Form yuborilganda bo'sh joylarni tozalash (backendga toza raqam borishi uchun)
            $(document).on('submit', 'form', function () {
                $(this).find('.price-format, input[name="price"], input[name="original_price"], input[name="amount"]').each(function () {
                    let cleanVal = $(this).val().toString().replace(/\s+/g, '');
                    $(this).val(cleanVal);
                });
            });
        })(jQuery);
    </script>

    @stack('js')
</body>

</html>
