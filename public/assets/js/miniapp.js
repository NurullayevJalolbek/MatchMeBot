/**
 * MatchMe Telegram Mini-App Client Logic
 */

// State management
const state = {
    userId: null,
    isTermsAccepted: false,
    name: '',
    birthDate: '2002-05-18',
    age: 23,
    gender: 'male',
    lookingFor: 'female',
    city: 'tashkent_city',
    latitude: null,
    longitude: null,
    bio: '',
    photos: [],
    currentSlotUploading: 0
};

// Initialize Telegram WebApp
const tg = window.Telegram?.WebApp;
if (tg) {
    tg.ready();
    tg.expand();
    try {
        if (typeof tg.setHeaderColor === 'function') tg.setHeaderColor('#000000');
        if (typeof tg.setBackgroundColor === 'function') tg.setBackgroundColor('#000000');
        if (typeof tg.setBottomBarColor === 'function') tg.setBottomBarColor('#000000');
        if (typeof tg.requestFullscreen === 'function') tg.requestFullscreen();
    } catch (e) {}
}

// Instant redirect if user already completed onboarding
if ((window.location.pathname === '/' || window.location.pathname === '/app') && localStorage.getItem('matchme_onboarding_completed') === 'true') {
    window.location.replace('/discovery');
}

// On Page Load
document.addEventListener('DOMContentLoaded', async () => {
    // Preset default date
    const dateInput = document.getElementById('input-birthdate');
    if (dateInput) {
        dateInput.value = state.birthDate;
        calculateAge();
    }

    // Get user from Telegram WebApp
    const tgUser = tg?.initDataUnsafe?.user || {
        id: window.APP_DEFAULT_USER_ID || 123456789,
        first_name: 'Jasur',
        last_name: 'Aliyev',
        username: 'jasur_aliyev',
        language_code: 'uz'
    };

    try {
        const res = await fetch('/api/onboarding/init', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'lang': 'uz'
            },
            body: JSON.stringify({ telegram_user: tgUser })
        });

        const data = await res.json();
        if (data.status && data.data?.user) {
            state.userId = data.data.user.id;
            state.isTermsAccepted = data.data.user.is_terms_accepted;
            
            const isCompleted = data.data.is_completed || data.data.user.onboarding_completed;
            const currentPath = window.location.pathname;

            if (isCompleted) {
                localStorage.setItem('matchme_onboarding_completed', 'true');
            }

            // If onboarding is completed and user is on welcome/register page, jump straight to Discovery!
            if (isCompleted && (currentPath === '/' || currentPath === '/app')) {
                window.location.replace('/discovery');
                return;
            }

            if (data.data.user.name) {
                state.name = data.data.user.name;
                const nameInput = document.getElementById('input-name');
                if (nameInput) nameInput.value = state.name;
            } else if (tgUser.first_name) {
                state.name = `${tgUser.first_name} ${tgUser.last_name || ''}`.trim();
                const nameInput = document.getElementById('input-name');
                if (nameInput) nameInput.value = state.name;
            }

            if (data.data.user.photo_urls) {
                state.photos = data.data.user.photo_urls;
                renderPhotoSlots(state.photos);
            }
        }

        // Smoothly reveal onboarding for new users
        const splashEl = document.getElementById('app-splash');
        const onboardingWrapper = document.getElementById('onboarding-main-wrapper');
        const welcomeScreen = document.getElementById('screen-welcome');

        if (splashEl) {
            splashEl.style.opacity = '0';
            setTimeout(() => { splashEl.style.display = 'none'; }, 300);
        }
        if (onboardingWrapper) {
            onboardingWrapper.style.display = 'flex';
        }
        if (welcomeScreen && !document.querySelector('.screen.active')) {
            welcomeScreen.classList.add('active');
        }
    } catch (e) {
        console.log('Init error:', e);
        const splashEl = document.getElementById('app-splash');
        const onboardingWrapper = document.getElementById('onboarding-main-wrapper');
        const welcomeScreen = document.getElementById('screen-welcome');
        if (splashEl) splashEl.style.display = 'none';
        if (onboardingWrapper) onboardingWrapper.style.display = 'flex';
        if (welcomeScreen) welcomeScreen.classList.add('active');
    }
});

