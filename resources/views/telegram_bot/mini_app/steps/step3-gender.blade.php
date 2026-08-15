<!-- ==================== SCREEN 3: JINSINGIZ (50%) ==================== -->
<section id="screen-step-3" class="screen">
    <div class="onboarding-meta">
        <span>3 / 6–qadam: Jinsingiz</span>
        <span>50%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width: 50%;"></div>
    </div>

    <h2 class="step-heading">Jinsingiz va maqsadingiz 👥</h2>
    <p class="step-subheading">Sizga eng mos insonlarni tavsiya qilamiz.</p>

    <div class="form-group">
        <label class="form-label">Sizning jinsingiz:</label>
        <div class="selection-grid">
            <div class="option-card selected" id="gender-male" onclick="selectGender('male')">
                <span class="option-emoji">👦</span>
                <span class="option-text">Erkak</span>
            </div>
            <div class="option-card" id="gender-female" onclick="selectGender('female')">
                <span class="option-emoji">👧</span>
                <span class="option-text">Ayol</span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Kimni qidiryapsiz?</label>
        <div class="selection-grid">
            <div class="option-card selected" id="looking-female" onclick="selectLooking('female')">
                <span class="option-emoji">👧</span>
                <span class="option-text">Qizlar</span>
            </div>
            <div class="option-card" id="looking-male" onclick="selectLooking('male')">
                <span class="option-emoji">👦</span>
                <span class="option-text">Yigitlar</span>
            </div>
        </div>
    </div>

    <div class="button-row">
        <button class="btn-back" onclick="goToStep(2)">
            ⬅ Orqaga
        </button>
        <button class="btn-primary" onclick="submitStep3()">
            Davom etish ➔
        </button>
    </div>
</section>
