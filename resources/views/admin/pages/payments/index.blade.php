@extends('admin.layouts.app')

@section('title', 'Tushumlar (To\'lovlar) Boshqaruvi')

@push('css')
<style>
    .card .table tr td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')

@include('admin.pages.payments._breadcrumb', [
    'title' => 'Tushumlar (To\'lovlar)',
    'isIndex' => true,
])

<!-- [ Main Content ] start -->
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="card-title mb-0">To'lovlar Ro'yxati</h5>
                        <!-- Filter tablari -->
                        <div class="btn-group btn-group-sm ms-3" role="group">
                            <a href="{{ route('admin.payments.index') }}" class="btn {{ empty($currentStatus) ? 'btn-primary' : 'btn-light' }}">
                                Barchasi
                            </a>
                            <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="btn {{ $currentStatus === 'pending' ? 'btn-warning text-dark' : 'btn-light' }}">
                                <i class="feather-clock me-1"></i> Kutilmoqda
                            </a>
                            <a href="{{ route('admin.payments.index', ['status' => 'approved']) }}" class="btn {{ $currentStatus === 'approved' ? 'btn-success' : 'btn-light' }}">
                                <i class="feather-check-circle me-1"></i> Tasdiqlangan
                            </a>
                            <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" class="btn {{ $currentStatus === 'rejected' ? 'btn-danger' : 'btn-light' }}">
                                <i class="feather-x-circle me-1"></i> Rad etilgan
                            </a>
                        </div>
                    </div>
                    <div class="card-header-action d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-light border-0" onclick="window.location.reload()" title="Yangilash">
                            <i class="feather-rotate-cw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body custom-card-action p-0">
                    @include('admin.pages.payments._columns', ['datas' => $payments ?? $datas])
                </div>
                @if (isset($payments) && $payments->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-end p-3">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@push('js')
<script>
    (function ($) {
        'use strict';

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        // 0. Chat ID dan tezkor nusxa olish
        $(document).on('click', '.copy-chat-id', function (e) {
            e.preventDefault();
            const chatId = $(this).data('chat-id');
            if (chatId) {
                navigator.clipboard.writeText(chatId).then(() => {
                    Toast.fire({
                        icon: 'info',
                        title: "Chat ID (" + chatId + ") nusxalandi!"
                    });
                });
            }
        });

        // 1. Chek / Skrinshotni katta ko'rish
        $(document).on('click', '.btn-view-receipt', function (e) {
            e.preventDefault();
            const imgUrl = $(this).data('image');
            const user = $(this).data('user');
            const amount = $(this).data('amount');

            if (!imgUrl) return;

            Swal.fire({
                title: user + " — " + amount,
                imageUrl: imgUrl,
                imageAlt: 'Chek skrinshoti',
                showCloseButton: true,
                showConfirmButton: false,
                width: 'auto',
                customClass: {
                    popup: 'rounded-4 p-3 shadow-lg',
                    image: 'img-fluid rounded-3 border'
                }
            });
        });

        // 2. To'lovni Tasdiqlash
        $(document).on('click', '.btn-approve', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            const user = $btn.data('user');
            const plan = $btn.data('plan');

            Swal.fire({
                title: "To'lovni tasdiqlaysizmi?",
                html: "<b>" + user + "</b> uchun <b>" + plan + "</b> xizmati darhol faollashtiriladi va bot orqali xabar yuboriladi.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#10b981",
                cancelButtonColor: "#64748b",
                confirmButtonText: "<i class='feather-check me-1'></i> Ha, Tasdiqlansin!",
                cancelButtonText: "Bekor qilish",
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'btn btn-success px-4 py-2 rounded-3 me-2',
                    cancelButton: 'btn btn-light px-4 py-2 rounded-3'
                },
                buttonsStyling: false
            }).then((result) => {
                const isConfirmed = result === true || (result && (result.isConfirmed === true || result.value === true));
                if (isConfirmed) {
                    axios.post(url)
                        .then(function (response) {
                            if (response.data && response.data.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.data.message || "To'lov muvaffaqiyatli tasdiqlandi!"
                                });
                                setTimeout(() => window.location.reload(), 800);
                            }
                        })
                        .catch(function (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Xatolik',
                                text: error.response?.data?.message || 'Tasdiqlash jarayonida xatolik yuz berdi'
                            });
                        });
                }
            });
        });

        // 3. To'lovni Rad etish / Qaytarish (Sababi bilan)
        $(document).on('click', '.btn-reject', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            const user = $btn.data('user');

            Swal.fire({
                title: "To'lovni rad etish / qaytarish",
                text: user + " uchun rad etish sababini kiriting (foydalanuvchiga yuboriladi):",
                input: 'textarea',
                inputPlaceholder: 'Masalan: Chekdagi summa to\'liq emas yoki soxta chek...',
                inputAttributes: {
                    'aria-label': 'Rad etish sababi'
                },
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#64748b",
                confirmButtonText: "<i class='feather-x me-1'></i> Rad etish",
                cancelButtonText: "Bekor qilish",
                reverseButtons: true,
                inputValidator: (value) => {
                    if (!value || !value.trim()) {
                        return 'Iltimos, rad etish sababini yozing!';
                    }
                },
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'btn btn-danger px-4 py-2 rounded-3 me-2',
                    cancelButton: 'btn btn-light px-4 py-2 rounded-3'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    axios.post(url, { reason: result.value })
                        .then(function (response) {
                            if (response.data && response.data.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.data.message || "To'lov rad etildi!"
                                });
                                setTimeout(() => window.location.reload(), 800);
                            }
                        })
                        .catch(function (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Xatolik',
                                text: error.response?.data?.message || 'Rad etish jarayonida xatolik yuz berdi'
                            });
                        });
                }
            });
        });

        // 4. O'chirish
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            const message = $btn.data('message') || "Haqiqatan ham ushbu to'lov yozuvini o'chirmoqchimisiz?";

            Swal.fire({
                title: "Ishonchingiz komilmi?",
                text: message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Ha, o'chirilsin!",
                cancelButtonText: "Bekor qilish",
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'btn btn-danger px-4 py-2 rounded-3 me-2',
                    cancelButton: 'btn btn-light px-4 py-2 rounded-3'
                },
                buttonsStyling: false
            }).then((result) => {
                const isConfirmed = result === true || (result && (result.isConfirmed === true || result.value === true));
                if (isConfirmed) {
                    axios.delete(url)
                        .then(function (response) {
                            if (response.data && response.data.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.data.message || "To'lov muvaffaqiyatli o'chirildi!"
                                });
                                const $row = $btn.closest('tr');
                                $row.fadeOut(350, function () {
                                    $(this).remove();
                                });
                            }
                        })
                        .catch(function (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Xatolik',
                                text: error.response?.data?.message || 'O\'chirishda xatolik yuz berdi'
                            });
                        });
                }
            });
        });
    })(jQuery);
</script>
@endpush
