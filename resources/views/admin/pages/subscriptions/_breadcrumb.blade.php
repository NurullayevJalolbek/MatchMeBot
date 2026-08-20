<!-- [ page-header ] start -->
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <ul class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Asosiy</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index') }}">Obuna Tariflari</a></li>
            @if (isset($label) || (isset($title) && $title !== 'Obunalar' && $title !== 'Obuna Tariflari' && $title !== 'Obunalar Boshqaruvi'))
                <li class="breadcrumb-item active">{{ $label ?? $title }}</li>
            @endif
        </ul>
    </div>
    <div class="page-header-right ms-auto">
        @if (isset($isIndex) && $isIndex)
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                <i class="feather-plus me-2"></i>
                <span>Yangi Obuna Qo'shish</span>
            </a>
        @else
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-light-brand border-0">
                <i class="feather-arrow-left me-2"></i>
                <span>Orqaga Qaytish</span>
            </a>
        @endif
    </div>
</div>
<!-- [ page-header ] end -->