function haptic() {
    if (tg?.HapticFeedback) {
        tg.HapticFeedback.impactOccurred('medium');
    }
}

// Terms toggle
function toggleTerms() {
    haptic();
    state.isTermsAccepted = !state.isTermsAccepted;
    const checkbox = document.getElementById('terms-checkbox');
    const btnStart = document.getElementById('btn-start-welcome');

    if (state.isTermsAccepted) {
        checkbox.classList.add('checked');
        btnStart.classList.remove('disabled');
        btnStart.removeAttribute('disabled');
    } else {
        checkbox.classList.remove('checked');
        btnStart.classList.add('disabled');
        btnStart.setAttribute('disabled', 'true');
    }
}

async function startOnboarding() {
    if (!state.isTermsAccepted) return;
    haptic();

    // Save terms to DB
    try {
        await fetch('/api/onboarding/terms', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'lang': 'uz'
            },
            body: JSON.stringify({ user_id: state.userId })
        });
    } catch (e) {}

    goToStep(1);
}

function goToStep(stepNumber) {
    haptic();
    document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
    const targetScreen = document.getElementById(`screen-step-${stepNumber}`);
    if (targetScreen) {
        targetScreen.classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// Step 1: Submit Name
async function submitStep1() {
    const nameVal = document.getElementById('input-name').value.trim();
    if (!nameVal) {
        alert('Iltimos, ismingizni kiriting!');
        return;
    }

    state.name = nameVal;
    await saveStepApi(1, { name: state.name });
    goToStep(2);
}

// Step 2: Calculate Age & Submit
function calculateAge() {
    const dateInput = document.getElementById('input-birthdate');
    if (!dateInput) return;
    const dateVal = dateInput.value;
    if (!dateVal) return;

    const birthDate = new Date(dateVal);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    state.age = age;
    state.birthDate = dateVal;

    const ageCard = document.getElementById('age-result-card');
    const ageBadge = document.getElementById('age-badge-text');
    const btnNext = document.getElementById('btn-submit-step2');

    if (ageCard && ageBadge && btnNext) {
        ageCard.style.display = 'flex';
        if (age >= 18) {
            ageBadge.className = 'age-badge';
            ageBadge.innerText = `${age} yosh (18+)`;
            btnNext.classList.remove('disabled');
            btnNext.removeAttribute('disabled');
        } else {
            ageBadge.className = 'age-badge invalid';
            ageBadge.innerText = `${age} yosh (18+ talab qilinadi)`;
            btnNext.classList.add('disabled');
            btnNext.setAttribute('disabled', 'true');
        }
    }
}

async function submitStep2() {
    if (state.age < 18) {
        alert('Xizmatdan faqat 18 yoshdan kattalar foydalanishi mumkin!');
        return;
    }

    await saveStepApi(2, { birth_date: state.birthDate });
    goToStep(3);
}

// Step 3: Gender & Looking For
function selectGender(val) {
    haptic();
    state.gender = val;
    document.getElementById('gender-male').classList.toggle('selected', val === 'male');
    document.getElementById('gender-female').classList.toggle('selected', val === 'female');
}

function selectLooking(val) {
    haptic();
    state.lookingFor = val;
    document.getElementById('looking-female').classList.toggle('selected', val === 'female');
    document.getElementById('looking-male').classList.toggle('selected', val === 'male');
}

async function submitStep3() {
    await saveStepApi(3, { gender: state.gender, looking_for: state.lookingFor });
    goToStep(4);
}

// Step 4: City & Geolocation
function detectGeolocation() {
    haptic();
    const geoText = document.getElementById('geo-text');
    if (!navigator.geolocation) {
        alert('Qurilmangiz geolokatsiyani qo\'llab-quvvatlamaydi');
        return;
    }

    geoText.innerText = 'Aniqlanmoqda...';

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            state.latitude = lat;
            state.longitude = lng;

            let detectedCity = 'tashkent_city';
            if (lat < 38.5) detectedCity = 'surkhandarya';
            else if (lat < 39.5 && lng < 67) detectedCity = 'kashkadarya';
            else if (lng < 62) detectedCity = 'karakalpakstan';
            else if (lng < 63) detectedCity = 'khorezm';
            else if (lng < 65) detectedCity = 'bukhara';
            else if (lng < 66) detectedCity = 'navoi';
            else if (lng < 68) detectedCity = 'samarkand';
            else if (lng < 69.5) detectedCity = 'jizzakh';
            else if (lng > 71.5) detectedCity = 'andijan';
            else if (lng > 71) detectedCity = 'namangan';
            else if (lng > 70.5) detectedCity = 'fergana';
            else detectedCity = 'tashkent_city';

            const citySelect = document.getElementById('input-city');
            if (citySelect) {
                citySelect.value = detectedCity;
                state.city = detectedCity;
            }

            geoText.innerText = '✅ Geolokatsiya aniqlandi!';
        },
        (err) => {
            geoText.innerText = 'Geolokatsiyani avto-aniqlash';
            alert('Geolokatsiyaga ruxsat berilmadi. Shaharni ro\'yxatdan tanlang.');
        }
    );
}

async function submitStep4() {
    state.city = document.getElementById('input-city').value || 'tashkent_city';
    await saveStepApi(4, { city: state.city, latitude: state.latitude, longitude: state.longitude });
    goToStep(5);
}

// Step 5: Bio
function updateBioCounter() {
    const val = document.getElementById('input-bio').value;
    document.getElementById('bio-counter').innerText = `${val.length} / 250`;
}

async function submitStep5() {
    state.bio = document.getElementById('input-bio').value.trim();
    await saveStepApi(5, { bio: state.bio });
    goToStep(6);
}

// Step 6: Photo Upload
function triggerPhotoUpload(slotIndex) {
    haptic();
    state.currentSlotUploading = slotIndex;
    document.getElementById('file-upload-input').click();
}

async function handlePhotoUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('photo', file);
    if (state.userId) {
        formData.append('user_id', state.userId);
    }

    try {
        const res = await fetch('/api/onboarding/upload-photo', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'lang': 'uz'
            },
            body: formData
        });

        const data = await res.json();
        if (data.status && data.data?.photos) {
            renderPhotoSlots(data.data.photos);
        }
    } catch (err) {
        console.log('Upload error:', err);
    }

    e.target.value = '';
}

function renderPhotoSlots(photos = []) {
    state.photos = photos;
    for (let i = 0; i < 3; i++) {
        const slot = document.getElementById(`photo-slot-${i}`);
        if (!slot) continue;

        if (photos[i]) {
            slot.className = 'photo-slot has-image';
            slot.innerHTML = `
                <img src="${photos[i]}" alt="Photo ${i+1}">
                <div class="photo-slot-delete" onclick="event.stopPropagation(); deletePhoto(${i})">&times;</div>
            `;
        } else {
            slot.className = 'photo-slot';
            slot.innerHTML = `
                <span class="photo-slot-plus">+</span>
                <span class="photo-slot-label">${i === 0 ? 'Asosiy rasm' : (i+1) + '-rasm'}</span>
            `;
        }
    }
}

async function deletePhoto(index) {
    haptic();
    try {
        const res = await fetch('/api/onboarding/delete-photo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'lang': 'uz'
            },
            body: JSON.stringify({ user_id: state.userId, photo_index: index })
        });
        const data = await res.json();
        if (data.status) {
            renderPhotoSlots(data.data.photos);
        }
    } catch (e) {}
}

async function submitStep6() {
    if (state.photos.length === 0) {
        state.photos = ['https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80'];
    }

    await saveStepApi(6, { photos: state.photos });
    localStorage.setItem('matchme_onboarding_completed', 'true');

    // Show celebration
    haptic();
    if (typeof confetti === 'function') {
        confetti({
            particleCount: 120,
            spread: 80,
            origin: { y: 0.6 }
        });
    }

    document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
    document.getElementById('screen-success').classList.add('active');

    setTimeout(() => {
        window.location.replace('/discovery');
    }, 1500);
}

// Generic API Step saver
async function saveStepApi(step, payload) {
    haptic();
    try {
        await fetch('/api/onboarding/step', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'lang': 'uz'
            },
            body: JSON.stringify({
                user_id: state.userId,
                step: step,
                ...payload
            })
        });
    } catch (e) {
        console.log(`Step ${step} error:`, e);
    }
}

function openDiscovery() {
    haptic();
    window.location.href = '/discovery';
}

// Navigation Tab Switcher
function switchNavTab(e, tabName) {
    if (e) e.preventDefault();
    haptic();

    document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
    const activeItem = document.getElementById(`nav-item-${tabName}`);
    if (activeItem) activeItem.classList.add('active');
}

// Card Swipe & Actions (Dislike / Gift / Like)
const sampleProfiles = [
    {
        name: 'Madina, 23',
        is_vip: true,
        is_verified: true,
        city: 'Toshkent, 3 km',
        goal: 'Nikoh & oila',
        bio: "Dizayner & musiqachi 🎨 Sevimli mashg'ulotim — kofe bilan kitob o'qish va kechki shahar sayrlari.",
        tags: ['☕ Coffee', '🎨 UI/UX', '✈️ Travel', '🎬 Netflix'],
        image: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&auto=format&fit=crop&q=80'
    },
    {
        name: 'Kamila, 21',
        is_vip: false,
        is_verified: true,
        city: 'Samarqand, 12 km',
        goal: 'Do\'stlik & suhbat',
        bio: "Filologiya talabasiman 📚 Chet tillarini o'rganish va sayohat qilishni yaxshi ko'raman.",
        tags: ['📚 Books', '🌍 Travel', '🎧 Music'],
        image: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=800&auto=format&fit=crop&q=80'
    },
    {
        name: 'Zuhra, 24',
        is_vip: true,
        is_verified: true,
        city: 'Toshkent, 5 km',
        goal: 'Jiddiy munosabat',
        bio: "Marketing mutaxassisi 📈 Sport, yoga va shinam qahvaxonalarni xush ko'raman.",
        tags: ['🧘‍♀️ Yoga', '☕ Coffee', '🏃‍♀️ Sport'],
        image: 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=800&auto=format&fit=crop&q=80'
    }
];

let currentProfileIndex = 0;

