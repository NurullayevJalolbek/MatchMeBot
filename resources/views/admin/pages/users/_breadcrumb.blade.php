<!-- [ page-header ] start -->
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <ul class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Asosiy</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Foydalanuvchilar</a></li>
            @if (isset($label) || (isset($title) && $title !== 'Foydalanuvchilar'))
                <li class="breadcrumb-item active">{{ $label ?? $title }}</li>
            @endif
        </ul>
    </div>
    @if (!isset($isIndex) || !$isIndex)
        <div class="page-header-right ms-auto">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light-brand border-0">
                <i class="feather-arrow-left me-2"></i>
                <span>Orqaga Qaytish</span>
            </a>
        </div>
    @endif
</div>
<!-- [ page-header ] end -->
