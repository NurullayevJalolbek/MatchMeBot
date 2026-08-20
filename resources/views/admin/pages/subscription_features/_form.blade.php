@extends('admin.layouts.app')

@section('title', $label ?? 'Obuna Afzalligi')

@section('content')
@php
    $model = $model ?? new \App\Models\SubscriptionFeature();
    $isCreate = !isset($method) || strtoupper($method) === 'POST';
    $label = $label ?? ($isCreate ? 'Yangi Afzallik Qo\'shish' : 'Afzallikni Tahrirlash');
    
    $currentStatus = $model->status instanceof \App\Enums\Subscription\SubscriptionStatusEnum 
        ? $model->status->value 
        : ($model->status ?: ($model->is_active ? 'active' : 'active'));
@endphp

@include('admin.pages.subscription_features._breadcrumb', [
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
                    <h5 class="card-title mb-0">Afzallik Parametrlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($method) && strtoupper($method) !== 'POST')
                            @method($method)
                        @endif

                        <div class="row g-3">
                            <!-- Sarlavha (Title) -->
                            <div class="col-md-7">
                                <label class="form-label fw-bold">Afzallik Nomi (Sarlavha) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-type"></i></span>
                                    <input type="text" name="title" value="{{ old('title', $model->title) }}" class="form-control" placeholder="Masalan: Cheksiz Layklar (Unlimited Likes)" required>
                                </div>
                                <small class="text-muted">Foydalanuvchi mini-ilovasida ko'rinadigan qisqa sarlavha</small>
                            </div>

                            <!-- Tartib raqami -->
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Tartib Raqami</label>
                                <input type="number" name="order" value="{{ old('order', $model->order ?? 0) }}" class="form-control" placeholder="0">
                                <small class="text-muted">0 - birinchi bo'lib chiqadi</small>
                            </div>

                            <!-- Status -->
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @foreach (\App\Enums\Subscription\SubscriptionStatusEnum::cases() as $statusEnum)
                                        <option value="{{ $statusEnum->value }}" {{ old('status', $currentStatus) == $statusEnum->value ? 'selected' : '' }}>
                                            {{ $statusEnum->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Ikonka / Rasm Yuklash -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Ikonka / Rasm Yuklash (PNG, SVG, JPG, WEBP)</label>
                                <div class="d-flex align-items-center gap-3 p-3 border rounded-3 bg-light">
                                    <div class="icon-preview-box rounded-3 border bg-white d-flex align-items-center justify-content-center overflow-hidden" style="width: 60px; height: 60px; flex-shrink: 0;">
                                        @if ($model->icon)
                                            <img id="icon-preview-img" src="{{ asset($model->icon) }}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        @else
                                            <img id="icon-preview-img" src="" alt="Preview" style="display: none; max-width: 100%; max-height: 100%; object-fit: contain;">
                                            <i id="icon-placeholder-icon" class="feather-image fs-3 text-muted"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="icon_file" id="icon-file-input" class="form-control" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                                        <small class="text-muted">Tavsiya etilgan hajm: 64x64px yoki SVG vektor formati (Maksimal: 2MB)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Batafsil Tavsif (Description) -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Batafsil Tavsifi (Description)</label>
                                <textarea name="description" rows="3" class="form-control" placeholder="Foydalanuvchiga ushbu afzallik beradigan imkoniyat haqida qisqacha ma'lumot...">{{ old('description', $model->description) }}</textarea>
                            </div>

                            <!-- Tugmalar -->
                            <div class="col-md-12 pt-3 d-flex align-items-center justify-content-end gap-2 border-top">
                                <a href="{{ route('admin.subscription-features.index') }}" class="btn btn-light">Bekor qilish</a>
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

        // Real-time image preview
        $('#icon-file-input').on('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    $('#icon-preview-img').attr('src', event.target.result).show();
                    $('#icon-placeholder-icon').hide();
                };
                reader.readAsDataURL(file);
            }
        });
    })(jQuery);
</script>
@endpush
