@extends('admin.layouts.app')

@section('title', 'Parametrni Tahrirlash')

@section('content')

@include('admin.pages.profile_options._breadcrumb', [
    'title' => 'Parametrni Tahrirlash',
    'isIndex' => false,
])

<!-- [ Main Content ] start -->
<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title mb-0">Parametrni Tahrirlash: {{ $option->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile-options.update', $option) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.pages.profile_options._form', [
                            'data' => $option,
                        ])

                        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('admin.profile-options.index', ['type' => $option->type->value]) }}" class="btn btn-light">
                                Bekor qilish
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-save me-1"></i> Yangilash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection
