@extends('admin.layouts.app')

@section('title', 'Foydalanuvchilar Boostlari Tarixi')

@push('css')
<style>
    .card .table tr td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')

@include('admin.pages.user_boosts._breadcrumb', [
    'title' => 'Boostlar Tarixi',
])

<!-- [ Main Content ] start -->
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="card-title mb-0">Boostlar Tarixi</h5>
                        <div class="btn-group btn-group-sm ms-3" role="group">
                            <a href="{{ route('admin.user-boosts.index') }}" class="btn {{ empty($currentStatus) ? 'btn-primary' : 'btn-light' }}">
                                Barchasi
                            </a>
                            <a href="{{ route('admin.user-boosts.index', ['status' => 'active']) }}" class="btn {{ $currentStatus === 'active' ? 'btn-success' : 'btn-light' }}">
                                <i class="feather-zap me-1"></i> Faol
                            </a>
                            <a href="{{ route('admin.user-boosts.index', ['status' => 'expired']) }}" class="btn {{ $currentStatus === 'expired' ? 'btn-secondary' : 'btn-light' }}">
                                <i class="feather-clock me-1"></i> Tugagan
                            </a>
                            <a href="{{ route('admin.user-boosts.index', ['status' => 'cancelled']) }}" class="btn {{ $currentStatus === 'cancelled' ? 'btn-danger' : 'btn-light' }}">
                                <i class="feather-x-circle me-1"></i> Bekor qilingan
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
                    @include('admin.pages.user_boosts._columns', ['datas' => $boosts ?? $datas])
                </div>
                @if (isset($boosts) && $boosts->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-end p-3">
                        {{ $boosts->links() }}
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

        // Chat ID dan tezkor nusxa olish
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

        // Boostni muddatidan oldin bekor qilish
        $(document).on('click', '.btn-cancel-boost', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url');
            const user = $btn.data('user');

            Swal.fire({
                title: "Boostni bekor qilmoqchimisiz?",
                text: user + " ning faol Boost xizmati darhol to'xtatiladi.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f59e0b",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Ha, bekor qilinsin!",
                cancelButtonText: "Orqaga",
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'btn btn-warning px-4 py-2 rounded-3 me-2',
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
                                    title: response.data.message || "Boost bekor qilindi!"
                                });
                                setTimeout(() => window.location.reload(), 700);
                            }
                        })
                        .catch(function (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Xatolik',
                                text: error.response?.data?.message || 'Xatolik yuz berdi'
                            });
                        });
                }
            });
        });

        // O'chirish
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url') || $btn.attr('data-url');
            const message = $btn.data('message') || $btn.attr('data-message') || "Haqiqatan ham ushbu yozuvni o'chirmoqchimisiz?";

            if (!url) return;

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
                                    title: response.data.message || "Muvaffaqiyatli o'chirildi!"
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
