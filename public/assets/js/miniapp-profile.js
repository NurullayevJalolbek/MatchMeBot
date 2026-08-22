/* ==========================================================================
   MINI-APP PROFILE PAGE SCRIPTS
   MatchMe Telegram Mini-App
   ========================================================================== */

let selectedPlan = window.DEFAULT_SELECTED_PLAN || {
    id: null,
    title: 'MatchMe Premium',
    price: 0,
    formattedPrice: ''
};
let selectedReceiptFile = null;

// 1. Toast xabarnoma chiqarish
window.showToast = function (msg) {
    let toast = document.getElementById('profile-app-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'profile-app-toast';
        toast.className = 'mini-app-toast';
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.classList.add('show');
    if (window._profileToastTimeout) clearTimeout(window._profileToastTimeout);
    window._profileToastTimeout = setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
};

// 2. Modallarni Ochish / Yopish
window.openPremiumModal = function () {
    document.getElementById('modal-premium').style.display = 'flex';
};
window.closePremiumModal = function () {
    document.getElementById('modal-premium').style.display = 'none';
};

window.openPaymentReceiptModal = function () {
    closePremiumModal();
    
    // Sync selected plan details to receipt sheet
    if (document.getElementById('receipt-summary-plan-title')) {
        document.getElementById('receipt-summary-plan-title').textContent = selectedPlan.title;
    }
    if (document.getElementById('receipt-summary-plan-price')) {
        document.getElementById('receipt-summary-plan-price').textContent = selectedPlan.formattedPrice;
    }
    if (document.getElementById('payment-exact-amount-text')) {
        document.getElementById('payment-exact-amount-text').textContent = selectedPlan.formattedPrice;
    }

    document.getElementById('modal-payment-receipt').style.display = 'flex';
};
window.closePaymentReceiptModal = function () {
    document.getElementById('modal-payment-receipt').style.display = 'none';
};

window.openInstagramModal = function () {
    document.getElementById('modal-instagram').style.display = 'flex';
};
window.closeInstagramModal = function () {
    document.getElementById('modal-instagram').style.display = 'none';
};

window.openExpensesModal = function () {
    document.getElementById('modal-expenses').style.display = 'flex';
    loadExpensesHistory();
};
window.closeExpensesModal = function () {
    document.getElementById('modal-expenses').style.display = 'none';
};

window.openFilterModal = function () {
    document.getElementById('modal-filters').style.display = 'flex';
};
window.closeFilterModal = function () {
    document.getElementById('modal-filters').style.display = 'none';
};

window.openSupportModal = function () {
    document.getElementById('modal-support').style.display = 'flex';
};
window.closeSupportModal = function () {
    document.getElementById('modal-support').style.display = 'none';
};

// Helper to get Telegram User & User ID
function getClientAuth() {
    const tg = window.Telegram?.WebApp;
    let tgUser = tg?.initDataUnsafe?.user;
    if (!tgUser) {
        try {
            tgUser = JSON.parse(localStorage.getItem('matchme_tg_user') || 'null');
        } catch(e) {}
    }
    const userId = localStorage.getItem('matchme_user_id') || '';
    return { tgUser, userId };
}

// Dynamic User Sync on Page Load
async function loadUserProfile() {
    const { tgUser, userId } = getClientAuth();
    const params = new URLSearchParams();
    if (userId) params.append('user_id', userId);
    if (tgUser && tgUser.id) params.append('telegram_id', tgUser.id);

    try {
        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        };
        if (tgUser && tgUser.id) headers['X-Telegram-Id'] = tgUser.id;
        if (userId) headers['X-User-Id'] = userId;

        const res = await fetch('/api/profile?' + params.toString(), { headers });
        const data = await res.json();

        if (data.status && data.data?.user) {
            const u = data.data.user;
            const stats = data.data.stats;
            const completion = data.data.completion;

            if (u.name && document.getElementById('profile-hero-name')) {
                document.getElementById('profile-hero-name').textContent = u.name;
            }
            if (u.age) {
                const ageEl = document.getElementById('profile-hero-age');
                if (ageEl) ageEl.textContent = ', ' + u.age;
            }
            if (u.city_label && document.getElementById('profile-hero-city')) {
                document.getElementById('profile-hero-city').textContent = u.city_label;
            }
            if (u.primary_photo_url && document.getElementById('profile-hero-avatar')) {
                document.getElementById('profile-hero-avatar').src = u.primary_photo_url;
            }

            if (stats) {
                if (document.getElementById('stat-likes-count')) document.getElementById('stat-likes-count').textContent = stats.likes_count;
                if (document.getElementById('stat-matches-count')) document.getElementById('stat-matches-count').textContent = stats.matches_count;
                if (document.getElementById('stat-days-count')) document.getElementById('stat-days-count').textContent = stats.days_count + ' kun';
            }

            if (completion) {
                if (document.getElementById('completion-percentage-badge')) {
                    document.getElementById('completion-percentage-badge').textContent = completion.percentage + "% to'ldirildi";
                }
                if (document.getElementById('completion-progress-bar')) {
                    document.getElementById('completion-progress-bar').style.width = completion.percentage + "%";
                }
            }

            if (u.instagram_username) {
                if (document.getElementById('badge-instagram-val')) {
                    document.getElementById('badge-instagram-val').textContent = '@' + u.instagram_username;
                }
                const igInput = document.getElementById('input-instagram-username');
                if (igInput) igInput.value = u.instagram_username;
            }
        }
    } catch (e) {
        console.error("Profile sync error:", e);
    }
}

