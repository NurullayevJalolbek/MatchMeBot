@extends('telegram_bot.mini_app.layouts.app')

@section('title', 'Profilni Tahrirlash')

@section('content')
@php
    $mainUser = auth()->user()
        ?? (session('user_id') ? \App\Models\User::find(session('user_id')) : null)
        ?? \App\Models\User::regularUsers()->latest('id')->first()
        ?? \App\Models\User::latest('id')->first();
    $profileService = app(\App\Contracts\iProfileService::class);
    $editData = $mainUser ? $profileService->getEditProfileData($mainUser) : [];
    $u = $editData['user'] ?? [];
    $regions = $editData['regions'] ?? [];
    $districts = $editData['districts'] ?? [];
    $options = $editData['options'] ?? [];
    $completion = $editData['completion'] ?? ['percentage' => 85, 'missing' => []];
    $selectedOptionIds = $u['selected_option_ids'] ?? [];
@endphp

<div class="edit-page-wrapper">
    
    <!-- 1. CLEAN TOP ACTION HEADER (ONLY BACK BUTTON & SAQLASH BUTTON) -->
    <header class="edit-clean-header">
        <button type="button" class="btn-clean-back" onclick="window.location.href='/profile'" title="Orqaga">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>

        <button type="button" class="btn-top-save-pill" id="btn-save-all" onclick="saveFullProfile()">
            <span>Saqlash</span>
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </button>
    </header>

    <!-- 2. SCROLLABLE EDIT FORM CONTAINER -->
    <div class="edit-scroll-container">

        <!-- A. PROFIL TO'LDIRILISHI PROGRESS KARTASI -->
        <div class="edit-completion-card">
            <div class="comp-header-row">
                <div class="comp-title-left">
                    <span class="comp-gift-emoji">🎁</span>
                    <span class="comp-label">Profil to'ldirilishi:</span>
                    <span class="comp-percent-val" id="comp-percent-text">{{ $completion['percentage'] }}%</span>
                </div>
                <span class="comp-remaining-badge" id="comp-remaining-badge">
                    {{ 100 - $completion['percentage'] > 0 ? '+' . (100 - $completion['percentage']) . '% qoldi' : '100% To\'liq' }}
                </span>
            </div>

            <div class="comp-track">
                <div class="comp-fill" id="comp-progress-bar" style="width: {{ $completion['percentage'] }}%;"></div>
            </div>

            <div class="comp-footer-row">
                <div class="comp-gift-info">
                    <span class="comp-mini-gift">🎁</span>
                    <span>100% to'ldirilganda <b>3 ta bepul sovg'a</b> beriladi</span>
                </div>
                <span class="comp-active-pill">Faol</span>
            </div>
        </div>

        <!-- B. 1-BO'LIM: RASMLAR (MAKSIMAL 3 TA) -->
        <div class="edit-section-card">
            <div class="section-title-row">
                <div class="section-title-left">
                    <span class="section-icon-emoji">📷</span>
                    <h3 class="section-title">Rasmlar (Maksimal 3 ta)</h3>
                </div>
                <span class="section-badge-counter" id="photos-counter-pill">{{ count($u['photos'] ?? []) }} / 3 rasm</span>
            </div>
            <p class="section-subtitle">
                Kamida 1 ta, maksimal 3 ta sifatli rasm yuklang. 1-rasm asosiy hisoblanadi.
            </p>

            <!-- 3 Ta Surat Grid -->
            <div class="photos-grid-layout" id="photos-grid-container">
                <!-- Javascript orqali render qilinadi -->
            </div>

            <input type="file" id="photo-upload-input" accept="image/jpeg,image/png,image/webp,image/jpg" style="display: none;" onchange="handlePhotoUpload(this)">
        </div>

        <!-- C. 2-BO'LIM: O'ZINGIZ HAQINGIZDA (BIO) -->
        <div class="edit-section-card">
            <div class="section-title-row">
                <div class="section-title-left">
                    <span class="section-icon-emoji">✍️</span>
                    <h3 class="section-title">O'zingiz haqingizda (Bio)</h3>
                </div>
                <span class="section-badge-counter" id="bio-char-counter">{{ mb_strlen($u['bio'] ?? '') }}/250</span>
            </div>

            <textarea id="edit-bio" class="edit-textarea-input" maxlength="250" 
                      placeholder="Dasturlash, sayohat va musiqaga qiziqaman. Samimiy va quvnoq insonlar bilan do'stlashishni xohlayman! ☕✈️"
                      oninput="updateBioCounter(this)">{{ $u['bio'] ?? '' }}</textarea>

            <!-- Cheklovlar Bloki -->
            <div class="bio-rules-box">
                <div class="rules-title">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" class="rules-shield-icon">
                        <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3z"/>
                    </svg>
                    <span>Profil qoidalari & Cheklovlar:</span>
                </div>
                <ul class="rules-list">
                    <li><b>Instagram / Telegram:</b> Bio matnida username yoki havolalar qoldirish taqiqlanadi.</li>
                    <li><b>Reklama:</b> Tijoriy mahsulotlar va pullik xizmatlar reklamasi man etiladi.</li>
                    <li><b>18+ Cheklov:</b> Odobsiz so'zlar va behayo mazmundagi gaplar taqiqlanadi.</li>
                </ul>
            </div>
        </div>

        <!-- D. 3-BO'LIM: TAFSILOTLAR (BO'Y & VAZN SLIDERLARI) -->
        <div class="edit-section-card">
            <div class="section-title-row">
                <div class="section-title-left">
                    <span class="section-icon-emoji">📊</span>
                    <h3 class="section-title">Tafsilotlar</h3>
                </div>
            </div>

            <!-- Bo'yingiz Slider -->
            <div class="slider-group-wrap">
                <div class="slider-info-row">
                    <span class="slider-label">Bo'yingiz:</span>
                    <span class="slider-val-highlight" id="height-val-display">{{ $u['height'] ?? 178 }} sm</span>
                </div>
                <input type="range" class="custom-range-slider" id="slider-height" 
                       min="130" max="220" value="{{ $u['height'] ?? 178 }}" 
                       oninput="onHeightChange(this.value)">
            </div>

            <!-- Vazningiz Slider -->
            <div class="slider-group-wrap">
                <div class="slider-info-row">
                    <span class="slider-label">Vazningiz:</span>
                    <span class="slider-val-highlight" id="weight-val-display">{{ $u['weight'] ?? 72 }} kg</span>
                </div>
                <input type="range" class="custom-range-slider" id="slider-weight" 
                       min="40" max="150" value="{{ $u['weight'] ?? 72 }}" 
                       oninput="onWeightChange(this.value)">
            </div>
        </div>

        <!-- E. 4-BO'LIM: QIZIQISHLAR & MAQSADLAR -->
        <div class="edit-section-card">
            <div class="section-title-row">
                <div class="section-title-left">
                    <span class="section-icon-emoji">💖</span>
                    <h3 class="section-title">Qiziqishlar & Maqsadlar</h3>
                </div>
            </div>

            <!-- Qiziqishlar Selector Trigger -->
            <div class="interests-trigger-row" onclick="openInterestsModal()">
                <div class="interests-header-sub">
                    <span class="sub-heading-text">Qiziqishlarim (Maksimal 10 ta):</span>
                </div>
                <div class="interests-box-bar">
                    <span class="interests-box-label">Tanlangan qiziqishlar</span>
                    <span class="interests-box-count" id="interests-pill-count">0 / 10 ta ➔</span>
                </div>
            </div>

            <!-- Tanlangan Qiziqishlar Chiroyli Pillari -->
            <div class="selected-pills-wrap" id="selected-interests-preview">
                <!-- Javascript tanlanganlarni chiqaradi -->
            </div>

            <!-- Tanishishdan Maqsad (Single Select) -->
            <div class="options-sub-block">
                <span class="sub-heading-text">Tanishishdan maqsad:</span>
                <div class="pill-options-grid" id="dating-purpose-options-grid">
                    @foreach($options['dating_purpose'] ?? [] as $opt)
                        <button type="button" class="btn-option-pill single-select {{ in_array($opt['id'], $selectedOptionIds) ? 'active' : '' }}" 
                                data-option-id="{{ $opt['id'] }}" 
                                data-type="dating_purpose"
                                onclick="toggleSingleOption(this, 'dating_purpose')">
                            <span class="opt-icon">{{ $opt['icon'] }}</span>
                            <span class="opt-text">{{ $opt['name'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- F. 5-BO'LIM: TURMUSH TARZI (LIFESTYLE) -->
        <div class="edit-section-card">
            <div class="section-title-row">
                <div class="section-title-left">
                    <span class="section-icon-emoji">🍸</span>
                    <h3 class="section-title">Turmush tarzi</h3>
                </div>
            </div>

            @foreach($options['lifestyle'] ?? [] as $categoryName => $catOptions)
                <div class="options-sub-block">
                    <span class="sub-heading-text">{{ $categoryName }}:</span>
                    <div class="pill-options-grid">
                        @foreach($catOptions as $opt)
                            <button type="button" class="btn-option-pill single-select {{ in_array($opt['id'], $selectedOptionIds) ? 'active' : '' }}" 
                                    data-option-id="{{ $opt['id'] }}" 
                                    data-category="{{ Str::slug($categoryName) }}"
                                    onclick="toggleCategoryOption(this, '{{ Str::slug($categoryName) }}')">
                                <span class="opt-icon">{{ $opt['icon'] }}</span>
                                <span class="opt-text">{{ $opt['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- G. 6-BO'LIM: MEN HAQIMDA KO'PROQ -->
        <div class="edit-section-card">
            <div class="section-title-row">
                <div class="section-title-left">
                    <span class="section-icon-emoji">🎓</span>
                    <h3 class="section-title">Men haqimda ko'proq</h3>
                </div>
            </div>

            @foreach($options['about_me'] ?? [] as $categoryName => $catOptions)
                <div class="options-sub-block">
                    <span class="sub-heading-text">{{ $categoryName }}:</span>
                    <div class="pill-options-grid">
                        @foreach($catOptions as $opt)
                            <button type="button" class="btn-option-pill single-select {{ in_array($opt['id'], $selectedOptionIds) ? 'active' : '' }}" 
                                    data-option-id="{{ $opt['id'] }}" 
                                    data-category="{{ Str::slug($categoryName) }}"
                                    onclick="toggleCategoryOption(this, '{{ Str::slug($categoryName) }}')">
                                <span class="opt-icon">{{ $opt['icon'] }}</span>
                                <span class="opt-text">{{ $opt['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- H. 7-BO'LIM: ASOSIY MA'LUMOTLAR & HUDUDLAR -->
        <div class="edit-section-card">
            <div class="section-title-row">
                <div class="section-title-left">
                    <span class="section-icon-emoji">🪪</span>
                    <h3 class="section-title">Asosiy ma'lumotlar</h3>
                </div>
            </div>

            <!-- Ism -->
            <div class="edit-form-field">
                <label class="field-label">Ismingiz:</label>
                <input type="text" id="edit-name" class="edit-text-input" value="{{ $u['name'] ?? '' }}" placeholder="Ismingiz">
            </div>

            <!-- Tug'ilgan Sana -->
            <div class="edit-form-field">
                <label class="field-label">Tug'ilgan sana:</label>
                <input type="date" id="edit-birth-date" class="edit-text-input" value="{{ $u['birth_date'] ?? '' }}">
            </div>

            <!-- Jinsingiz -->
            <div class="edit-form-field">
                <label class="field-label">Jinsingiz:</label>
                <div class="gender-toggle-grid">
                    <button type="button" class="btn-gender-pill {{ ($u['gender'] ?? 'male') === 'male' ? 'active' : '' }}" id="gender-btn-male" onclick="selectGender('male')">
                        <span>🧔</span>
                        <span>Erkak</span>
                    </button>
                    <button type="button" class="btn-gender-pill {{ ($u['gender'] ?? '') === 'female' ? 'active' : '' }}" id="gender-btn-female" onclick="selectGender('female')">
                        <span>👩</span>
                        <span>Ayol</span>
                    </button>
                </div>
            </div>

            <!-- Hozirgi Yashayotgan Joyi (Viloyat & Tuman) -->
            <div class="edit-form-field">
                <label class="field-label">Hozirgi yashayotgan viloyat / shahringiz:</label>
                <select id="edit-living-region" class="edit-select-input" onchange="onLivingRegionChange(this.value)">
                    <option value="">Tanlang...</option>
                    @foreach($regions as $r)
                        <option value="{{ $r['id'] }}" {{ ($u['living_region_id'] ?? '') == $r['id'] ? 'selected' : '' }}>
                            {{ $r['name_uz'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="edit-form-field">
                <label class="field-label">Hozirgi yashayotgan tumaningiz:</label>
                <select id="edit-living-district" class="edit-select-input">
                    <option value="">Avval viloyatni tanlang...</option>
                </select>
            </div>

            <!-- Tug'ilgan Joyi (Viloyat & Tuman) -->
            <div class="edit-form-field">
                <label class="field-label">Tug'ilgan viloyat / shahringiz:</label>
                <select id="edit-birth-region" class="edit-select-input" onchange="onBirthRegionChange(this.value)">
                    <option value="">Tanlang...</option>
                    @foreach($regions as $r)
                        <option value="{{ $r['id'] }}" {{ ($u['birth_region_id'] ?? '') == $r['id'] ? 'selected' : '' }}>
                            {{ $r['name_uz'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="edit-form-field">
                <label class="field-label">Tug'ilgan tumaningiz:</label>
                <select id="edit-birth-district" class="edit-select-input">
                    <option value="">Avval viloyatni tanlang...</option>
                </select>
            </div>

            <!-- Aniq Lokatsiya Neon Preview Pilli -->
            <div class="location-neon-dock">
                <div class="neon-dock-inner">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#f43f5e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                    </svg>
                    <span id="location-preview-text">Toshkent, Yunusobod (Aniq lokatsiya)</span>
                </div>
            </div>
        </div>

        <div style="height: 60px;"></div>
    </div>
</div>

<!-- ==================== 4. QIZIQISHLAR MODAL SHEET (MAKSIMAL 10 TA) ==================== -->
<div class="custom-interests-overlay" id="modal-interests-picker" onclick="if(event.target === this) closeInterestsModal()">
    <div class="custom-interests-sheet">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-top-row">
            <div class="sheet-title-box">
                <h3 class="sheet-title">Qiziqishlarni tanlang</h3>
                <span class="sheet-counter-badge" id="modal-interests-counter">0 / 10 ta</span>
            </div>
            <button type="button" class="sheet-close-btn" onclick="closeInterestsModal()">✕</button>
        </div>
        <p class="sheet-desc">O'zingizga yoqqan qiziqishlarni belgilang (Maksimal 10 ta):</p>

        <div class="interests-categories-scroll">
            @foreach($options['interests'] ?? [] as $categoryName => $catOptions)
                <div class="interest-category-block">
                    <h4 class="interest-cat-title">{{ $categoryName }}</h4>
                    <div class="pill-options-grid">
                        @foreach($catOptions as $opt)
                            <button type="button" class="btn-option-pill multi-select interest-pill-item {{ in_array($opt['id'], $selectedOptionIds) ? 'active' : '' }}" 
                                    data-option-id="{{ $opt['id'] }}" 
                                    data-name="{{ $opt['name'] }}"
                                    data-icon="{{ $opt['icon'] }}"
                                    onclick="toggleInterestItem(this, {{ $opt['id'] }})">
                                <span class="opt-icon">{{ $opt['icon'] }}</span>
                                <span class="opt-text">{{ $opt['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" class="sheet-submit-btn" onclick="closeInterestsModal()">
            Tayyor ➔
        </button>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/miniapp-profile-edit.css') }}?v={{ time() }}">
@endpush

@push('scripts')
    <script>
        window.INITIAL_EDIT_STATE = {
            name: "{{ addslashes($u['name'] ?? '') }}",
            gender: "{{ $u['gender'] ?? 'male' }}",
            birthDate: "{{ $u['birth_date'] ?? '' }}",
            height: {{ $u['height'] ?? 178 }},
            weight: {{ $u['weight'] ?? 72 }},
            bio: "{{ addslashes($u['bio'] ?? '') }}",
            livingRegionId: "{{ $u['living_region_id'] ?? '' }}",
            livingDistrictId: "{{ $u['living_district_id'] ?? '' }}",
            birthRegionId: "{{ $u['birth_region_id'] ?? '' }}",
            birthDistrictId: "{{ $u['birth_district_id'] ?? '' }}",
            photos: @json($u['photos'] ?? []),
            selectedOptionIds: @json($selectedOptionIds ?? []),
            districts: @json($districts ?? [])
        };
    </script>
    <script src="{{ asset('assets/js/miniapp-profile-edit.js') }}?v={{ time() }}"></script>
@endpush