function handleCardAction(action) {
    haptic();
    const photoBox = document.getElementById('card-photo-box');
    if (!photoBox) return;

    if (action === 'like') {
        photoBox.style.transform = 'scale(1.05) rotate(4deg)';
        photoBox.style.opacity = '0.4';
        confetti({ particleCount: 60, spread: 70, origin: { y: 0.7 } });
    } else if (action === 'dislike') {
        photoBox.style.transform = 'scale(0.95) rotate(-4deg)';
        photoBox.style.opacity = '0.4';
    } else if (action === 'gift') {
        photoBox.style.transform = 'scale(1.08)';
        confetti({ particleCount: 40, spread: 50, origin: { y: 0.7 } });
    }

    setTimeout(() => {
        currentProfileIndex = (currentProfileIndex + 1) % sampleProfiles.length;
        loadProfileCard(sampleProfiles[currentProfileIndex]);
        photoBox.style.transition = 'none';
        photoBox.style.transform = 'none';
        photoBox.style.opacity = '1';
        setTimeout(() => { photoBox.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)'; }, 50);
    }, 280);
}

function loadProfileCard(profile) {
    const cardImg = document.getElementById('card-bg-image');
    const nameEl = document.getElementById('user-name-val');
    const cityEl = document.getElementById('user-city-val');
    const tagsBox = document.getElementById('user-tags-box');

    if (cardImg) cardImg.src = profile.image;
    if (nameEl) nameEl.innerText = profile.name;
    if (cityEl) cityEl.innerText = profile.city;
    if (tagsBox && profile.tags) {
        tagsBox.innerHTML = profile.tags.map(t => `<span class="user-tag-pill">${t}</span>`).join('');
    }
}

function openReportModal() {
    haptic();
}

function toggleProfileDetails() {
    haptic();
}

function openBonusModal() {
    haptic();
}

// ==================== WALLET & DEPOSIT LOGIC ====================
let selectedDepositFile = null;

function openBalanceModal() {
    haptic();
    const modal = document.getElementById('wallet-modal-overlay');
    if (!modal) return;

    // Fetch user balance
    if (state.userId) {
        fetch(`/api/wallet/balance?user_id=${state.userId}`, {
            headers: { 'lang': 'uz' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status && res.data) {
                const balEl = document.getElementById('modal-current-balance');
                if (balEl) balEl.innerText = `Balans: ${res.data.formatted_balance}`;
                if (res.data.card_number) {
                    const cardEl = document.getElementById('wallet-card-number');
                    if (cardEl) cardEl.innerText = res.data.card_number;
                }
            }
        })
        .catch(e => console.log('Wallet load error:', e));
    }

    modal.classList.add('active');
    checkDepositValidity();
}

function closeWalletModal(e) {
    if (e) e.stopPropagation();
    haptic();
    const modal = document.getElementById('wallet-modal-overlay');
    if (modal) modal.classList.remove('active');
}

function checkDepositValidity() {
    const amountInput = document.getElementById('input-deposit-amount');
    const amount = parseFloat(amountInput ? amountInput.value : 0);
    const btn = document.getElementById('btn-submit-deposit');

    const isValid = amount >= 1000 && selectedDepositFile !== null;

    if (btn) {
        btn.disabled = !isValid;
        btn.classList.toggle('disabled', !isValid);
    }
}

function selectPresetAmount(amount) {
    haptic();
    const input = document.getElementById('input-deposit-amount');
    if (input) input.value = amount;

    document.querySelectorAll('.preset-btn').forEach(btn => btn.classList.remove('active'));
    const activeBtn = document.getElementById(`preset-${amount}`);
    if (activeBtn) activeBtn.classList.add('active');

    checkDepositValidity();
}

function onCustomAmountChange(val) {
    const num = parseInt(val, 10);
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.classList.toggle('active', btn.id === `preset-${num}`);
    });

    checkDepositValidity();
}

function copyCardNumber() {
    haptic();
    const cardEl = document.getElementById('wallet-card-number');
    const cardText = (cardEl ? cardEl.innerText : '5614 6819 1495 1557').replace(/\s+/g, '');
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(cardText).catch(() => {});
    }

    const copyBtn = document.querySelector('.btn-copy-card');
    if (copyBtn) {
        const originalHtml = copyBtn.innerHTML;
        copyBtn.innerHTML = '<span>✔ Nusxalandi!</span>';
        setTimeout(() => { copyBtn.innerHTML = originalHtml; }, 1800);
    }
}

function triggerReceiptFileSelect() {
    haptic();
    document.getElementById('input-receipt-file')?.click();
}

