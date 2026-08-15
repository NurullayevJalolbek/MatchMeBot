<!-- ==================== DISCOVERY FILTER BOTTOM SHEET MODAL ==================== -->
<div class="filter-modal-overlay" id="filter-modal-overlay" onclick="closeFilterModal(event)">
    <div class="filter-bottom-sheet" id="filter-bottom-sheet" onclick="event.stopPropagation()">
        
        <!-- Drag Handle -->
        <div class="sheet-drag-handle"></div>

        <!-- Sheet Header -->
        <div class="sheet-header">
            <h3 class="sheet-title">Qidiruv Sozlamalari ⚙️</h3>
            <p class="sheet-subtitle">Sizga qanday insonlar ko'rinishini sozlang</p>
        </div>

        <!-- 1. Gender Selection -->
        <div class="filter-group">
            <label class="filter-label">Kimni qidiryapsiz? (Jins bo'yicha):</label>
            <div class="filter-gender-grid">
                <div class="filter-gender-card active" id="f-gender-female" onclick="selectFilterGender('female')">
                    <span class="gender-card-emoji">👩</span>
                    <span class="gender-card-text">Qizlar</span>
                </div>
                <div class="filter-gender-card" id="f-gender-male" onclick="selectFilterGender('male')">
                    <span class="gender-card-emoji">👨</span>
                    <span class="gender-card-text">Yigitlar</span>
                </div>
                <div class="filter-gender-card" id="f-gender-all" onclick="selectFilterGender('all')">
                    <span class="gender-card-emoji">👥</span>
                    <span class="gender-card-text">Barchasi</span>
                </div>
            </div>
        </div>

        <!-- 2. Age Range -->
        <div class="filter-group">
            <div class="filter-header-row">
                <label class="filter-label">Yosh oralig'i:</label>
                <span class="filter-val-badge" id="label-filter-age">18 – 28 yosh</span>
            </div>
            <div class="filter-slider-wrapper">
                <input type="range" class="filter-range-slider" id="input-filter-age" min="18" max="60" value="28" oninput="updateFilterAge(this.value)">
            </div>
        </div>

        <!-- 3. Max Distance -->
        <div class="filter-group">
            <div class="filter-header-row">
                <label class="filter-label">Maksimal masofa:</label>
                <span class="filter-val-badge" id="label-filter-dist">50 km</span>
            </div>
            <div class="filter-slider-wrapper">
                <input type="range" class="filter-range-slider" id="input-filter-dist" min="5" max="200" step="5" value="50" oninput="updateFilterDist(this.value)">
            </div>
        </div>

        <!-- 4. City Filter -->
        <div class="filter-group">
            <label class="filter-label">Shahar bo'yicha filter:</label>
            <div class="custom-select-wrapper">
                <select id="select-filter-city" class="custom-filter-select">
                    <option value="all">Barcha viloyatlar</option>
                    @foreach(\App\Enums\General\CityEnum::cases() as $city)
                        <option value="{{ $city->value }}">
                            {{ $city->label() }}
                        </option>
                    @endforeach
                </select>
                <div class="select-arrow-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="sheet-action-row">
            <button class="btn-save-filters" onclick="saveFilterSettings()">
                Filtrlarni Saqlash
            </button>
        </div>

    </div>
</div>
