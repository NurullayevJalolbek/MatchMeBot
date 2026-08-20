@extends('admin.layouts.app')

@section('title', 'Xarajatlar Boshqaruvi')

@push('css')
<style>
    .card .table tr td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')

@include('admin.pages.expenses._breadcrumb', [
    'title' => 'Xarajatlar',
    'isIndex' => true,
])

<!-- [ Main Content ] start -->
<div class="main-content">
    <div class="row">
        <div class="col-12">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">Xarajatlar Ro'yxati</h5>
                    <div class="card-header-action d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-light border-0" onclick="window.location.reload()" title="Yangilash">
                            <i class="feather-rotate-cw"></i>
                        </button>
                        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary btn-sm px-3">
                            <i class="feather-plus me-1"></i> Qo'shish
                        </a>
                    </div>
                </div>
                <div class="card-body custom-card-action p-0">
                    @include('admin.pages.expenses._columns', ['datas' => $expenses ?? $datas])
                </div>
                @if (isset($expenses) && $expenses->hasPages())
                    <div class="card-footer d-flex align-items-center justify-content-end p-3">
                        {{ $expenses->links() }}
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

        // Toast bildirishnoma
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

        // O'chirish tugmasi bosilganda
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const url = $btn.data('url') || $btn.attr('data-url');
            const message = $btn.data('message') || $btn.attr('data-message') || "Haqiqatan ham ushbu Xarajat yozuvini o'chirmoqchimisiz?";

            if (!url) {
                console.error('URL topilmadi!');
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
                    const token = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: token
                        },
                        success: function (response) {
                            if (response && response.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message || "Xarajat muvaffaqiyatli o'chirildi!"
                                });
                                const $row = $btn.closest('tr');
                                $row.fadeOut(350, function () {
                                    $(this).remove();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Xatolik',
                                    text: response.message || 'O\'chirishda xatolik yuz berdi'
                                });
                            }
                        },
                        error: function (xhr) {
                            let errorMsg = "O'chirishda xatolik yuz berdi";
                            try {
                                const res = JSON.parse(xhr.responseText);
                                if (res.message) errorMsg = res.message;
                            } catch (e) {}

                            Swal.fire({
                                icon: 'error',
                                title: 'Xatolik',
                                text: errorMsg
                            });
                        }
                    });
                }
            });
        });
    })(jQuery);
</script>
@endpush
