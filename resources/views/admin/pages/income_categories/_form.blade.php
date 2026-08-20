@extends('admin.layouts.app')

@section('title', $label ?? 'Tushum Kategoriyalari')

@section('content')
@php
    $model = $model ?? new \App\Models\IncomeCategory();
    $isCreate = !isset($method) || strtoupper($method) === 'POST';
    $label = $label ?? ($isCreate ? 'Yangi Tushum Kategoriyasi' : 'Tushum Kategoriyasini Tahrirlash');
    
    $currentStatus = $model->status instanceof \App\Enums\Finance\FinanceStatusEnum 
        ? $model->status->value 
        : ($model->status ?: 'active');

    $activeParent = $parentCategory ?? $model->parent;
@endphp

@include('admin.pages.income_categories._breadcrumb', [
    'title' => $label,
    'label' => $label,
    'parentCategory' => $activeParent,
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
                    <h5 class="card-title mb-0">
                        @if($activeParent)
                            "{{ $activeParent->name }}" ichiga yangi bo'lim qo'shish
                        @else
                            Tushum Kategoriyasi Parametrlari
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ $route }}" method="POST">
                        @csrf
                        @if (isset($method) && strtoupper($method) !== 'POST')
                            @method($method)
                        @endif

                        <!-- Hidden parent_id -->
                        <input type="hidden" name="parent_id" value="{{ old('parent_id', $parentId ?? $model->parent_id) }}">

                        @if($activeParent)
                            <div class="alert alert-soft-primary d-flex align-items-center mb-4 rounded-3">
                                <span class="fs-4 me-3">{{ $activeParent->icon ?: '💎' }}</span>
                                <div>
                                    <strong class="d-block text-primary">Ota Kategoriya: {{ $activeParent->name }}</strong>
                                    <small class="text-muted">Yaratilayotgan kategoriya ushbu guruhning ichki bo'limi (bola kategoriyasi) bo'ladi.</small>
                                </div>
                            </div>
                        @endif

                        <div class="row g-3">
                            <!-- Kategoriya Nomi -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategoriya Nomi <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $model->name) }}" class="form-control" placeholder="Masalan: {{ $activeParent ? '1 Oylik VIP Paket' : 'Premium Obunalar' }}" required>
                            </div>

                            <!-- Ikonka / Emoji -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ikonka (Emoji)</label>
                                <div class="input-group">
                                    <input type="text" name="icon" id="category-icon-input" value="{{ old('icon', $model->icon ?: ($activeParent ? '⭐' : '💎')) }}" class="form-control text-center fs-4" placeholder="💎" maxlength="10">
                                    <div class="btn-group dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Tanlash
                                        </button>
                                        <div class="dropdown-menu p-2 dropdown-menu-end" style="min-width: 200px;">
                                            <div class="d-flex flex-wrap gap-2 fs-4 justify-content-center">
                                                @foreach(['💎','👑','⭐','⚡','🚀','🔥','🌟','💳','🟢','🔵','🟣','🎁','🪙','🌹','📢','📱','🤝','💰','🎯','✨'] as $emoji)
                                                    <a href="javascript:void(0);" class="p-1 emoji-pick text-decoration-none rounded hover-bg-light">{{ $emoji }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">Kategoriya oldida ko'rinadigan belgi</small>
                            </div>

                            <!-- Tartib Raqami -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tartib Raqami</label>
                                <input type="number" name="order" value="{{ old('order', $model->order ?? 0) }}" class="form-control" min="0" placeholder="0">
                                <small class="text-muted">Kichik raqamlar yuqoriroqda chiqadi</small>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @foreach (\App\Enums\Finance\FinanceStatusEnum::cases() as $statusEnum)
                                        <option value="{{ $statusEnum->value }}" {{ old('status', $currentStatus) == $statusEnum->value ? 'selected' : '' }}>
                                            {{ $statusEnum->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tavsif -->
                            <div class="col-12">
                                <label class="form-label fw-bold">Batafsil Tavsifi</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Ushbu tushum kategoriyasiga nimalar kirishi haqida qisqacha izoh...">{{ old('description', $model->description) }}</textarea>
                            </div>

                            <!-- Tugmalar -->
                            <div class="col-12 pt-3 d-flex align-items-center justify-content-end gap-2 border-top">
                                <a href="{{ route('admin.income-categories.index', ['parent_id' => $parentId ?? ($activeParent?->id)]) }}" class="btn btn-light border-0">Bekor qilish</a>
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

@push('js')
<script>
    (function ($) {
        'use strict';

        // Emoji picker
        $(document).on('click', '.emoji-pick', function (e) {
            e.preventDefault();
            const emoji = $(this).text().trim();
            $('#category-icon-input').val(emoji);
        });
    })(jQuery);
</script>
@endpush
