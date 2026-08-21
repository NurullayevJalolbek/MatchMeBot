<!-- [ page-header ] start -->
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <ul class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Asosiy</a></li>
            <li class="breadcrumb-item">Xizmatlar Tarixi</li>
            <li class="breadcrumb-item"><a href="{{ route('admin.user-subscriptions.index') }}">Obunalar Tarixi</a></li>
            @if (isset($label) || (isset($title) && $title !== 'Obunalar Tarixi'))
                <li class="breadcrumb-item active">{{ $label ?? $title }}</li>
            @endif
        </ul>
    </div>
</div>
<!-- [ page-header ] end -->