document.addEventListener('DOMContentLoaded', loadUserProfile);

// 3. Instagram Ulash Submit
window.submitInstagramLink = async function (e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save-instagram');
    btn.disabled = true;
    btn.innerHTML = 'Saqlanmoqda...';

    const { tgUser, userId } = getClientAuth();
    const username = document.getElementById('input-instagram-username').value;

    try {
        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        };
        if (tgUser && tgUser.id) headers['X-Telegram-Id'] = tgUser.id;
        if (userId) headers['X-User-Id'] = userId;

        const res = await fetch('/api/profile/link-instagram', {
            method: 'POST',
            headers: headers,
            body: JSON.stringify({ instagram_username: username, user_id: userId, telegram_id: tgUser?.id })
        });

        const data = await res.json();
        if (data.status && data.data?.user) {
            showToast("Instagram akkaunti muvaffaqiyatli ulandi! 📸");
            const igVal = data.data.user.instagram_username ? '@' + data.data.user.instagram_username : 'Ulanmagan';
            if (document.getElementById('badge-instagram-val')) {
                document.getElementById('badge-instagram-val').textContent = igVal;
            }
            closeInstagramModal();
        } else {
            showToast(data.message || "Xatolik yuz berdi");
        }
    } catch (err) {
        showToast("Server bilan bog'lanishda xatolik");
    } finally {
        btn.disabled = false;
        btn.innerHTML = '📸 Instagramni Saqlash';
    }
};

// 4. Obuna Tarifini Tanlash
window.selectSubscriptionPlan = function (el, planId, title, price, formattedPrice) {
    document.querySelectorAll('.premium-plan-box').forEach(c => c.classList.remove('active'));
    el.classList.add('active');

    selectedPlan = {
        id: planId,
        title: title,
        price: price,
        formattedPrice: formattedPrice
    };

    const btnLabel = document.getElementById('btn-activate-label-text');
    if (btnLabel) {
        btnLabel.textContent = `Obuna bo'lish (${formattedPrice})`;
    }
};

// 5. Karta Raqamini Nusxalash
window.copyCardNumber = function () {
    const cardDigits = document.getElementById('payment-card-number-text').innerText.replace(/\s+/g, '');
    navigator.clipboard.writeText(cardDigits).then(() => {
        const btnText = document.getElementById('copy-btn-text');
        if (btnText) btnText.textContent = "Nusxalandi! ✓";
        showToast("Karta raqami nusxalandi! 📋");
        setTimeout(() => {
            if (btnText) btnText.textContent = "Nusxalash";
        }, 2000);
    }).catch(() => {
        showToast("Karta raqami: " + cardDigits);
    });
};

// 6. Chek Faylini Tanlash & Ko'rsatish
window.handleReceiptFileSelect = function (input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    selectedReceiptFile = file;

    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('receipt-preview-img').src = e.target.result;
        document.getElementById('receipt-empty-view').style.display = 'none';
        document.getElementById('receipt-preview-view').style.display = 'block';
    };
    reader.readAsDataURL(file);
};

window.removeReceiptPreview = function () {
    selectedReceiptFile = null;
    document.getElementById('receipt-file-input').value = '';
    document.getElementById('receipt-preview-img').src = '';
    document.getElementById('receipt-empty-view').style.display = 'block';
    document.getElementById('receipt-preview-view').style.display = 'none';
};

