@extends('admin.layouts.app')

@section('title', $label ?? 'Obunalar')

@section('content')
@php
    $model = $model ?? new \App\Models\SubscriptionPlan();
    $isCreate = !isset($method) || strtoupper($method) === 'POST';
    $label = $label ?? ($isCreate ? 'Yangi Obuna Yaratish' : 'Obunani Tahrirlash');
    
    $currentStatus = $model->status instanceof \App\Enums\Subscription\SubscriptionStatusEnum 
        ? $model->status->value 
        : ($model->status ?: ($model->is_active ? 'active' : 'active'));

    $currentPeriodType = $model->period_type instanceof \App\Enums\Subscription\SubscriptionPeriodTypeEnum 
        ? $model->period_type->value 
        : ($model->period_type ?: 'month');
@endphp

@include('admin.pages.subscriptions._breadcrumb', [
    'title' => $label,
    'label' => $label,
    'isIndex' => false,
])

<!-- [ Main Content ] start -->
<div class="main-content">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="feather-alert-circle me-2"></i>
            <strong>Xatolik yuz berdi!</strong> Iltimos, maydonlarni tekshiring:
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title mb-0">Obuna Tarifi Parametrlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ $route }}" method="POST">
                        @csrf
                        @if (isset($method) && strtoupper($method) !== 'POST')
                            @method($method)
                        @endif

                        <div class="row g-3">
                            <!-- Sarlavha (Title) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tarif Nomi / Sarlavhasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-type"></i></span>
                                    <input type="text" name="title" value="{{ old('title', $model->title) }}" class="form-control" placeholder="Masalan: MatchMe Premium 1 Oylik" required>
                                </div>
                                <small class="text-muted">Mini-app va admin ro'yxatida ko'rinadigan asosiy nom</small>
                            </div>

                            <!-- Davomiyligi (Period: son + tur) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Amal Qilish Davri (Muddat) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-clock"></i></span>
                                    <input type="number" name="period_count" value="{{ old('period_count', $model->period_count ?? 1) }}" min="1" max="365" class="form-control" placeholder="1" style="max-width: 120px;" required>
                                    <select name="period_type" class="form-select" required>
                                        @foreach (\App\Enums\Subscription\SubscriptionPeriodTypeEnum::cases() as $periodEnum)
                                            <option value="{{ $periodEnum->value }}" {{ old('period_type', $currentPeriodType) == $periodEnum->value ? 'selected' : '' }}>
                                                {{ $periodEnum->label() }} ({{ $periodEnum->toDays(1) }} kun)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="text-muted">Masalan: 7 kun, 1 oy, 3 oy yoki 1 yil</small>
                            </div>

                            <!-- Sotuv Narxi (UZS) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sotuv Narxi (UZS) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-dollar-sign"></i></span>
                                    <input type="number" name="price" value="{{ old('price', $model->price ? (int)$model->price : '') }}" min="0" step="500" class="form-control" placeholder="30000" required>
                                    <span class="input-group-text">UZS</span>
                                </div>
                            </div>

                            <!-- Asl Narxi (UZS) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Asl Narxi (Chegirmagacha) <span class="text-muted">(Ixtiyoriy)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-tag"></i></span>
                                    <input type="number" name="original_price" value="{{ old('original_price', $model->original_price ? (int)$model->original_price : '') }}" min="0" step="500" class="form-control" placeholder="45000">
                                    <span class="input-group-text">UZS</span>
                                </div>
                                <small class="text-muted">Chizilgan holatda ko'rsatish uchun</small>
                            </div>

                            <!-- Ikonka -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ikonka (Emoji / Belgisi)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-smile"></i></span>
                                    <input type="text" name="icon" value="{{ old('icon', $model->icon ?: '👑') }}" maxlength="20" class="form-control" placeholder="👑">
                                </div>
                                <small class="text-muted">Masalan: 👑, ⭐, 💎, 🔥</small>
                            </div>

                            <!-- Nishon (Badge) -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Nishon (Badge) <span class="text-muted">(Ixtiyoriy)</span></label>
                                <input type="text" name="badge" value="{{ old('badge', $model->badge) }}" class="form-control" placeholder="Masalan: MASHHUR 🔥, -50% TEJAM 🤑">
                            </div>

                            <!-- Status -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @foreach (\App\Enums\Subscription\SubscriptionStatusEnum::cases() as $statusEnum)
                                        <option value="{{ $statusEnum->value }}" {{ old('status', $currentStatus) == $statusEnum->value ? 'selected' : '' }}>
                                            {{ $statusEnum->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tartib raqami -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tartib Raqami</label>
                                <input type="number" name="order" value="{{ old('order', $model->order ?? 0) }}" class="form-control" placeholder="0">
                                <small class="text-muted">0 - eng birinchi bo'lib chiqadi</small>
                            </div>

                            <!-- Qisqacha Tavsif -->
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Qisqacha Tavsif (Description)</label>
                                <input type="text" name="description" value="{{ old('description', $model->description) }}" class="form-control" placeholder="Cheklovlarsiz to'liq Premium imkoniyatlar...">
                            </div>

                            <!-- Tugmalar -->
                            <div class="col-md-12 pt-4 d-flex align-items-center justify-content-end gap-2 border-top">
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-light">Bekor qilish</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather-check-circle me-1"></i> {{ $label }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection
