<!-- ==================== SCREEN 5: BIO (83%) ==================== -->
<section id="screen-step-5" class="screen">
    <div class="onboarding-meta">
        <span>5 / 6–qadam: O'zingiz haqida</span>
        <span>83%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width: 83.3%;"></div>
    </div>

    <h2 class="step-heading">O'zingiz haqingizda yozing</h2>
    <p class="step-subheading">Qiziqishlaringiz va o'zingiz haqingizda qisqacha ma'lumot.</p>

    <div class="form-group">
        <label class="form-label">Bio (O'zingiz haqingizda - kamida 10 ta belgi) *</label>
        <textarea id="input-bio" class="bio-textarea" maxlength="250" placeholder="Salom! Qiziqishlarim va o'zim haqimda..." oninput="updateBioCounter(); checkStep5Valid();"></textarea>
        <div class="bio-counter" id="bio-counter">0 / 250</div>
    </div>

    <div class="rules-warning-box">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" style="flex-shrink: 0;">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <span>Matnda @username, reklama yoki 18+ mazmundagi so'zlarni yozish qat'iyan taqiqlanadi.</span>
    </div>

    <div class="button-row">
        <button class="btn-back" onclick="goToStep(4)">
            Orqaga
        </button>
        <button class="btn-primary disabled" id="btn-submit-step5" onclick="submitStep5()" disabled>
            Davom etish ➔
        </button>
    </div>
</section>
