@extends('admin.layouts.app')

@section('title', $label ?? 'Xarajatlar')

@section('content')
@php
    $model = $model ?? new \App\Models\Expense();
    $isCreate = !isset($method) || strtoupper($method) === 'POST';
    $label = $label ?? ($isCreate ? 'Yangi Xarajat Qo\'shish' : 'Xarajatni Tahrirlash');
    
    $currentStatus = $model->status instanceof \App\Enums\Finance\ExpenseStatusEnum 
        ? $model->status->value 
        : ($model->status ?: 'approved');

    $spentAtValue = old('spent_at', $model->spent_at ? $model->spent_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
@endphp

@include('admin.pages.expenses._breadcrumb', [
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
                    <h5 class="card-title mb-0">Xarajat Ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ $route }}" method="POST">
                        @csrf
                        @if (isset($method) && strtoupper($method) !== 'POST')
                            @method($method)
                        @endif

                        <div class="row g-3">
                            <!-- Xarajat Sarlavhasi (Title) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Xarajat Nomi / Sarlavhasi <span class="text-danger">*</span></label>
                                <input type="text" name="title" value="{{ old('title', $model->title) }}" class="form-control" placeholder="Masalan: Instagram Target reklama to'lovi" required>
                                <small class="text-muted">Qisqa va tushunarli sarlavha</small>
                            </div>

                            <!-- Xarajat Kategoriyasi (Faqat bola kategoriyalar) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Xarajat Kategoriyasi <span class="text-danger">*</span></label>
                                <select name="expense_category_id" class="form-select" required>
                                    <option value="">-- Kategoriyani tanlang --</option>
                                    @if(isset($categories))
                                        @foreach($categories as $parentCat)
                                            <optgroup label="{{ $parentCat->icon }} {{ $parentCat->name }}">
                                                @foreach($parentCat->children as $childCat)
                                                    <option value="{{ $childCat->id }}" {{ old('expense_category_id', $model->expense_category_id) == $childCat->id ? 'selected' : '' }}>
                                                        {{ $childCat->icon }} {{ $childCat->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    @endif
                                </select>
                                <small class="text-muted">Faqat ichki bo'limlar tanlanadi</small>
                            </div>

                            <!-- Summasi (UZS) -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Xarajat Summasi (UZS) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="feather-dollar-sign"></i></span>
                                    <input type="number" name="amount" value="{{ old('amount', $model->amount ? (int)$model->amount : '') }}" min="0" step="100" class="form-control" placeholder="500000" required>
                                    <span class="input-group-text">UZS</span>
                                </div>
                            </div>

                            <!-- Sana va Vaqt (Soati bilan) -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Xarajat Sana & Vaqti <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="spent_at" value="{{ $spentAtValue }}" class="form-control" required>
                                <small class="text-muted">Chiqim amalga oshirilgan aniq vaqt</small>
                            </div>

                            <!-- To'lov Usuli -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">To'lov Usuli <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    @php
                                        $currentPaymentMethod = $model->payment_method instanceof \App\Enums\Finance\PaymentMethodEnum 
                                            ? $model->payment_method->value 
                                            : ($model->payment_method ?: 'card');
                                    @endphp
                                    @foreach(\App\Enums\Finance\PaymentMethodEnum::cases() as $methodEnum)
                                        <option value="{{ $methodEnum->value }}" {{ old('payment_method', $currentPaymentMethod) == $methodEnum->value ? 'selected' : '' }}>
                                            {{ $methodEnum->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @foreach (\App\Enums\Finance\ExpenseStatusEnum::cases() as $statusEnum)
                                        <option value="{{ $statusEnum->value }}" {{ old('status', $currentStatus) == $statusEnum->value ? 'selected' : '' }}>
                                            {{ $statusEnum->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Batafsil Tavsif / Izoh -->
                            <div class="col-12">
                                <label class="form-label fw-bold">Batafsil Izoh (Tavsif)</label>
                                <textarea name="description" rows="3" class="form-control" placeholder="Xarajat bo'yicha qo'shimcha ma'lumotlar, maqsad yoki to'lov maqsadi...">{{ old('description', $model->description) }}</textarea>
                            </div>

                            <!-- Tugmalar -->
                            <div class="col-12 pt-3 d-flex align-items-center justify-content-end gap-2 border-top">
                                <a href="{{ route('admin.expenses.index') }}" class="btn btn-light border-0">Bekor qilish</a>
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
