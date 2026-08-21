@extends('admin.layouts.app')

@section('title', $title ?? 'Ma\'lumotnomalar Boshqaruvi')

@push('css')
<style>
    .card .table tr td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')

@include('admin.pages.profile_options._breadcrumb', [
    'title' => $title ?? 'Ma\'lumotnomalar',
    'isIndex' => true,
])

<!-- [ Main Content ] start -->
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h5 class="card-title mb-0">{{ $title }}</h5>
                        <!-- Filter tablari -->
                        <div class="btn-group btn-group-sm ms-md-3" role="group">
                            <a href="{{ route('admin.profile-options.index') }}" class="btn {{ empty($currentType) ? 'btn-primary' : 'btn-light' }}">
                                Barchasi
                            </a>
                            <a href="{{ route('admin.profile-options.index', ['type' => 'interest']) }}" class="btn {{ $currentType === 'interest' ? 'btn-danger' : 'btn-light' }}">
                                💖 Qiziqishlar
                            </a>
                            <a href="{{ route('admin.profile-options.index', ['type' => 'dating_purpose']) }}" class="btn {{ $currentType === 'dating_purpose' ? 'btn-primary' : 'btn-light' }}">
                                💍 Maqsad
                            </a>
                            <a href="{{ route('admin.profile-options.index', ['type' => 'lifestyle']) }}" class="btn {{ $currentType === 'lifestyle' ? 'btn-success' : 'btn-light' }}">
                                🍷 Turmush Tarzi
                            </a>
                            <a href="{{ route('admin.profile-options.index', ['type' => 'about_me']) }}" class="btn {{ $currentType === 'about_me' ? 'btn-info' : 'btn-light' }}">
                                🎓 Men Haqimda
                            </a>
                            <a href="{{ route('admin.profile-options.index', ['type' => 'marital_status']) }}" class="btn {{ $currentType === 'marital_status' ? 'btn-warning text-dark' : 'btn-light' }}">
                                👨‍👩‍👧 Oilaviy Holati
                            </a>
                            <a href="{{ route('admin.profile-options.index', ['type' => 'language']) }}" class="btn {{ $currentType === 'language' ? 'btn-dark' : 'btn-light' }}">
                                🌐 Tillari
                            </a>
                        </div>
                    </div>
                    <div class="card-header-action d-flex align-items-center gap-2">
                        <a href="{{ route('admin.profile-options.create', ['type' => $currentType]) }}" class="btn btn-primary btn-sm">
                            <i class="feather-plus me-1"></i> Qo'shish
                        </a>
                        <button type="button" class="btn btn-sm btn-light border-0" onclick="window.location.reload()" title="Yangilash">
                            <i class="feather-rotate-cw"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body custom-card-action p-0">
                    @include('admin.pages.profile_options._columns', ['datas' => $options ?? $datas])
                </div>
                @if (isset($options) && $options->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-end p-3">
                        {{ $options->appends(request()->query())->links() }}
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

        // 1. Status Toggle Switch
        $(document).on('change', '.status-toggle', function () {
            const $switch = $(this);
            const url = $switch.data('url');
            const isChecked = $switch.is(':checked');

            axios.post(url)
                .then(function (response) {
                    if (response.data && response.data.success) {
                        Toast.fire({
                            icon: 'success',
                            title: response.data.message || "Holat o'zgartirildi!"
                        });
                    }
                })
                .catch(function (error) {
                    $switch.prop('checked', !isChecked);
                    Swal.fire({
                        icon: 'error',
                        title: 'Xatolik',
                        text: error.response?.data?.message || 'Holatni o\'zgartirishda xatolik yuz berdi'
                    });
                });
        });

        // 2. O'chirish
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url') || $btn.attr('data-url');
            const message = $btn.data('message') || $btn.attr('data-message') || "Haqiqatan ham ushbu parametrni o'chirmoqchimisiz?";

            if (!url) {
                console.error("O'chirish uchun URL topilmadi");
                return;
            }

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
                    const token = $('meta[name="csrf-token"]').attr('content');
                    axios.delete(url, {
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function (response) {
                        if (response.data && response.data.success) {
                            Toast.fire({
                                icon: 'success',
                                title: response.data.message || "Parametr muvaffaqiyatli o'chirildi!"
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
