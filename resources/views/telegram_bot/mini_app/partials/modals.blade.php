<!-- Modals -->
<div class="modal-overlay" id="terms-modal" onclick="closeModals(event)">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="modal-header-row">
            <h3 class="modal-title">Foydalanish Qoidalari 📜</h3>
            <button type="button" class="sheet-close-btn" onclick="closeModals(event)" title="Yopish">✕</button>
        </div>
        <div class="modal-body">
            1. MatchMe xizmatidan faqat 18 yoshdan oshgan shaxslar foydalanishi mumkin.<br><br>
            2. Anketalarda noqonuniy, haqoratomuz, 18+ yoki soxta ma'lumotlar joylash qat'iyan taqiqlanadi.<br><br>
            3. Qoidalarni buzgan foydalanuvchilar akkaunti bir umrga bloklanadi.
        </div>
        <button class="modal-close-btn" onclick="closeModals(event)">Tushundim</button>
    </div>
</div>

<div class="modal-overlay" id="privacy-modal" onclick="closeModals(event)">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="modal-header-row">
            <h3 class="modal-title">Xavfsizlik Siyosati 🔒</h3>
            <button type="button" class="sheet-close-btn" onclick="closeModals(event)" title="Yopish">✕</button>
        </div>
        <div class="modal-body">
            1. Sizning shaxsiy ma'lumotlaringiz va geolokatsiyangiz uchinchi shaxslarga berilmaydi.<br><br>
            2. MatchMe platformasi xavfsiz va shifrlangan ma'lumotlar almashinuvini kafolatlaydi.
        </div>
        <button class="modal-close-btn" onclick="closeModals(event)">Tushundim</button>
    </div>
</div>
