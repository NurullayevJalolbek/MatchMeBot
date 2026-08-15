<!-- ==================== SCREEN 6: RASMLAR (100%) ==================== -->
<section id="screen-step-6" class="screen">
    <div class="onboarding-meta">
        <span>6 / 6–qadam: Rasmlar</span>
        <span>100%</span>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width: 100%;"></div>
    </div>

    <h2 class="step-heading">Suratlaringizni yuklang 📸</h2>
    <p class="step-subheading">Kamida 1 ta, ko'pi bilan 3 ta sifatli suratingizni joylang.</p>

    <div class="photo-grid">
        <!-- Slot 1 -->
        <div class="photo-slot" id="photo-slot-0" onclick="triggerPhotoUpload(0)">
            <span class="photo-slot-plus">+</span>
            <span class="photo-slot-label">Asosiy rasm</span>
        </div>
        <!-- Slot 2 -->
        <div class="photo-slot" id="photo-slot-1" onclick="triggerPhotoUpload(1)">
            <span class="photo-slot-plus">+</span>
            <span class="photo-slot-label">2-rasm</span>
        </div>
        <!-- Slot 3 -->
        <div class="photo-slot" id="photo-slot-2" onclick="triggerPhotoUpload(2)">
            <span class="photo-slot-plus">+</span>
            <span class="photo-slot-label">3-rasm</span>
        </div>
    </div>
    <input type="file" id="file-upload-input" accept="image/*" style="display: none;" onchange="handlePhotoUpload(event)">

    <div class="button-row">
        <button class="btn-back" onclick="goToStep(5)">
            ⬅ Orqaga
        </button>
        <button class="btn-primary" id="btn-submit-step6" onclick="submitStep6()">
            Profilni Yakunlash 🎉
        </button>
    </div>
</section>
