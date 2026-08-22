<!-- ==================== SCREEN 4: SHAHRINGIZ (67%) ==================== -->
<section id="screen-step-4" class="screen">
    <div class="onboarding-meta">
        <span>4 / 6–qadam: Shahringiz</span>
        <span>67%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width: 66.6%;"></div>
    </div>

    <h2 class="step-heading">Shahringizni tanlang</h2>
    <p class="step-subheading">Yaqiningizdagi insonlar bilan tanishing.</p>

    <div class="form-group">
        <label class="form-label">Shahar / Viloyat</label>
        <select id="input-city" class="form-input form-select" onchange="checkStep4Valid()">
            <option value="" disabled selected>Shahringizni tanlang...</option>
            @foreach(\App\Enums\General\CityEnum::cases() as $city)
                <option value="{{ $city->value }}">
                    {{ $city->label() }}
                </option>
            @endforeach
        </select>
    </div>

    <button class="btn-geo" onclick="detectGeolocation()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="7"></circle>
            <line x1="12" y1="1" x2="12" y2="4"></line>
            <line x1="12" y1="20" x2="12" y2="23"></line>
            <line x1="1" y1="12" x2="4" y2="12"></line>
            <line x1="20" y1="12" x2="23" y2="12"></line>
        </svg>
        <span id="geo-text">Geolokatsiyani avto-aniqlash</span>
    </button>

    <div class="button-row">
        <button class="btn-back" onclick="goToStep(3)">
            Orqaga
        </button>
        <button class="btn-primary disabled" id="btn-submit-step4" onclick="submitStep4()" disabled>
            Davom etish ➔
        </button>
    </div>
</section>
