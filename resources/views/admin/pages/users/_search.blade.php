<div class="card mb-4 border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-end">
            <!-- 1. Qidiruv -->
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">
                    <i class="feather-search me-1 text-primary"></i> Qidiruv
                </label>
                <input type="text" name="search" class="form-control form-control-sm rounded-3" 
                       placeholder="Ism, @username, Chat ID..." 
                       value="{{ request('search') }}">
            </div>

            <!-- 2. Jinsi -->
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">
                    <i class="feather-user me-1 text-primary"></i> Jinsi
                </label>
                <select name="gender" class="form-select form-select-sm rounded-3">
                    <option value="">Barchasi</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Erkak</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Ayol</option>
                </select>
            </div>

            <!-- 3. Shahar -->
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">
                    <i class="feather-map-pin me-1 text-primary"></i> Shahar
                </label>
                <select name="city" class="form-select form-select-sm rounded-3">
                    <option value="">Barchasi</option>
                    @foreach(\App\Enums\General\CityEnum::cases() as $city)
                        <option value="{{ $city->value }}" {{ request('city') === $city->value ? 'selected' : '' }}>
                            {{ $city->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 4. VIP Holati -->
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">
                    <i class="feather-award me-1 text-warning"></i> VIP Maqomi
                </label>
                <select name="is_vip" class="form-select form-select-sm rounded-3">
                    <option value="">Barchasi</option>
                    <option value="1" {{ request('is_vip') === '1' ? 'selected' : '' }}>👑 Faqat VIP</option>
                    <option value="0" {{ request('is_vip') === '0' ? 'selected' : '' }}>Oddiy a'zolar</option>
                </select>
            </div>

            <!-- 5. Holati -->
            <div class="col-6 col-md-3 col-lg-2">
                <label class="form-label fs-12 fw-bold text-muted text-uppercase mb-1">
                    <i class="feather-check-circle me-1 text-primary"></i> Holati
                </label>
                <select name="status" class="form-select form-select-sm rounded-3">
                    <option value="">Barchasi</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Faol</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Bloklangan</option>
                </select>
            </div>

            <!-- 6. Tugmalar -->
            <div class="col-12 col-lg-1 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-3 w-100" title="Filtrlash">
                    <i class="feather-filter me-1"></i> Izlash
                </button>
                @if(request()->hasAny(['search', 'gender', 'city', 'is_vip', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm rounded-3" title="Tozalash">
                        <i class="feather-rotate-ccw"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