function handleReceiptFileSelected(event) {
    const file = event.target.files[0];
    if (!file) return;

    selectedDepositFile = file;

    const emptyView = document.getElementById('receipt-empty-view');
    const previewView = document.getElementById('receipt-preview-view');
    const previewImg = document.getElementById('receipt-preview-img');
    const nameEl = document.getElementById('receipt-file-name');

    if (nameEl) nameEl.innerText = file.name;

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            if (previewImg) previewImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        if (previewImg) previewImg.src = '/assets/images/pdf-icon.png';
    }

    if (emptyView) emptyView.style.display = 'none';
    if (previewView) previewView.style.display = 'flex';

    checkDepositValidity();
}

function removeReceiptFile(event) {
    if (event) event.stopPropagation();
    haptic();

    selectedDepositFile = null;
    const fileInput = document.getElementById('input-receipt-file');
    if (fileInput) fileInput.value = '';

    const emptyView = document.getElementById('receipt-empty-view');
    const previewView = document.getElementById('receipt-preview-view');

    if (emptyView) emptyView.style.display = 'flex';
    if (previewView) previewView.style.display = 'none';

    checkDepositValidity();
}

async function submitDepositPayment() {
    haptic();
    const amountInput = document.getElementById('input-deposit-amount');
    const amount = parseFloat(amountInput ? amountInput.value : 0);

    if (!amount || amount < 1000) {
        amountInput?.focus();
        return;
    }

    const btn = document.getElementById('btn-submit-deposit');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>⏳ Tekshirilmoqda...</span>';
    }

    const formData = new FormData();
    formData.append('user_id', state.userId || window.APP_DEFAULT_USER_ID || 1);
    formData.append('amount', amount);
    if (selectedDepositFile) {
        formData.append('receipt', selectedDepositFile);
    }

    try {
        const res = await fetch('/api/wallet/deposit', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'lang': 'uz'
            },
            body: formData
        });

        const data = await res.json();
        if (data.status) {
            closeWalletModal();
            removeReceiptFile();
            if (typeof confetti === 'function') {
                confetti({ particleCount: 70, spread: 60, origin: { y: 0.7 } });
            }
        }
    } catch (e) {
        console.log('Deposit error:', e);
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span class="btn-icon">✔</span><span>To\'lovni Tasdiqlash & Balansga Qo\'shish</span>';
        }
    }
}

// ==================== BOOST LOGIC ====================
let selectedBoostPlanId = 2;
let selectedBoostPrice = 20000;

function openBoostModal() {
    haptic();
    const modal = document.getElementById('boost-modal-overlay');
    if (!modal) return;

    // Fetch user balance and boost status
    if (state.userId) {
        fetch(`/api/boost/status?user_id=${state.userId}`, {
            headers: { 'lang': 'uz' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status && res.data) {
                const balEl = document.getElementById('boost-modal-balance');
                if (balEl) balEl.innerText = `Balans: ${res.data.formatted_balance}`;
            }
        })
        .catch(e => console.log('Boost status error:', e));
    }

    modal.classList.add('active');
}

function closeBoostModal(e) {
    if (e) e.stopPropagation();
    haptic();
    const modal = document.getElementById('boost-modal-overlay');
    if (modal) modal.classList.remove('active');
}

function selectBoostPlan(planId, price) {
    haptic();
    selectedBoostPlanId = parseInt(planId, 10);
    selectedBoostPrice = price;

    document.querySelectorAll('.boost-plan-card').forEach(card => card.classList.remove('active'));
    const activeCard = document.getElementById(`boost-plan-${planId}`);
    if (activeCard) activeCard.classList.add('active');

    const labelEl = document.getElementById('btn-boost-label');
    if (labelEl) {
        labelEl.innerText = `Balansdan Faollashtirish (${price.toLocaleString()} UZS)`;
    }
}

