<!-- ==================== SCREEN 5: BIO (83%) ==================== -->
<section id="screen-step-5" class="screen">
    <div class="onboarding-meta">
        <span>5 / 6–qadam: O'zingiz haqida</span>
        <span>83%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width: 83.3%;"></div>
    </div>

    <h2 class="step-heading">O'zingiz haqingizda yozing ✍️</h2>
    <p class="step-subheading">Qiziqishlaringiz va o'zingiz haqingizda qisqacha ma'lumot.</p>

    <div class="form-group">
        <label class="form-label">Bio (O'zingiz haqingizda)</label>
        <textarea id="input-bio" class="bio-textarea" maxlength="250" placeholder="Salom! Yangi do'stlar va samimiy suhbatlar uchun ochiqman..." oninput="updateBioCounter()"></textarea>
        <div class="bio-counter" id="bio-counter">0 / 250</div>
    </div>

    <div class="rules-warning-box">
        <span>⚠️</span>
        <span>Matnda @username, reklama yoki 18+ mazmundagi so'zlarni yozish qat'iyan taqiqlanadi.</span>
    </div>

    <div class="button-row">
        <button class="btn-back" onclick="goToStep(4)">
            ⬅ Orqaga
        </button>
        <button class="btn-primary" onclick="submitStep5()">
            Davom etish ➔
        </button>
    </div>
</section>
