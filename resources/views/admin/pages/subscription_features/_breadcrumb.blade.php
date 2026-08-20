<!-- [ page-header ] start -->
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <ul class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Asosiy</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.subscription-features.index') }}">Obuna Afzalliklari</a></li>
            @if (isset($label) || (isset($title) && $title !== 'Obuna Afzalliklari' && $title !== 'Obuna Afzalliklari Boshqaruvi'))
                <li class="breadcrumb-item active">{{ $label ?? $title }}</li>
            @endif
        </ul>
    </div>
    <div class="page-header-right ms-auto">
        @if (isset($isIndex) && $isIndex)
            <a href="{{ route('admin.subscription-features.create') }}" class="btn btn-primary">
                <i class="feather-plus me-2"></i>
                <span>Yangi Afzallik Qo'shish</span>
            </a>
        @else
            <a href="{{ route('admin.subscription-features.index') }}" class="btn btn-light-brand border-0">
                <i class="feather-arrow-left me-2"></i>
                <span>Orqaga Qaytish</span>
            </a>
        @endif
    </div>
</div>
<!-- [ page-header ] end -->