async function activateSelectedBoost() {
    haptic();
    const btn = document.getElementById('btn-activate-boost');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>⏳ Faollashtirilmoqda...</span>';
    }

    try {
        const res = await fetch('/api/boost/activate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'lang': 'uz'
            },
            body: JSON.stringify({
                user_id: state.userId || window.APP_DEFAULT_USER_ID || 1,
                plan_id: selectedBoostPlanId
            })
        });

        const data = await res.json();
        if (data.status) {
            closeBoostModal();
            if (typeof confetti === 'function') {
                confetti({ particleCount: 90, spread: 80, origin: { y: 0.6 } });
            }
        } else {
            // Insufficient balance: redirect user to Top up Wallet
            closeBoostModal();
            openBalanceModal();
            if (data.data?.required_amount) {
                selectPresetAmount(Math.max(20000, data.data.required_amount));
            }
        }
    } catch (e) {
        console.log('Boost activate error:', e);
        closeBoostModal();
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<span class="btn-icon">⚡</span><span id="btn-boost-label">Balansdan Faollashtirish (${selectedBoostPrice.toLocaleString()} UZS)</span>`;
        }
    }
}

// Discovery Filter Bottom Sheet Logic
const filterState = {
    looking_for: 'female',
    min_age: 18,
    max_age: 28,
    max_distance_km: 50,
    city: 'all'
};

function openFilterModal() {
    haptic();
    const modal = document.getElementById('filter-modal-overlay');
    if (!modal) return;

    // Load filter from server or current state
    if (state.userId) {
        fetch(`/api/discovery/filter?user_id=${state.userId}`, {
            headers: { 'lang': 'uz' }
        })
        .then(res => res.json())
        .then(res => {
            if (res.status && res.data) {
                applyFilterToUI(res.data);
            }
        })
        .catch(e => console.log('Filter load error:', e));
    }

    modal.classList.add('active');
}

function closeFilterModal(e) {
    if (e) e.stopPropagation();
    haptic();
    const modal = document.getElementById('filter-modal-overlay');
    if (modal) modal.classList.remove('active');
}

function selectFilterGender(val) {
    haptic();
    filterState.looking_for = val;
    document.getElementById('f-gender-female')?.classList.toggle('active', val === 'female');
    document.getElementById('f-gender-male')?.classList.toggle('active', val === 'male');
    document.getElementById('f-gender-all')?.classList.toggle('active', val === 'all');
}

function updateFilterAge(val) {
    filterState.max_age = parseInt(val, 10);
    const label = document.getElementById('label-filter-age');
    if (label) label.innerText = `18 – ${val} yosh`;
}

function updateFilterDist(val) {
    filterState.max_distance_km = parseInt(val, 10);
    const label = document.getElementById('label-filter-dist');
    if (label) label.innerText = `${val} km`;
}

function applyFilterToUI(data) {
    if (data.looking_for) selectFilterGender(data.looking_for);
    if (data.max_age) {
        const slider = document.getElementById('input-filter-age');
        if (slider) {
            slider.value = data.max_age;
            updateFilterAge(data.max_age);
        }
    }
    if (data.max_distance_km) {
        const slider = document.getElementById('input-filter-dist');
        if (slider) {
            slider.value = data.max_distance_km;
            updateFilterDist(data.max_distance_km);
        }
    }
    if (data.city) {
        const citySelect = document.getElementById('select-filter-city');
        if (citySelect) citySelect.value = data.city;
    }
}

async function saveFilterSettings() {
    haptic();
    filterState.city = document.getElementById('select-filter-city')?.value || 'all';

    // Instantly close modal smoothly
    closeFilterModal();

    try {
        const res = await fetch('/api/discovery/filter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'lang': 'uz'
            },
            body: JSON.stringify({
                user_id: state.userId,
                ...filterState
            })
        });

        const data = await res.json();
        if (data.status) {
            // Refresh cards smoothly
            currentProfileIndex = 0;
            loadProfileCard(sampleProfiles[0]);
        }
    } catch (e) {
        console.log('Filter save error:', e);
    }
}

// Modal controls
function openTermsModal() {
    document.getElementById('terms-modal').classList.add('active');
}

function openPrivacyModal() {
    document.getElementById('privacy-modal').classList.add('active');
}

function closeModals() {
    haptic();
    document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
}

// ==================== LIKES & GIFTS ACTIONS ====================
async function handleLikeAccept(likeId) {
    haptic();
    const card = document.getElementById(`like-card-${likeId}`);
    if (card) {
        card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        card.style.transform = 'scale(1.08)';
        card.style.opacity = '0';
        setTimeout(() => { 
            card.remove(); 
            updateLikesBadge(); 
        }, 280);
    }

    if (typeof confetti === 'function') {
        confetti({ particleCount: 70, spread: 60, origin: { y: 0.6 } });
    }

    try {
        await fetch('/api/likes/accept', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'lang': 'uz'
            },
            body: JSON.stringify({
                user_id: state.userId || window.APP_DEFAULT_USER_ID || 1,
                like_id: likeId
            })
        });
    } catch (e) {
        console.log('Like accept error:', e);
    }
}

async function handleLikeReject(likeId) {
    haptic();
    const card = document.getElementById(`like-card-${likeId}`);
    if (card) {
        card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        card.style.transform = 'scale(0.85)';
        card.style.opacity = '0';
        setTimeout(() => { 
            card.remove(); 
            updateLikesBadge(); 
        }, 280);
    }

    try {
        await fetch('/api/likes/reject', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'lang': 'uz'
            },
            body: JSON.stringify({
                user_id: state.userId || window.APP_DEFAULT_USER_ID || 1,
                like_id: likeId
            })
        });
    } catch (e) {
        console.log('Like reject error:', e);
    }
}

function updateLikesBadge() {
    const vipCount = document.querySelectorAll('.vip-profile-card').length;
    const regCount = document.querySelectorAll('.regular-like-card').length;
    const totalLikes = vipCount + regCount;

    const badge = document.getElementById('badge-likes-count');
    const regularBadge = document.getElementById('regular-count-badge');
    const vipBadge = document.getElementById('vip-count-badge');
    const vipSection = document.getElementById('vip-gifts-section');
    const regSection = document.getElementById('regular-likes-section');
    const scrollContent = document.querySelector('.likes-scroll-content');

    if (badge) badge.innerText = totalLikes;
    if (regularBadge) regularBadge.innerText = `${regCount} ta yangi`;
    if (vipBadge) vipBadge.innerText = `${vipCount} ta sovg'a`;

    if (vipCount === 0 && vipSection) {
        vipSection.remove();
    }
    if (regCount === 0 && regSection) {
        regSection.remove();
    }

    if (totalLikes === 0 && scrollContent && !document.getElementById('likes-empty-container')) {
        scrollContent.innerHTML = `
            <div class="likes-empty-container" id="likes-empty-container">
                <div class="empty-glow-circle">
                    <svg viewBox="0 0 24 24" class="empty-heart-svg">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
                <h3 class="empty-title">Hozircha yangi layklar yo'q</h3>
                <p class="empty-description">
                    Profilni yanada ko'proq insonlar ko'rishi uchun tanishuvlar orqali birinchi bo'lib layk bosing yoki profilingizni faollashtiring ✨
                </p>
                <a href="/discovery" class="btn-go-discovery">
                    <span class="btn-go-icon">🎴</span>
                    <span>Tanishuvlarga O'tish</span>
                    <span class="btn-go-arrow">➔</span>
                </a>
            </div>
        `;
    }
}

// Load balance on Likes Page load
if (window.location.pathname.includes('/likes')) {
    document.addEventListener('DOMContentLoaded', () => {
        const uid = state.userId || window.APP_DEFAULT_USER_ID || 1;
        fetch(`/api/wallet/balance?user_id=${uid}`, { headers: { 'lang': 'uz' } })
            .then(r => r.json())
            .then(res => {
                if (res.status && res.data) {
                    const el = document.getElementById('likes-page-balance');
                    if (el) el.innerText = res.data.formatted_balance;
                }
            })
            .catch(() => {});
    });
}
