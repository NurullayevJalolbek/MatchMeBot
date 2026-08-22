@extends('admin.layouts.app')

@section('title', $title)

@section('content')
<div class="nxl-content">
    @include('admin.pages.users._breadcrumb', ['label' => $user->full_name])

    <div class="main-content">
        @php
            $photoUrl = $user->primary_photo_url ?? asset('assets/images/avatar/default.png');
            $isVipActive = $user->is_vip && ($user->vip_expires_at === null || $user->vip_expires_at->isFuture());
            $isBoostActive = $user->boost_expires_at && $user->boost_expires_at->isFuture();
        @endphp

        <!-- ==================== 1. USER PROFILE HEADER CARD ==================== -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="position-relative">
                            <img src="{{ $photoUrl }}" 
                                 alt="{{ $user->full_name }}" 
                                 class="rounded-circle object-fit-cover shadow border border-3 border-white"
                                 style="width: 80px; height: 80px;"
                                 onerror="this.onerror=null; this.src='{{ asset('assets/images/avatar/default.png') }}';">
                            @if($isVipActive)
                                <span class="position-absolute bottom-0 end-0 bg-warning text-white rounded-circle p-1 d-flex align-items-center justify-content-center shadow" 
                                      style="width: 24px; height: 24px; font-size: 12px;" title="VIP Foydalanuvchi">
                                    👑
                                </span>
                            @endif
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="fw-bold mb-0 text-dark">{{ $user->full_name }}</h4>
                                @if($isVipActive)
                                    <span class="badge bg-warning text-white fs-12 px-2 py-1 shadow-sm">👑 VIP Premium</span>
                                @endif
                                @if($isBoostActive)
                                    <span class="badge bg-danger text-white fs-12 px-2 py-1 shadow-sm">⚡ Boost</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                                @if($user->username)
                                    <a href="https://t.me/{{ $user->username }}" target="_blank" class="badge bg-soft-info text-info text-decoration-none fs-12">
                                        <i class="feather-send me-1"></i> {{ '@' . $user->username }}
                                    </a>
                                @endif
                                @if($user->telegram_id)
                                    <span class="badge bg-soft-secondary text-dark fs-12 cursor-pointer copy-chat-id" 
                                          data-chat-id="{{ $user->telegram_id }}" 
                                          title="Nusxa olish uchun bosing">
                                        <i class="feather-copy me-1"></i> Chat ID: {{ $user->telegram_id }}
                                    </span>
                                @endif
                                <span class="badge {{ $user->status === \App\Enums\Admin\AdminStatusEnum::ACTIVE ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} fs-12">
                                    {{ $user->status === \App\Enums\Admin\AdminStatusEnum::ACTIVE ? 'Faol' : 'Bloklangan' }}
                                </span>
                                <span class="text-muted fs-12 ms-1">
                                    <i class="feather-calendar me-1"></i> Qo'shilgan: {{ format_datetime($user->created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <!-- Bloklash / Faollashtirish -->
                        <button type="button" 
                                class="btn {{ $user->status === \App\Enums\Admin\AdminStatusEnum::ACTIVE ? 'btn-outline-danger' : 'btn-success' }} btn-sm rounded-3 px-3 py-2 btn-toggle-status"
                                data-url="{{ route('admin.users.toggle', $user->id) }}">
                            <i class="feather-shield me-1"></i> {{ $user->status === \App\Enums\Admin\AdminStatusEnum::ACTIVE ? 'Bloklash' : 'Blokdan Chiqarish' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- ==================== 2. CHAP USTUN: RASMLAR VA ANKETA ==================== -->
            <div class="col-12 col-lg-5">
                <!-- Rasmlar Galereyasi -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 px-4 border-0">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="feather-image me-2 text-primary"></i> Suratlar Galereyasi ({{ $user->photos->count() }} ta)
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-2">
                            @forelse($user->photos as $photo)
                                <div class="col-4">
                                    <div class="position-relative rounded-3 overflow-hidden shadow-sm ratio ratio-1x1 border">
                                        <img src="{{ $photo->url }}" 
                                             alt="User Photo" 
                                             class="w-100 h-100 object-fit-cover cursor-pointer"
                                             onclick="openImageModal('{{ $photo->url }}')"
                                             onerror="this.onerror=null; this.src='{{ asset('assets/images/avatar/default.png') }}';">
                                        @if($photo->is_main)
                                            <span class="position-absolute top-0 start-0 bg-primary text-white fs-10 px-2 py-1 rounded-bottom-end">
                                                Asosiy
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4 text-muted">
                                    <i class="feather-camera fs-32 d-block mb-1 text-muted opacity-50"></i>
                                    Suratlar mavjud emas
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Anketa Parametrlari -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 px-4 border-0">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="feather-user-check me-2 text-primary"></i> Anketa Ma'lumotlari
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted fs-13">Ism-familiya:</span>
                                <span class="fw-bold text-dark fs-13">{{ $user->full_name }}</span>
                            </li>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted fs-13">Jinsi:</span>
                                <span class="badge {{ $user->gender?->value === 'male' ? 'bg-soft-primary text-primary' : 'bg-soft-danger text-danger' }} fs-12">
                                    {{ $user->gender?->label() ?? '—' }}
                                </span>
                            </li>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted fs-13">Yoshi:</span>
                                <span class="fw-bold text-dark fs-13">{{ $user->age ? $user->age . ' yosh' : '—' }}</span>
                            </li>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted fs-13">Shahar / Viloyat:</span>
                                <span class="fw-bold text-dark fs-13">
                                    {{ \App\Enums\General\CityEnum::tryFrom($user->city ?? '')?->label() ?? ucfirst($user->city ?? '—') }}
                                </span>
                            </li>
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                <span class="text-muted fs-13">Anketa holati:</span>
                                @if($user->onboarding_completed)
                                    <span class="badge bg-soft-success text-success fs-12">100% To'liq</span>
                                @else
                                    <span class="badge bg-soft-warning text-warning fs-12">Jarayonda</span>
                                @endif
                            </li>
                        </ul>

                        @if($user->bio)
                            <div class="mt-3 pt-3 border-top">
                                <span class="text-muted fs-12 fw-bold text-uppercase d-block mb-1">Bio / Haqida:</span>
                                <p class="mb-0 fs-13 text-dark bg-light p-3 rounded-3 fst-italic">
                                    "{{ $user->bio }}"
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ==================== 3. O'NG USTUN: XIZMATLAR, TO'LOVLAR VA TARIX ==================== -->
            <div class="col-12 col-lg-7">
                <!-- Xizmatlar & Obunalar Holati -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="feather-award me-2 text-warning"></i> Faol Xizmatlar & Obunalar Boshqaruvi
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-3">
                            <!-- 1. Obuna (VIP) Box -->
                            <div class="col-12 col-md-6">
                                <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between {{ $isVipActive ? 'bg-soft-warning border border-warning' : 'bg-light border' }}">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold fs-14 {{ $isVipActive ? 'text-warning' : 'text-dark' }}">
                                                👑 Obuna (VIP)
                                            </span>
                                            <span class="badge {{ $isVipActive ? 'bg-warning text-white' : 'bg-secondary' }}">
                                                {{ $isVipActive ? 'Faol' : 'Nofaol' }}
                                            </span>
                                        </div>
                                        <div class="fs-12 text-muted mb-3">
                                            @if($isVipActive)
                                                Muddati: <b>{{ $user->vip_expires_at ? format_datetime($user->vip_expires_at) : 'Cheksiz (Doimiy)' }}</b>
                                            @else
                                                Foydalanuvchida faol VIP obuna mavjud emas
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        @if($isVipActive)
                                            <button type="button" 
                                                    class="btn btn-warning btn-sm text-dark rounded-3 px-3 py-1 flex-grow-1 fw-bold btn-open-sub-modal">
                                                <i class="feather-edit-2 me-1"></i> O'zgartirish
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm rounded-3 px-2 py-1 btn-revoke-sub" 
                                                    title="Obunani bekor qilish">
                                                <i class="feather-x"></i>
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-warning btn-sm text-dark rounded-3 w-100 py-2 fw-bold btn-open-sub-modal">
                                                <i class="feather-plus-circle me-1"></i> Obuna (VIP) Berish
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Boost Box -->
                            <div class="col-12 col-md-6">
                                <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between {{ $isBoostActive ? 'bg-soft-danger border border-danger' : 'bg-light border' }}">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold fs-14 {{ $isBoostActive ? 'text-danger' : 'text-dark' }}">
                                                ⚡ Boost Xizmati
                                            </span>
                                            <span class="badge {{ $isBoostActive ? 'bg-danger text-white' : 'bg-secondary' }}">
                                                {{ $isBoostActive ? 'Faol' : 'Nofaol' }}
                                            </span>
                                        </div>
                                        <div class="fs-12 text-muted mb-3">
                                            @if($isBoostActive)
                                                Muddati: <b>{{ format_datetime($user->boost_expires_at) }}</b>
                                            @else
                                                Foydalanuvchida faol Boost mavjud emas
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        @if($isBoostActive)
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm text-white rounded-3 px-3 py-1 flex-grow-1 fw-bold btn-open-boost-modal">
                                                <i class="feather-edit-2 me-1"></i> Uzaytirish
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm rounded-3 px-2 py-1 btn-revoke-boost" 
                                                    title="Boostni to'xtatish">
                                                <i class="feather-x"></i>
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm text-white rounded-3 w-100 py-2 fw-bold btn-open-boost-modal">
                                                <i class="feather-zap me-1"></i> Boost Ulash
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- To'lovlar Tarixi (Payments) -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="feather-dollar-sign me-2 text-success"></i> To'lovlar Tarixi ({{ $user->payments->count() }} ta)
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fs-12 text-muted">#ID</th>
                                    <th class="fs-12 text-muted">Kategoriya</th>
                                    <th class="fs-12 text-muted">Summa</th>
                                    <th class="fs-12 text-muted text-center">Holati</th>
                                    <th class="fs-12 text-muted text-end">Sana</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->payments as $payment)
                                    <tr>
                                        <td class="fw-bold fs-12">#{{ $payment->id }}</td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary fs-11">
                                                {{ $payment->incomeCategory?->name ?? 'Xizmat' }}
                                            </span>
                                        </td>
                                        <td class="fw-bold fs-13 text-dark">
                                            {{ format_price($payment->amount) }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $payment->status?->badgeClass() ?? 'bg-secondary' }} fs-11">
                                                {{ $payment->status?->label() ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="text-end text-muted fs-12">
                                            {{ format_datetime($payment->created_at) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted fs-13">
                                            To'lovlar tarixi mavjud emas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Obunalar va Boostlar Tarixi -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="feather-history me-2 text-primary"></i> Obunalar va Boostlar Tarixi
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <ul class="nav nav-pills mb-3" id="services-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active btn-sm rounded-3 py-2 px-3" id="subs-tab" data-bs-toggle="pill" data-bs-target="#subs-pane" type="button" role="tab">
                                    Obunalar ({{ $user->subscriptions->count() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link btn-sm rounded-3 py-2 px-3" id="boosts-tab" data-bs-toggle="pill" data-bs-target="#boosts-pane" type="button" role="tab">
                                    Boostlar ({{ $user->boosts->count() }})
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="services-tab-content">
                            <!-- Obunalar -->
                            <div class="tab-pane fade show active" id="subs-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr class="text-muted fs-12">
                                                <th>Tarif</th>
                                                <th>Boshlanish</th>
                                                <th>Tugash</th>
                                                <th class="text-end">Holati</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($user->subscriptions as $sub)
                                                <tr>
                                                    <td class="fw-bold fs-12">{{ $sub->plan?->name ?? 'Tarif' }}</td>
                                                    <td class="fs-12">{{ format_datetime($sub->starts_at) }}</td>
                                                    <td class="fs-12">{{ format_datetime($sub->ends_at) }}</td>
                                                    <td class="text-end">
                                                        <span class="badge {{ $sub->status?->badgeClass() ?? 'bg-secondary' }} fs-11">
                                                            {{ $sub->status?->label() ?? '—' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted fs-12">Obuna tarixi mavjud emas</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Boostlar -->
                            <div class="tab-pane fade" id="boosts-pane" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead>
                                            <tr class="text-muted fs-12">
                                                <th>Paket</th>
                                                <th>Boshlanish</th>
                                                <th>Tugash</th>
                                                <th class="text-end">Holati</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($user->boosts as $boost)
                                                <tr>
                                                    <td class="fw-bold fs-12">{{ $boost->plan?->name ?? 'Boost' }}</td>
                                                    <td class="fs-12">{{ format_datetime($boost->starts_at) }}</td>
                                                    <td class="fs-12">{{ format_datetime($boost->ends_at) }}</td>
                                                    <td class="text-end">
                                                        <span class="badge {{ $boost->status?->badgeClass() ?? 'bg-secondary' }} fs-11">
                                                            {{ $boost->status?->label() ?? '—' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted fs-12">Boost tarixi mavjud emas</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rasm Ko'rish Modali -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 bg-transparent shadow-none">
            <div class="modal-body text-center p-0 position-relative">
                <button type="button" class="btn btn-light btn-sm rounded-circle position-absolute top-0 end-0 m-3 shadow" data-bs-dismiss="modal">
                    <i class="feather-x"></i>
                </button>
                <img src="" id="modalPreviewImg" class="img-fluid rounded-4 shadow-lg" style="max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    (function ($) {
        'use strict';

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });

        // 1. Chat ID nusxa olish
        $(document).on('click', '.copy-chat-id', function () {
            const chatId = $(this).data('chat-id');
            navigator.clipboard.writeText(chatId).then(() => {
                Toast.fire({
                    icon: 'success',
                    title: `Chat ID (${chatId}) nusxalandi!`
                });
            });
        });

        // 2. Rasm ko'rish modali
        window.openImageModal = function (url) {
            $('#modalPreviewImg').attr('src', url);
            new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
        };

        // 3. Status o'zgartirish (Bloklash / Faollashtirish)
        $(document).on('click', '.btn-toggle-status', function () {
            const url = $(this).data('url');
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
                        title: response.data.message
                    });
                    setTimeout(() => window.location.reload(), 600);
                }
            })
            .catch(function (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Xatolik',
                    text: error.response?.data?.message || 'Xatolik yuz berdi'
                });
            });
        });

        // 4. OBUNA (VIP) BERISH / O'ZGARTIRISH MODALI
        $(document).on('click', '.btn-open-sub-modal', function () {
            const grantUrl = "{{ route('admin.users.grant-subscription', $user->id) }}";
            const hasPlans = {{ $subscriptionPlans->isNotEmpty() ? 'true' : 'false' }};

            if (!hasPlans) {
                Swal.fire({
                    icon: 'info',
                    title: 'Obuna tariflari mavjud emas',
                    text: "Tizimda faol obuna tariflari topilmadi. Iltimos, avval 'Obuna Tariflari' bo'limidan tarif qo'shing.",
                    confirmButtonText: 'Tushunarli',
                    customClass: {
                        popup: 'rounded-4 shadow-lg',
                        confirmButton: 'btn btn-primary px-4 py-2 rounded-3'
                    },
                    buttonsStyling: false
                });
                return;
            }

            Swal.fire({
                title: `👑 Obuna (VIP) Berish`,
                html: `
                    <p class="text-muted fs-13 mb-3"><b>{{ $user->full_name }}</b> uchun tizimdagi obuna tariflaridan birini tanlang:</p>
                    <div class="text-start mb-3">
                        <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Obuna Tarifi</label>
                        <select id="swal-sub-select" class="form-select rounded-3">
                            @foreach($subscriptionPlans as $plan)
                                <option value="{{ $plan->id }}">
                                    👑 {{ $plan->title ?? $plan->name }} ({{ $plan->days }} kun) — {{ format_price($plan->price) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: "👑 Obunani Tasdiqlash",
                cancelButtonText: "Bekor qilish",
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'btn btn-warning text-dark px-4 py-2 rounded-3 me-2 fw-bold',
                    cancelButton: 'btn btn-light px-4 py-2 rounded-3'
                },
                buttonsStyling: false,
                preConfirm: () => {
                    const planId = document.getElementById('swal-sub-select').value;
                    if (!planId) {
                        Swal.showValidationMessage('Iltimos, obuna tarifini tanlang!');
                        return false;
                    }
                    return { plan_id: planId };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    axios.post(grantUrl, result.value, {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function (response) {
                        if (response.data && response.data.success) {
                            Toast.fire({ icon: 'success', title: response.data.message });
                            setTimeout(() => window.location.reload(), 600);
                        }
                    })
                    .catch(function (error) {
                        Swal.fire({ icon: 'error', title: 'Xatolik', text: error.response?.data?.message || 'Xatolik yuz berdi' });
                    });
                }
            });
        });

        // 5. OBUNANI (VIP) BEKOR QILISH
        $(document).on('click', '.btn-revoke-sub', function () {
            const revokeUrl = "{{ route('admin.users.revoke-subscription', $user->id) }}";

            Swal.fire({
                title: "Obunani bekor qilmoqchimisiz?",
                text: "{{ $user->full_name }} ning VIP obunasi to'xtatiladi.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Ha, bekor qilinsin!",
                cancelButtonText: "Orqaga",
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'btn btn-danger px-4 py-2 rounded-3 me-2',
                    cancelButton: 'btn btn-light px-4 py-2 rounded-3'
                },
                buttonsStyling: false
            }).then((result) => {
                const isConfirmed = result === true || (result && (result.isConfirmed === true || result.value === true));
                if (isConfirmed) {
                    axios.post(revokeUrl, {}, {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function (response) {
                        if (response.data && response.data.success) {
                            Toast.fire({ icon: 'success', title: response.data.message });
                            setTimeout(() => window.location.reload(), 600);
                        }
                    })
                    .catch(function (error) {
                        Swal.fire({ icon: 'error', title: 'Xatolik', text: error.response?.data?.message || 'Xatolik yuz berdi' });
                    });
                }
            });
        });

        // 6. BOOST ULASH / UZAYTIRISH MODALI
        $(document).on('click', '.btn-open-boost-modal', function () {
            const grantUrl = "{{ route('admin.users.grant-boost', $user->id) }}";
            const hasPlans = {{ $boostPlans->isNotEmpty() ? 'true' : 'false' }};

            if (!hasPlans) {
                Swal.fire({
                    icon: 'info',
                    title: 'Boost paketlari mavjud emas',
                    text: "Tizimda faol Boost paketlari topilmadi. Iltimos, avval 'Boost' bo'limidan paket qo'shing.",
                    confirmButtonText: 'Tushunarli',
                    customClass: {
                        popup: 'rounded-4 shadow-lg',
                        confirmButton: 'btn btn-primary px-4 py-2 rounded-3'
                    },
                    buttonsStyling: false
                });
                return;
            }

            Swal.fire({
                title: `⚡ Boost Ulash`,
                html: `
                    <p class="text-muted fs-13 mb-3"><b>{{ $user->full_name }}</b> uchun tizimdagi Boost paketlaridan birini tanlang:</p>
                    <div class="text-start mb-3">
                        <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">Boost Paketi</label>
                        <select id="swal-boost-select" class="form-select rounded-3">
                            @foreach($boostPlans as $plan)
                                <option value="{{ $plan->id }}">
                                    ⚡ {{ $plan->title ?? $plan->name }} ({{ $plan->hours }} soat) — {{ format_price($plan->price) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: "⚡ Boostni Faollashtirish",
                cancelButtonText: "Bekor qilish",
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'btn btn-danger px-4 py-2 rounded-3 me-2 fw-bold',
                    cancelButton: 'btn btn-light px-4 py-2 rounded-3'
                },
                buttonsStyling: false,
                preConfirm: () => {
                    const planId = document.getElementById('swal-boost-select').value;
                    if (!planId) {
                        Swal.showValidationMessage('Iltimos, Boost paketini tanlang!');
                        return false;
                    }
                    return { plan_id: planId };
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    axios.post(grantUrl, result.value, {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function (response) {
                        if (response.data && response.data.success) {
                            Toast.fire({ icon: 'success', title: response.data.message });
                            setTimeout(() => window.location.reload(), 600);
                        }
                    })
                    .catch(function (error) {
                        Swal.fire({ icon: 'error', title: 'Xatolik', text: error.response?.data?.message || 'Xatolik yuz berdi' });
                    });
                }
            });
        });

        // 7. BOOSTNI TO'XTATISH
        $(document).on('click', '.btn-revoke-boost', function () {
            const revokeUrl = "{{ route('admin.users.revoke-boost', $user->id) }}";

            Swal.fire({
                title: "Boostni to'xtatmoqchimisiz?",
                text: "{{ $user->full_name }} ning Boost xizmati muddatidan oldin yakunlanadi.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Ha, to'xtatilsin!",
                cancelButtonText: "Orqaga",
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'btn btn-danger px-4 py-2 rounded-3 me-2',
                    cancelButton: 'btn btn-light px-4 py-2 rounded-3'
                },
                buttonsStyling: false
            }).then((result) => {
                const isConfirmed = result === true || (result && (result.isConfirmed === true || result.value === true));
                if (isConfirmed) {
                    axios.post(revokeUrl, {}, {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function (response) {
                        if (response.data && response.data.success) {
                            Toast.fire({ icon: 'success', title: response.data.message });
                            setTimeout(() => window.location.reload(), 600);
                        }
                    })
                    .catch(function (error) {
                        Swal.fire({ icon: 'error', title: 'Xatolik', text: error.response?.data?.message || 'Xatolik yuz berdi' });
                    });
                }
            });
        });
    })(jQuery);
</script>
@endpush
