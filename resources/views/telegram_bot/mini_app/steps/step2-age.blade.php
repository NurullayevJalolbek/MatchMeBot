<!-- ==================== SCREEN 2: YOSHINGIZ (33%) ==================== -->
<section id="screen-step-2" class="screen">
    <div class="onboarding-meta">
        <span>2 / 6–qadam: Yoshingiz</span>
        <span>33%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width: 33.3%;"></div>
    </div>

    <h2 class="step-heading">Yoshingiz nechida? 🎂</h2>
    <p class="step-subheading">Faqat 18 yoshdan kattalar uchun xizmat.</p>

    <div class="form-group">
        <label class="form-label">Tug'ilgan sanangiz</label>
        <input type="date" id="input-birthdate" class="form-input" onchange="calculateAge()" max="{{ date('Y-m-d', strtotime('-18 years')) }}">
    </div>

    <div class="age-result-card" id="age-result-card" style="display: none;">
        <span class="age-result-label">Hisoblangan yoshingiz:</span>
        <span class="age-badge" id="age-badge-text">23 yosh (18+)</span>
    </div>

    <div class="button-row">
        <button class="btn-back" onclick="goToStep(1)">
            ⬅ Orqaga
        </button>
        <button class="btn-primary" id="btn-submit-step2" onclick="submitStep2()">
            Davom etish ➔
        </button>
    </div>
</section>
