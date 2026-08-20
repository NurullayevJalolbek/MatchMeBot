<!-- [ page-header ] start -->
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ $title ?? ($label ?? 'Adminlar') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Asosiy</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.admins.index') }}">Adminlar</a></li>
            @if (isset($label) || (isset($title) && $title !== 'Adminlar' && $title !== 'Adminlar Boshqaruvi'))
                <li class="breadcrumb-item active">{{ $label ?? $title }}</li>
            @endif
        </ul>
    </div>
    <div class="page-header-right ms-auto">
        @if (isset($isIndex) && $isIndex)
            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                <i class="feather-plus me-2"></i>
                <span>Yangi Admin Qo'shish</span>
            </a>
        @else
            <a href="{{ route('admin.admins.index') }}" class="btn btn-light-brand">
                <i class="feather-arrow-left me-2"></i>
                <span>Orqaga Qaytish</span>
            </a>
        @endif
    </div>
</div>
<!-- [ page-header ] end -->
