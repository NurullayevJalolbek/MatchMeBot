@extends('admin.layouts.app')

@section('title', $label ?? 'Boost')

@section('content')
@php
    $model = $model ?? new \App\Models\BoostPlan();
    $isCreate = !isset($method) || strtoupper($method) === 'POST';
    $label = $label ?? ($isCreate ? 'Yangi Boost Yaratish' : 'Boostni Tahrirlash');
    $currentStatus = $model->status instanceof \App\Enums\Boost\BoostStatusEnum 
        ? $model->status->value 
        : ($model->status ?: ($model->is_active ? 'active' : 'active'));
@endphp

@include('admin.pages.boosts._breadcrumb', [
    'title' => $label,
    'label' => $label,
    'isIndex' => false,
])

<!-- [ Main Content ] start -->
<div class="main-content">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="feather-alert-circle me-2"></i>
            <strong>Xatolik yuz berdi!</strong> Iltimos, quyidagi maydonlarni tekshiring:
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
                    <h5 class="card-title mb-0">Boost Parametrlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ $route }}" method="POST">
                        @csrf
                        @if (isset($method) && strtoupper($method) !== 'POST')
                            @method($method)
                        @endif

                        <div class="row g-3">
                            <!-- Boost Sarlavhasi (Title) -->
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Boost Sarlavhasi / Nomi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-type"></i></span>
                                    <input type="text" name="title" value="{{ old('title', $model->title ?: $model->name) }}" class="form-control" placeholder="Masalan: 1 Soatlik Tezkor Boost" required>
                                </div>
                                <small class="text-muted">Mini-app va ro'yxatda ko'rinadigan asosiy nom</small>
                            </div>

                            <!-- Davomiyligi (Soat) -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Davomiyligi (Soat) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-clock"></i></span>
                                    <input type="number" name="hours" value="{{ old('hours', $model->hours ?? 1) }}" min="1" max="720" class="form-control" placeholder="1" required>
                                    <span class="input-group-text">soat</span>
                                </div>
                                <small class="text-muted">Faqat son kiriting (1, 2, 5, 24)</small>
                            </div>

                            <!-- Sotuv Narxi (UZS) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sotuv Narxi (UZS) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-dollar-sign"></i></span>
                                    <input type="number" name="price" value="{{ old('price', $model->price ? (int)$model->price : '') }}" min="0" step="500" class="form-control" placeholder="10000" required>
                                    <span class="input-group-text">UZS</span>
                                </div>
                            </div>

                            <!-- Asl Narxi (UZS) Chegirma uchun -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Asl Narxi (Chegirmagacha) <span class="text-muted">(Ixtiyoriy)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-tag"></i></span>
                                    <input type="number" name="original_price" value="{{ old('original_price', $model->original_price ? (int)$model->original_price : '') }}" min="0" step="500" class="form-control" placeholder="15000">
                                    <span class="input-group-text">UZS</span>
                                </div>
                                <small class="text-muted">Chizilgan holatda ko'rsatish uchun</small>
                            </div>

                            <!-- Ikonka -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ikonka (Emoji / Belgisi)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-smile"></i></span>
                                    <input type="text" name="icon" value="{{ old('icon', $model->icon ?: '⚡') }}" maxlength="20" class="form-control" placeholder="⚡">
                                </div>
                                <small class="text-muted">Masalan: ⚡, 🚀, 🔥, 💎, 👑</small>
                            </div>

                            <!-- Nishon (Badge) -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Nishon (Badge) <span class="text-muted">(Ixtiyoriy)</span></label>
                                <input type="text" name="badge" value="{{ old('badge', $model->badge) }}" class="form-control" placeholder="Masalan: Mashhur, Super, VIP">
                            </div>

                            <!-- Status -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @foreach (\App\Enums\Boost\BoostStatusEnum::cases() as $statusEnum)
                                        <option value="{{ $statusEnum->value }}" {{ old('status', $currentStatus) == $statusEnum->value ? 'selected' : '' }}>
                                            {{ $statusEnum->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tushum Kategoriyasi -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tushum Kategoriyasi (Moliya hisoboti uchun)</label>
                                <select name="income_category_id" class="form-select">
                                    <option value="">-- Kategoriyani tanlang --</option>
                                    @if(isset($incomeCategories))
                                        @foreach($incomeCategories as $parentCat)
                                            <optgroup label="{{ $parentCat->icon }} {{ $parentCat->name }}">
                                                <option value="{{ $parentCat->id }}" {{ old('income_category_id', $model->income_category_id) == $parentCat->id ? 'selected' : '' }}>
                                                    {{ $parentCat->icon }} {{ $parentCat->name }} (Asosiy Guruh)
                                                </option>
                                                @foreach($parentCat->children as $childCat)
                                                    <option value="{{ $childCat->id }}" {{ old('income_category_id', $model->income_category_id) == $childCat->id ? 'selected' : '' }}>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;↳ {{ $childCat->icon }} {{ $childCat->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    @endif
                                </select>
                                <small class="text-muted">Statistika va moliya bo'limida hisobga olinadi</small>
                            </div>

                            <!-- Tartib raqami -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tartib Raqami</label>
                                <input type="number" name="order" value="{{ old('order', $model->order ?? 0) }}" class="form-control" placeholder="0">
                                <small class="text-muted">0 - eng birinchi bo'lib chiqadi</small>
                            </div>

                            <!-- Tavsif / Izoh -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Qisqacha Tavsif (Description)</label>
                                <textarea name="description" rows="3" class="form-control" placeholder="Foydalanuvchilarga ushbu rejaning foydasi haqida ma'lumot...">{{ old('description', $model->description ?: $model->subtitle) }}</textarea>
                            </div>

                            <!-- Tugmalar -->
                            <div class="col-md-12 pt-3 d-flex align-items-center justify-content-end gap-2 border-top">
                                <a href="{{ route('admin.boosts.index') }}" class="btn btn-light">Bekor qilish</a>
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
