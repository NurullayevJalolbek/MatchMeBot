<!-- ==================== SCREEN 0: WELCOME SCREEN ==================== -->
<section id="screen-welcome" class="screen">
    <div class="hero-card">
        <div class="hero-image-wrapper">
            <img src="/images/hero_illustration.jpg" alt="MatchMe Dating Hero" onerror="this.src='/images/welcome-banner.jpg'">
            <div class="hero-image-overlay"></div>
        </div>
        <div class="hero-content">
            <div class="hero-tag">MATCHME DATING</div>
            <h2 class="hero-title">O'z baxtingizni <span class="accent-bugun">Bugun</span> Toping</h2>
            <p class="hero-description">Platformamizdan o'z baxtingiz va haqiqiy muhabbatingizni topsangiz, biz bundan faqat xursand bo'lamiz ✨</p>
        </div>
    </div>

    <div class="checkbox-container" id="terms-box" onclick="toggleTerms()">
        <div class="custom-checkbox" id="terms-checkbox">
            <svg viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
        </div>
        <div class="checkbox-label">
            Men <a onclick="event.stopPropagation(); openTermsModal()">Foydalanish Qoidalari</a> va <a onclick="event.stopPropagation(); openPrivacyModal()">Xavfsizlik</a> siyosatiga roziman
        </div>
    </div>

    <div class="button-row" style="padding-top: 0;">
        <button id="btn-start-welcome" class="btn-primary disabled" onclick="startOnboarding()" disabled>
            Tanishuvni Boshlash ➔
        </button>
    </div>
</section>
