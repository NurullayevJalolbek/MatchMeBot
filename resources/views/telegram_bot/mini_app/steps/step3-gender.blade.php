<!-- ==================== SCREEN 3: JINSINGIZ (50%) ==================== -->
<section id="screen-step-3" class="screen">
    <div class="onboarding-meta">
        <span>3 / 6–qadam: Jinsingiz</span>
        <span>50%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width: 50%;"></div>
    </div>

    <h2 class="step-heading">Jinsingiz va qidiruv</h2>
    <p class="step-subheading">Sizga eng mos insonlarni tavsiya qilamiz.</p>

    <div class="form-group">
        <label class="form-label">Sizning jinsingiz:</label>
        <div class="selection-grid">
            <!-- Male -->
            <div class="option-card" id="gender-male" onclick="selectGender('male')">
                <div class="option-icon-box">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="10" cy="14" r="5"></circle>
                        <line x1="19" y1="5" x2="13.6" y2="10.4"></line>
                        <polyline points="15 5 19 5 19 9"></polyline>
                    </svg>
                </div>
                <span class="option-text">Erkak</span>
            </div>

            <!-- Female -->
            <div class="option-card" id="gender-female" onclick="selectGender('female')">
                <div class="option-icon-box">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="12" cy="9" r="5"></circle>
                        <line x1="12" y1="14" x2="12" y2="21"></line>
                        <line x1="9" y1="18" x2="15" y2="18"></line>
                    </svg>
                </div>
                <span class="option-text">Ayol</span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Kimni qidiryapsiz?</label>
        <div class="selection-grid">
            <!-- Looking Female -->
            <div class="option-card" id="looking-female" onclick="selectLooking('female')">
                <div class="option-icon-box">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="12" cy="9" r="5"></circle>
                        <line x1="12" y1="14" x2="12" y2="21"></line>
                        <line x1="9" y1="18" x2="15" y2="18"></line>
                    </svg>
                </div>
                <span class="option-text">Qizlar</span>
            </div>

            <!-- Looking Male -->
            <div class="option-card" id="looking-male" onclick="selectLooking('male')">
                <div class="option-icon-box">
                    <svg viewBox="0 0 24 24" stroke-width="2">
                        <circle cx="10" cy="14" r="5"></circle>
                        <line x1="19" y1="5" x2="13.6" y2="10.4"></line>
                        <polyline points="15 5 19 5 19 9"></polyline>
                    </svg>
                </div>
                <span class="option-text">Yigitlar</span>
            </div>
        </div>
    </div>

    <div class="button-row">
        <button class="btn-back" onclick="goToStep(2)">
            Orqaga
        </button>
        <button class="btn-primary disabled" id="btn-submit-step3" onclick="submitStep3()" disabled>
            Davom etish ➔
        </button>
    </div>
</section>
