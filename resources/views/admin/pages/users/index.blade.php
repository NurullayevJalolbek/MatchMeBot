@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="nxl-content">
    @include('admin.pages.users._breadcrumb', ['isIndex' => true])

    <div class="main-content">
        <!-- ==================== 1. STATISTIKA VIDJETLARI ==================== -->
        <div class="row g-3 mb-4">
            <!-- Jami Foydalanuvchilar -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-bold text-uppercase d-block mb-1">Jami Foydalanuvchilar</span>
                            <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_users']) }}</h3>
                        </div>
                        <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-3">
                            <i class="feather-users fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faol VIP Obunachilar -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-bold text-uppercase d-block mb-1">VIP Obunachilar</span>
                            <h3 class="fw-bold mb-0 text-warning">{{ number_format($stats['vip_users']) }}</h3>
                        </div>
                        <div class="avatar-text avatar-md bg-soft-warning text-warning rounded-3">
                            <i class="feather-award fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faol Boostlar -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-bold text-uppercase d-block mb-1">Faol Boostlar</span>
                            <h3 class="fw-bold mb-0 text-danger">{{ number_format($stats['boost_users']) }}</h3>
                        </div>
                        <div class="avatar-text avatar-md bg-soft-danger text-danger rounded-3">
                            <i class="feather-zap fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yangi Qo'shilganlar (7 kun) -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 fw-bold text-uppercase d-block mb-1">Yangi (Oxirgi 7 kun)</span>
                            <h3 class="fw-bold mb-0 text-success">+{{ number_format($stats['new_this_week']) }}</h3>
                        </div>
                        <div class="avatar-text avatar-md bg-soft-success text-success rounded-3">
                            <i class="feather-user-plus fs-20"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== 2. QIDIRUV VA FILTRLAR ==================== -->
        @include('admin.pages.users._search')

        <!-- ==================== 3. ASOSIY JADVAL ==================== -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-0">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="feather-list me-2 text-primary"></i> Foydalanuvchilar Ro'yxati
                </h5>
                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fs-12 fw-bold">
                    Jami: {{ $users->total() }} ta
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    @include('admin.pages.users._columns')
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $photoUrl = $user->primary_photo_url ?? asset('assets/images/avatar/default.png');
                                $isVipActive = $user->is_vip && ($user->vip_expires_at === null || $user->vip_expires_at->isFuture());
                                $isBoostActive = $user->boost_expires_at && $user->boost_expires_at->isFuture();
                            @endphp
                            <tr id="row-user-{{ $user->id }}">
                                <!-- 1. ID -->
                                <td class="text-center fw-bold text-muted fs-12">#{{ $user->id }}</td>

                                <!-- 2. Foydalanuvchi -->
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <img src="{{ $photoUrl }}" 
                                                 alt="{{ $user->full_name }}" 
                                                 class="rounded-circle object-fit-cover shadow-sm border border-2 border-white"
                                                 style="width: 44px; height: 44px;"
                                                 onerror="this.onerror=null; this.src='{{ asset('assets/images/avatar/default.png') }}';">
                                            @if($isVipActive)
                                                <span class="position-absolute bottom-0 end-0 bg-warning text-white rounded-circle p-1 d-flex align-items-center justify-content-center shadow" 
                                                      style="width: 16px; height: 16px; font-size: 9px;" title="VIP Foydalanuvchi">
                                                    👑
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="fw-bold text-dark text-decoration-none d-block hover-primary fs-14">
                                                {{ $user->full_name }}
                                            </a>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                @if($user->username)
                                                    <a href="https://t.me/{{ $user->username }}" target="_blank" class="badge bg-soft-info text-info text-decoration-none fs-11">
                                                        {{ '@' . $user->username }}
                                                    </a>
                                                @endif
                                                @if($user->telegram_id)
                                                    <span class="badge bg-soft-secondary text-dark fs-11 cursor-pointer copy-chat-id" 
                                                          data-chat-id="{{ $user->telegram_id }}" 
                                                          title="Chat ID dan nusxa olish">
                                                        <i class="feather-copy me-1"></i> ID: {{ $user->telegram_id }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- 3. Jinsi & Yoshi -->
                                <td>
                                    @if($user->gender)
                                        <span class="badge {{ $user->gender->value === 'male' ? 'bg-soft-primary text-primary' : 'bg-soft-danger text-danger' }} fs-12 me-1">
                                            {{ $user->gender->label() }}
                                        </span>
                                    @endif
                                    @if($user->age)
                                        <span class="fw-bold text-dark fs-13">{{ $user->age }} yosh</span>
                                    @else
                                        <span class="text-muted fs-12">—</span>
                                    @endif
                                </td>

                                <!-- 4. Shahar -->
                                <td>
                                    @if($user->city)
                                        <span class="text-dark fs-13">
                                            <i class="feather-map-pin me-1 text-muted"></i>
                                            {{ \App\Enums\General\CityEnum::tryFrom($user->city)?->label() ?? ucfirst($user->city) }}
                                        </span>
                                    @else
                                        <span class="text-muted fs-12">—</span>
                                    @endif
                                </td>

                                <!-- 5. Xizmatlar (VIP / Boost) -->
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        @if($isVipActive)
                                            <span class="badge bg-warning text-white fs-11 shadow-sm" title="{{ $user->vip_expires_at ? 'Muddati: ' . format_datetime($user->vip_expires_at) : 'Cheksiz' }}">
                                                👑 VIP
                                            </span>
                                        @endif
                                        @if($isBoostActive)
                                            <span class="badge bg-danger text-white fs-11 shadow-sm" title="Muddati: {{ format_datetime($user->boost_expires_at) }}">
                                                ⚡ Boost
                                            </span>
                                        @endif
                                        @if(!$isVipActive && !$isBoostActive)
                                            <span class="badge bg-soft-secondary text-muted fs-11">Oddiy</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 6. Anketa -->
                                <td class="text-center">
                                    @if($user->onboarding_completed)
                                        <span class="badge bg-soft-success text-success fs-12 px-2 py-1 rounded-pill">
                                            <i class="feather-check me-1"></i> To'liq
                                        </span>
                                    @else
                                        <span class="badge bg-soft-warning text-warning fs-12 px-2 py-1 rounded-pill">
                                            <i class="feather-clock me-1"></i> Jarayonda
                                        </span>
                                    @endif
                                </td>

                                <!-- 7. Holati (Switch) -->
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input switch-status cursor-pointer" 
                                               type="checkbox" 
                                               role="switch"
                                               data-url="{{ route('admin.users.toggle', $user->id) }}"
                                               {{ $user->status === \App\Enums\Admin\AdminStatusEnum::ACTIVE ? 'checked' : '' }}
                                               title="Faollikni o'zgartirish">
                                    </div>
                                </td>

                                <!-- 8. Ro'yxatdan o'tgan -->
                                <td class="text-center text-muted fs-12">
                                    {{ format_datetime($user->created_at) }}
                                </td>

                                <!-- 9. Amallar -->
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <!-- Ko'rish (Show) -->
                                        <a href="{{ route('admin.users.show', $user->id) }}" 
                                           class="btn btn-sm btn-light-brand" 
                                           title="Profilni to'liq ko'rish">
                                            <i class="feather-eye"></i>
                                        </a>

                                        <!-- O'chirish -->
                                        <button type="button" 
                                                class="btn btn-sm btn-light text-danger btn-delete"
                                                data-url="{{ route('admin.users.destroy', $user->id) }}"
                                                data-message="Haqiqatan ham {{ $user->full_name }} profilini o'chirmoqchimisiz?"
                                                title="O'chirish">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="feather-users fs-36 d-block mb-2 text-muted opacity-50"></i>
                                    Foydalanuvchilar topilmadi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="card-footer bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted fs-13">
                        Ko'rsatilyapti: <b>{{ $users->firstItem() ?? 0 }}</b> dan <b>{{ $users->lastItem() ?? 0 }}</b> gacha, jami <b>{{ $users->total() }}</b> ta foydalanuvchi
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    (function ($) {
        'use strict';

        // 1. Toast xabarnoma sozlamasi
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });

        // 2. Chat ID nusxa olish
        $(document).on('click', '.copy-chat-id', function () {
            const chatId = $(this).data('chat-id');
            navigator.clipboard.writeText(chatId).then(() => {
                Toast.fire({
                    icon: 'success',
                    title: `Chat ID (${chatId}) nusxalandi!`
                });
            });
        });

        // 3. Holatni o'zgartirish (Switch toggle)
        $(document).on('change', '.switch-status', function () {
            const $switch = $(this);
            const url = $switch.data('url');

            axios.post(url, {}, {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (response) {
                if (response.data && response.data.success) {
                    Toast.fire({
                        icon: 'success',
                        title: response.data.message || "Holat muvaffaqiyatli o'zgartirildi!"
                    });
                }
            })
            .catch(function (error) {
                $switch.prop('checked', !$switch.prop('checked'));
                Swal.fire({
                    icon: 'error',
                    title: 'Xatolik',
                    text: error.response?.data?.message || "Holatni o'zgartirishda xatolik yuz berdi"
                });
            });
        });

        // 4. Foydalanuvchini O'chirish
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const url = $btn.data('url') || $btn.attr('data-url');
            const message = $btn.data('message') || "Haqiqatan ham ushbu foydalanuvchini o'chirmoqchimisiz?";

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
                    axios.delete(url, {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function (response) {
                        if (response.data && response.data.success) {
                            Toast.fire({
                                icon: 'success',
                                title: response.data.message || "Foydalanuvchi muvaffaqiyatli o'chirildi!"
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
