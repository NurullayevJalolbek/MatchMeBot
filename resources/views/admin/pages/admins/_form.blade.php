@extends('admin.layouts.app')

@section('title', $label ?? 'Adminlar')

@section('content')
@php
    $model = $model ?? new \App\Models\User();
    $isCreate = !isset($method) || strtoupper($method) === 'POST';
    $label = $label ?? ($isCreate ? 'Yangi Admin Qo\'shish' : 'Admin Ma\'lumotlarini Tahrirlash');
    
    $currentStatus = $model->status instanceof \App\Enums\Admin\AdminStatusEnum 
        ? $model->status->value 
        : ($model->status ?: 'active');
@endphp

@include('admin.pages.admins._breadcrumb', [
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
                    <h5 class="card-title mb-0">Administrator Ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ $route }}" method="POST" autocomplete="off">
                        @csrf
                        @if (isset($method) && strtoupper($method) !== 'POST')
                            @method($method)
                        @endif

                        <!-- Fake hidden inputs to strictly disable browser autofill -->
                        <input style="display:none" type="text" name="fakeusernameremembered"/>
                        <input style="display:none" type="password" name="fakepasswordremembered"/>

                        <div class="row g-3">
                            <!-- Ism-familiya -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ism-familiya <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-user"></i></span>
                                    <input type="text" name="name" value="{{ old('name', $model->name) }}" class="form-control" placeholder="Masalan: Jalolbek Nurullayev" required autocomplete="off">
                                </div>
                            </div>

                            <!-- Username -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Foydalanuvchi Nomi (Username) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-at-sign"></i></span>
                                    <input type="text" name="username" value="{{ old('username', $model->username) }}" class="form-control" placeholder="admin_jalol" required autocomplete="off">
                                </div>
                                <small class="text-muted">Tizimga kirish uchun ishlatiladi</small>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email Manzili <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-mail"></i></span>
                                    <input type="email" name="email" value="{{ old('email', $model->email) }}" class="form-control" placeholder="admin@matchme.uz" required autocomplete="off">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @foreach (\App\Enums\Admin\AdminStatusEnum::cases() as $statusEnum)
                                        <option value="{{ $statusEnum->value }}" {{ old('status', $currentStatus) == $statusEnum->value ? 'selected' : '' }}>
                                            {{ $statusEnum->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Parol Sozlamalari Bo'limi -->
                            <div class="col-12 mt-4 pt-3 border-top">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-0">
                                            <i class="feather-lock text-primary me-1"></i>
                                            {{ $isCreate ? 'Xavfsizlik va Parol O\'rnatish' : 'Parolni Yangilash' }}
                                        </h6>
                                        <small class="text-muted">
                                            {{ $isCreate ? 'Yangi administrator uchun kamida 6 ta belgidan iborat kuchli parol o\'rnating' : 'Agar parolni o\'zgartirishni istamasangiz, ushbu maydonni bo\'sh qoldiring' }}
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-light-brand" id="btn-generate-password">
                                        <i class="feather-refresh-cw me-1"></i> Parol Generatsiya Qilish
                                    </button>
                                </div>

                                <div class="row g-2 mt-1">
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="feather-key"></i></span>
                                            <input type="password" name="password" id="admin-password-input" class="form-control" placeholder="{{ $isCreate ? 'Yangi parolni kiriting...' : 'Yangi parolni kiriting (ixtiyoriy)...' }}" autocomplete="new-password" {{ $isCreate ? 'required' : '' }}>
                                            <button class="btn btn-outline-secondary" type="button" id="btn-toggle-password" title="Parolni ko'rsatish/yashirish">
                                                <i class="feather-eye" id="toggle-password-icon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-center">
                                        <span id="password-strength-text" class="fs-12 text-muted"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tugmalar -->
                            <div class="col-md-12 pt-4 d-flex align-items-center justify-content-end gap-2 border-top">
                                <a href="{{ route('admin.admins.index') }}" class="btn btn-light">Bekor qilish</a>
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

        // Clear password input value on initial load if browser auto-filled it in edit mode
        @if(!$isCreate)
            setTimeout(function() {
                const passInput = document.getElementById('admin-password-input');
                if (passInput) {
                    passInput.value = '';
                }
            }, 100);
        @endif

        // Toggle password show/hide
        $('#btn-toggle-password').on('click', function () {
            const $input = $('#admin-password-input');
            const $icon = $('#toggle-password-icon');

            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $icon.removeClass('feather-eye').addClass('feather-eye-off');
            } else {
                $input.attr('type', 'password');
                $icon.removeClass('feather-eye-off').addClass('feather-eye');
            }
        });

        // Generate strong random password
        $('#btn-generate-password').on('click', function () {
            const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%&*';
            let password = '';
            for (let i = 0; i < 12; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }

            const $input = $('#admin-password-input');
            $input.attr('type', 'text').val(password);
            $('#toggle-password-icon').removeClass('feather-eye').addClass('feather-eye-off');

            $('#password-strength-text').html('<span class="text-success fw-bold"><i class="feather-check me-1"></i> Yangi kuchli parol yaratildi!</span>');
        });
    })(jQuery);
</script>
@endpush
