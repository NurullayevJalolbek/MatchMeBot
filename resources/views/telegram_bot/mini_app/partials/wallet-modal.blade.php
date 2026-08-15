<!-- ==================== DEPOSIT & WALLET BOTTOM SHEET MODAL ==================== -->
<div class="wallet-modal-overlay" id="wallet-modal-overlay" onclick="closeWalletModal(event)">
    <div class="wallet-bottom-sheet" id="wallet-bottom-sheet" onclick="event.stopPropagation()">
        
        <!-- Drag Handle -->
        <div class="sheet-drag-handle"></div>

        <!-- Header -->
        <div class="wallet-sheet-header">
            <div class="wallet-title-row">
                <div class="wallet-title-left">
                    <span class="wallet-icon-emoji">💳</span>
                    <h3 class="wallet-sheet-title">Balansni To'ldirish</h3>
                </div>
                <div class="wallet-title-right">
                    <div class="wallet-balance-badge" id="modal-current-balance">
                        Balans: 0 UZS
                    </div>
                    <button type="button" class="sheet-close-btn" onclick="closeWalletModal(event)" title="Yopish">✕</button>
                </div>
            </div>
            <p class="wallet-sheet-subtitle">Kerakli summani tanlang va to'lov chekini yuklang</p>
        </div>

        <!-- Strict Warning Box -->
        <div class="wallet-warning-card">
            <div class="warning-title-row">
                <span class="warning-icon">⚠️</span>
                <span class="warning-heading">DIQQAT: QAT'IY OGOHLANTIRISH!</span>
            </div>
            <p class="warning-text">
                Soxta, o'zgartirilgan yoki tahrirlangan (feyk) to'lov cheklarini yuklash qat'iyan man etiladi! Tizimni aldamoqchi bo'lganlarning <strong>akkaunti bir umrga butunlay bloklanadi</strong> hamda shu paytgacha qilingan barcha to'lovlari, VIP obunasi, balansi va hisobi hech qanday tovon pulsiz to'liq bekor qilinadi!
            </p>
        </div>

        <!-- Amount Match Notice Box -->
        <div class="wallet-match-notice-box">
            <span class="match-notice-icon">💡</span>
            <div class="match-notice-text">
                <strong>Muhim eslatma:</strong> To'lov chekida (skrinshotda) ko'rsatilgan aniq summa bilan kiritayotgan summanız <u>aynan bir xil (mos)</u> bo'lishi shart!
            </div>
        </div>

        <!-- Preset Amounts -->
        <div class="wallet-group">
            <label class="wallet-label">Summani tanlang: <span class="required-star">*</span></label>
            <div class="preset-amounts-grid">
                <button type="button" class="preset-btn active" id="preset-20000" onclick="selectPresetAmount(20000)">
                    20,000 UZS
                </button>
                <button type="button" class="preset-btn" id="preset-50000" onclick="selectPresetAmount(50000)">
                    50,000 UZS
                </button>
                <button type="button" class="preset-btn" id="preset-100000" onclick="selectPresetAmount(100000)">
                    100,000 UZS
                </button>
            </div>
        </div>

        <!-- Custom Amount Input -->
        <div class="wallet-group">
            <label class="wallet-label">Yoki ixtiyoriy summa kiriting: <span class="required-star">*</span></label>
            <input type="number" id="input-deposit-amount" class="wallet-amount-input" value="20000" min="1000" step="1000" oninput="onCustomAmountChange(this.value)">
        </div>

        <!-- Card Payment Info Box -->
        <div class="wallet-card-box">
            <div class="card-box-label">To'lov uchun karta (Click / Payme / Uzum):</div>
            <div class="card-number-row">
                <span class="card-number-text" id="wallet-card-number">5614 6819 1495 1557</span>
                <button type="button" class="btn-copy-card" onclick="copyCardNumber()">
                    <span>📋 Nusxa</span>
                </button>
            </div>
            <div class="card-recipient-text">
                Qabul qiluvchi: MatchMe Official (JALOLBEK N.)
            </div>
        </div>

        <!-- Receipt Upload Box -->
        <div class="wallet-group">
            <label class="wallet-label">To'lov cheki skrinshoti (Kvitansiya): <span class="required-star">*</span></label>
            <div class="receipt-upload-box" id="receipt-upload-container" onclick="triggerReceiptFileSelect()">
                <input type="file" id="input-receipt-file" accept="image/*,application/pdf" style="display: none;" onchange="handleReceiptFileSelected(event)">
                
                <div class="receipt-upload-content" id="receipt-empty-view">
                    <div class="receipt-icon-badge">📄</div>
                    <span class="receipt-upload-text">Chekni yuklash uchun bosing</span>
                    <span class="receipt-format-hint">Format: JPG, PNG yoki PDF (Majburiy)</span>
                </div>

                <div class="receipt-preview-view" id="receipt-preview-view" style="display: none;">
                    <img id="receipt-preview-img" src="" alt="Chek">
                    <span class="receipt-filename" id="receipt-file-name">kvitansiya.jpg</span>
                    <button type="button" class="btn-remove-receipt" onclick="removeReceiptFile(event)">✕</button>
                </div>
            </div>
        </div>

        <!-- Submit Deposit Button (Disabled by default until file is uploaded and amount is valid) -->
        <div class="wallet-action-row">
            <button type="button" class="btn-submit-deposit disabled" id="btn-submit-deposit" disabled onclick="submitDepositPayment()">
                <span class="btn-icon">✔</span>
                <span>To'lovni Tasdiqlash & Balansga Qo'shish</span>
            </button>
        </div>

    </div>
</div>
