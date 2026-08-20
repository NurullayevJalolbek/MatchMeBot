<!-- [ page-header ] start -->
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <ul class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Asosiy</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.income-categories.index') }}">Tushumlar kategoriyasi</a></li>
            
            @if(isset($parentCategory) && $parentCategory)
                @foreach($parentCategory->getBreadcrumbs() as $crumb)
                    @if($loop->last && !isset($label))
                        <li class="breadcrumb-item active">{{ $crumb->name }}</li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.income-categories.index', ['parent_id' => $crumb->id]) }}">{{ $crumb->name }}</a>
                        </li>
                    @endif
                @endforeach
            @endif

            @if(isset($label))
                <li class="breadcrumb-item active">{{ $label }}</li>
            @endif
        </ul>
    </div>
    @if (!isset($isIndex) || !$isIndex)
        <div class="page-header-right ms-auto">
            <a href="{{ route('admin.income-categories.index', ['parent_id' => $parentId ?? ($parentCategory?->id)]) }}" class="btn btn-light-brand border-0">
                <i class="feather-arrow-left me-2"></i>
                <span>Orqaga Qaytish</span>
            </a>
        </div>
    @endif
</div>
<!-- [ page-header ] end -->