// 7. Chekni Serverga Yuborish (Submit Receipt)
window.submitPaymentReceipt = async function () {
    if (!selectedPlan || !selectedPlan.id) {
        showToast("Iltimos, avval tarifni tanlang!");
        return;
    }

    if (!selectedReceiptFile) {
        showToast("Iltimos, to'lov cheki skrinshotini yuklang! 📸");
        return;
    }

    const btn = document.getElementById('btn-submit-receipt');
    btn.disabled = true;
    btn.innerHTML = 'Chek yuborilmoqda... ⏳';

    const { tgUser, userId } = getClientAuth();
    const notes = document.getElementById('receipt-input-notes').value;

    const formData = new FormData();
    formData.append('plan_id', selectedPlan.id);
    formData.append('receipt', selectedReceiptFile);
    if (notes) formData.append('notes', notes);
    if (userId) formData.append('user_id', userId);
    if (tgUser && tgUser.id) formData.append('telegram_id', tgUser.id);

    try {
        const headers = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        };
        if (tgUser && tgUser.id) headers['X-Telegram-Id'] = tgUser.id;
        if (userId) headers['X-User-Id'] = userId;

        const res = await fetch('/api/profile/submit-receipt', {
            method: 'POST',
            headers: headers,
            body: formData
        });

        const data = await res.json();
        if (res.ok && data.status) {
            showToast("To'lov chekingiz muvaffaqiyatli yuborildi! Admin tasdiqlagach faollashadi ✨");
            removeReceiptPreview();
            document.getElementById('receipt-input-notes').value = '';
            closePaymentReceiptModal();
            if (window.Telegram?.WebApp?.HapticFeedback) {
                try { window.Telegram.WebApp.HapticFeedback.notificationOccurred('success'); } catch(e){}
            }
        } else {
            showToast(data.message || "Chekni yuborishda xatolik yuz berdi");
        }
    } catch (e) {
        console.error("Receipt submission error:", e);
        showToast("Server bilan bog'lanishda xatolik");
    } finally {
        btn.disabled = false;
        btn.innerHTML = '📤 To\'lov Chekini Yuborish ➔';
    }
};

// 8. Xarajatlar Tarixini Yuklash
window.loadExpensesHistory = async function () {
    const container = document.getElementById('expenses-history-content');
    container.innerHTML = '<div class="loading-spinner-box"><div class="spinner-dot"></div></div>';

    const { tgUser, userId } = getClientAuth();
    const params = new URLSearchParams();
    if (userId) params.append('user_id', userId);
    if (tgUser && tgUser.id) params.append('telegram_id', tgUser.id);

    try {
        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        };
        if (tgUser && tgUser.id) headers['X-Telegram-Id'] = tgUser.id;
        if (userId) headers['X-User-Id'] = userId;

        const res = await fetch('/api/profile/expenses?' + params.toString(), { headers });
        const data = await res.json();

        if (data.status && data.data) {
            const payments = data.data.payments || [];
            const subs = data.data.subscriptions || [];
            const boosts = data.data.boosts || [];

            if (payments.length === 0 && subs.length === 0 && boosts.length === 0) {
                container.innerHTML = '<div class="empty-sheet-msg">Sizda hali xarajatlar va to\'lovlar mavjud emas.</div>';
                return;
            }

            let html = '<div class="expenses-list" style="display: flex; flex-direction: column; gap: 8px;">';
            
            subs.forEach(s => {
                html += `
                    <div style="background: #0f111a; border-radius: 12px; padding: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.9rem;">👑 ${s.plan?.title || 'Obuna'}</div>
                            <div style="font-size: 0.75rem; color: #64748b;">${s.starts_at ? new Date(s.starts_at).toLocaleDateString() : ''}</div>
                        </div>
                        <div style="color: #fbbf24; font-weight: 700; font-size: 0.9rem;">Faol</div>
                    </div>
                `;
            });

            payments.forEach(p => {
                html += `
                    <div style="background: #0f111a; border-radius: 12px; padding: 12px; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.9rem;">🧾 ${p.income_category?.name || 'Xizmat'}</div>
                            <div style="font-size: 0.75rem; color: #64748b;">${new Date(p.created_at).toLocaleDateString()}</div>
                        </div>
                        <div style="color: #10b981; font-weight: 700; font-size: 0.9rem;">${Number(p.amount).toLocaleString()} UZS</div>
                    </div>
                `;
            });

            html += '</div>';
            container.innerHTML = html;
        }
    } catch (err) {
        container.innerHTML = '<div class="empty-sheet-msg">Tarixni yuklashda xatolik yuz berdi.</div>';
    }
};

// 9. Filtr Sozlamalari Submit
window.submitFilterPreferences = async function (e) {
    e.preventDefault();
    const minAge = document.getElementById('filter-min-age').value;
    const maxAge = document.getElementById('filter-max-age').value;
    const gender = document.getElementById('filter-input-gender').value;

    const genderLabel = gender === 'female' ? 'Qizlar' : 'Yigitlar';
    if (document.getElementById('badge-filters-preview')) {
        document.getElementById('badge-filters-preview').textContent = `${genderLabel}, ${minAge}–${maxAge} yosh`;
    }
    showToast("Qidiruv filtrlari saqlandi! 🎛️");
    closeFilterModal();
};
