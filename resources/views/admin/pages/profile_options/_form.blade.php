<div class="row g-4">
    <!-- Bo'lim Turi (Type) -->
    <div class="col-md-6">
        <label for="type" class="form-label fw-bold">
            Bo'lim Turi <span class="text-danger">*</span>
        </label>
        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
            <option value="">-- Bo'lim turini tanlang --</option>
            @foreach ($types as $typeOption)
                @php
                    $val = $typeOption->value;
                    $selected = old('type', $data->type->value ?? ($data->type ?? ($defaultType ?? ''))) === $val;
                @endphp
                <option value="{{ $val }}" {{ $selected ? 'selected' : '' }}>
                    {{ $typeOption->label() }}
                </option>
            @endforeach
        </select>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted fs-11">Qaysi bo'limga tegishli ekanligi (Qiziqishlar, Maqsad, Turmush tarzi, Men haqimda)</small>
    </div>

    <!-- Guruhi / Toifasi (Category) -->
    <div class="col-md-6">
        <label for="category" class="form-label fw-bold">
            Guruhi / Toifasi
        </label>
        <input type="text" 
               class="form-control @error('category') is-invalid @enderror" 
               id="category" 
               name="category" 
               list="category-suggestions"
               value="{{ old('category', $data->category ?? '') }}" 
               placeholder="Masalan: Sport va Fitnes, Chekish odati...">
        <datalist id="category-suggestions">
            @if(isset($existingCategories))
                @foreach($existingCategories as $cat)
                    <option value="{{ $cat }}">
                @endforeach
            @endif
        </datalist>
        @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted fs-11">Ichki guruh nomi (bo'sh qoldirilsa umumiy bo'ladi)</small>
    </div>

    <!-- Nomi (Name) -->
    <div class="col-md-6">
        <label for="name" class="form-label fw-bold">
            Nomi <span class="text-danger">*</span>
        </label>
        <input type="text" 
               class="form-control @error('name') is-invalid @enderror" 
               id="name" 
               name="name" 
               value="{{ old('name', $data->name ?? '') }}" 
               placeholder="Masalan: Futbol, Nikoh va oila, Chekmayman..." 
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Ikonka / Emoji (Icon) -->
    <div class="col-md-6">
        <label for="icon" class="form-label fw-bold">
            Ikonka (Emoji)
        </label>
        <input type="text" 
               class="form-control @error('icon') is-invalid @enderror" 
               id="icon" 
               name="icon" 
               value="{{ old('icon', $data->icon ?? '') }}" 
               placeholder="Masalan: ⚽, 💍, 🚭, 🎓...">
        @error('icon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted fs-11">Variant oldida chiqadigan emoji belgisi</small>
    </div>

    <!-- Tartib Raqami (Order) -->
    <div class="col-md-6">
        <label for="order" class="form-label fw-bold">
            Tartib Raqami
        </label>
        <input type="number" 
               class="form-control @error('order') is-invalid @enderror" 
               id="order" 
               name="order" 
               value="{{ old('order', $data->order ?? 0) }}" 
               min="0">
        @error('order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted fs-11">Ro'yxatda chiqish tartibi (kichik raqamlar oldin chiqadi)</small>
    </div>

    <!-- Status (is_active) -->
    <div class="col-md-6 d-flex align-items-center">
        <div class="form-check form-switch pt-md-4">
            <input class="form-check-input" 
                   type="checkbox" 
                   role="switch" 
                   id="is_active" 
                   name="is_active" 
                   value="1" 
                   {{ old('is_active', $data->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold ms-2" for="is_active">
                Faol (Foydalanuvchilarga ko'rinsin)
            </label>
        </div>
    </div>
</div>
