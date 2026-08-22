<!-- ==================== VIP SUBSCRIPTION MODAL ==================== -->
<div class="vip-modal-overlay" id="vip-modal-overlay" onclick="closeVipModal(event)">
    <div class="vip-bottom-sheet" id="vip-bottom-sheet" onclick="event.stopPropagation()">
        
        <!-- Drag Handle -->
        <div class="sheet-drag-handle"></div>

        <!-- Header with Close Button -->
        <div class="sheet-header-top-row">
            <div class="vip-sheet-title-group">
                <div class="vip-crown-badge-circle">
                    <svg viewBox="0 0 24 24" class="svg-crown-large">
                        <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1v-1h14v1z"/>
                    </svg>
                </div>
                <h3 class="vip-sheet-title">VIP Obuna Talab Qilinadi</h3>
            </div>
            <button type="button" class="sheet-close-btn" onclick="closeVipModal(event)" title="Yopish">✕</button>
        </div>

        <p class="vip-sheet-subtitle">
            Ruletkada jins bo'yicha (faqat qizlar yoki faqat yigitlar bilan) aniq saralab suhbatlashish uchun <strong>VIP a'zolik</strong> kerak!
        </p>

        <!-- VIP Perks Feature List -->
        <div class="vip-perks-list">
            <div class="vip-perk-item">
                <div class="perk-icon-circle gold">
                    <svg viewBox="0 0 24 24" class="perk-svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
                    </svg>
                </div>
                <div class="perk-text">
                    <strong>Jins bo'yicha cheksiz qidiruv</strong>
                    <span>Faqat o'zingiz xohlagan jinsdagi insonlar bilan suhbatlashing</span>
                </div>
            </div>

            <div class="vip-perk-item">
                <div class="perk-icon-circle yellow">
                    <svg viewBox="0 0 24 24" class="perk-svg">
                        <path d="M7 2v11h3v9l7-12h-4l4-8z"/>
                    </svg>
                </div>
                <div class="perk-text">
                    <strong>Tezkor ulanish (0 navbat)</strong>
                    <span>Serverda eng birinchi bo'lib mos suhbatdoshga ulanish</span>
                </div>
            </div>

            <div class="vip-perk-item">
                <div class="perk-icon-circle crown">
                    <svg viewBox="0 0 24 24" class="perk-svg">
                        <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1v-1h14v1z"/>
                    </svg>
                </div>
                <div class="perk-text">
                    <strong>Oltin VIP Nishon</strong>
                    <span>Profilingizda barchaga ko'rinuvchi nufuzli VIP toj belgisi</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="vip-action-group">
            <button type="button" class="btn-get-vip" onclick="window.location.href='/profile'; closeVipModal();">
                <svg viewBox="0 0 24 24" class="btn-action-svg" fill="currentColor">
                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1v-1h14v1z"/>
                </svg>
                <span>Premium Obuna Olish 👑</span>
            </button>
            <button type="button" class="btn-vip-free-fallback" onclick="selectRouletteGender('all'); closeVipModal();">
                <span>Bepul rejimda (Hamma bilan) davom etish</span>
            </button>
        </div>

    </div>
</div>
