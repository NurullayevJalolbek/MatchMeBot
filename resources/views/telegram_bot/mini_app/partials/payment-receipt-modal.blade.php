<!-- ==================== UNIVERSAL PAYMENT & SCREENSHOT PROOF MODAL ==================== -->
<div class="profile-modal-overlay" id="modal-payment-receipt" onclick="if(event.target === this) closePaymentReceiptModal()">
    <div class="profile-sheet-card payment-receipt-sheet" onclick="event.stopPropagation()">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-header">
            <div class="payment-title-box">
                <h3 class="sheet-title">💳 To'lov & Chekni Yuborish</h3>
            </div>
            <button type="button" class="sheet-close-btn" onclick="closePaymentReceiptModal()">✕</button>
        </div>

        <!-- Selected Plan/Boost Summary Badge -->
        <div class="selected-plan-summary-card">
            <div class="plan-summary-left">
                <span class="summary-crown-icon" id="receipt-summary-icon">⚡</span>
                <div>
                    <div class="summary-plan-title" id="receipt-summary-plan-title">MatchMe Boost</div>
                    <div class="summary-plan-sub" id="receipt-summary-plan-sub">Profilni 1-o'ringa ko'tarish</div>
                </div>
            </div>
            <div class="summary-plan-price" id="receipt-summary-plan-price">25 000 UZS</div>
        </div>

        <!-- Bank Card Payment Box -->
        <div class="bank-card-details-box">
            <div class="bank-card-top-row">
                <span class="bank-label">HUMO / UZCARD orqali to'lov qiling:</span>
                <span class="bank-online-tag">24/7 Qabul</span>
            </div>
            <div class="card-number-row">
                <span class="card-digits" id="payment-card-number-text">9860 0301 4528 7890</span>
                <button type="button" class="btn-copy-card" onclick="copyCardNumber()" title="Nusxalash">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span id="copy-btn-text">Nusxalash</span>
                </button>
            </div>
            <div class="bank-card-holder-row">
                <span>Karta egasi: <b>MatchMe Official</b></span>
                <span>To'lov: <b id="payment-exact-amount-text" style="color: #fbbf24;">25 000 UZS</b></span>
            </div>
        </div>

        <!-- Screenshot Upload Dropzone -->
        <div class="receipt-upload-section">
            <label class="receipt-section-label">📸 To'lov cheki skrinshotini yuklang:</label>
            
            <div class="receipt-dropzone" id="receipt-dropzone-box" onclick="document.getElementById('receipt-file-input').click()">
                <div id="receipt-empty-view" class="receipt-empty-state">
                    <div class="dropzone-icon">📷</div>
                    <div class="dropzone-main-text">Skrinshotni tanlash uchun bosing</div>
                    <div class="dropzone-hint-text">JPG, PNG yoki WebP formatida (Maksimal 10MB)</div>
                </div>

                <div id="receipt-preview-view" class="receipt-preview-state" style="display: none;">
                    <img id="receipt-preview-img" src="" alt="To'lov Cheki" class="receipt-img-thumb">
                    <button type="button" class="btn-remove-receipt" onclick="event.stopPropagation(); removeReceiptPreview()">✕ O'chirish</button>
                </div>
            </div>

            <input type="file" id="receipt-file-input" accept="image/jpeg,image/png,image/webp,image/jpg,image/heic" style="display: none;" onchange="handleReceiptFileSelect(this)">
        </div>

        <!-- Optional Notes -->
        <div class="sheet-form-group" style="margin-bottom: 0;">
            <label class="sheet-label">Qo'shimcha izoh / Telegram username (ixtiyoriy):</label>
            <input type="text" id="receipt-input-notes" class="sheet-input" placeholder="Masalan: @username yoki tranzaksiya raqami">
        </div>

        <!-- Submit Button -->
        <button type="button" class="sheet-submit-btn bg-gold-btn" id="btn-submit-receipt" onclick="submitUniversalPaymentReceipt()">
            📤 To'lov Chekini Yuborish ➔
        </button>
    </div>
</div>
