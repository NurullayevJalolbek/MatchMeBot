/**
 * MatchMe Telegram Mini-App Client Logic
 */

// State management (No mock defaults)
const state = {
    userId: null,
    isTermsAccepted: false,
    name: '',
    birthDate: '',
    age: null,
    gender: null,
    lookingFor: null,
    city: '',
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
        if (typeof tg.setHeaderColor === 'function') tg.setHeaderColor('#0c0e17');
        if (typeof tg.setBackgroundColor === 'function') tg.setBackgroundColor('#0c0e17');
        if (typeof tg.setBottomBarColor === 'function') tg.setBottomBarColor('#0c0e17');
    } catch (e) {}
}

// On Page Load
document.addEventListener('DOMContentLoaded', async () => {
    // Get user from Telegram WebApp if available
    const tgUser = tg?.initDataUnsafe?.user || {
        id: window.APP_DEFAULT_USER_ID || null,
        first_name: '',
        last_name: '',
        username: '',
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
                if (currentPath === '/' || currentPath === '/app') {
                    window.location.replace('/discovery');
                    return;
                }
            } else {
                localStorage.removeItem('matchme_onboarding_completed');
                if (currentPath === '/discovery') {
                    window.location.replace('/');
                    return;
                }
            }

            if (data.data.user.name) {
                state.name = data.data.user.name;
                const nameInput = document.getElementById('input-name');
                if (nameInput) {
                    nameInput.value = state.name;
                    checkStep1Valid();
                }
            } else if (tgUser.first_name) {
                state.name = `${tgUser.first_name} ${tgUser.last_name || ''}`.trim();
                const nameInput = document.getElementById('input-name');
                if (nameInput) {
                    nameInput.value = state.name;
                    checkStep1Valid();
                }
            }

            if (data.data.user.birth_date) {
                state.birthDate = data.data.user.birth_date.split('T')[0];
                const dateInput = document.getElementById('input-birthdate');
                if (dateInput) {
                    dateInput.value = state.birthDate;
                    calculateAge();
                }
            }

            if (data.data.user.gender) {
                selectGender(data.data.user.gender);
            }
            if (data.data.user.looking_for) {
                selectLooking(data.data.user.looking_for);
            }

            if (data.data.user.city) {
                state.city = data.data.user.city;
                const citySelect = document.getElementById('input-city');
                if (citySelect) {
                    citySelect.value = state.city;
                    checkStep4Valid();
                }
            }

            if (data.data.user.bio) {
                state.bio = data.data.user.bio;
                const bioInput = document.getElementById('input-bio');
                if (bioInput) {
                    bioInput.value = state.bio;
                    updateBioCounter();
                    checkStep5Valid();
                }
            }

            if (data.data.user.photo_urls) {
                state.photos = data.data.user.photo_urls;
                renderPhotoSlots(state.photos);
            }

            if (window.location.pathname.includes('/discovery')) {
                fetchDiscoveryCandidates();
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

function haptic(style = 'medium') {
    try {
        if (tg && typeof tg.isVersionAtLeast === 'function' && tg.isVersionAtLeast('6.1') && tg.HapticFeedback) {
            tg.HapticFeedback.impactOccurred(style);
        }
    } catch (e) {}
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

function startOnboarding() {
    haptic();
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

// ==================== STEP 1: ISMINGIZ ====================
function checkStep1Valid() {
    const nameVal = document.getElementById('input-name')?.value.trim() || '';
    const btn = document.getElementById('btn-submit-step1');
    const isValid = nameVal.length >= 2;
    if (btn) {
        btn.disabled = !isValid;
        btn.classList.toggle('disabled', !isValid);
    }
    return isValid;
}

async function submitStep1() {
    if (!checkStep1Valid()) {
        if (window.showToast) window.showToast('Iltimos, ismingizni kiriting (kamida 2 ta belgi)!');
        return;
    }

    state.name = document.getElementById('input-name').value.trim();
    const ok = await saveStepApi(1, { name: state.name });
    if (ok) goToStep(2);
}

// ==================== STEP 2: YOSHINGIZ ====================
function calculateAge() {
    const dateInput = document.getElementById('input-birthdate');
    if (!dateInput || !dateInput.value) {
        const btn = document.getElementById('btn-submit-step2');
        if (btn) {
            btn.disabled = true;
            btn.classList.add('disabled');
        }
        return false;
    }

    const birthDate = new Date(dateInput.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    state.age = age;
    state.birthDate = dateInput.value;

    const ageCard = document.getElementById('age-result-card');
    const ageBadge = document.getElementById('age-badge-text');
    const btnNext = document.getElementById('btn-submit-step2');

    const isValid = age >= 18 && age <= 100;

    if (ageCard && ageBadge && btnNext) {
        ageCard.style.display = 'flex';
        if (isValid) {
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
    return isValid;
}

async function submitStep2() {
    if (!calculateAge() || !state.birthDate || state.age < 18) {
        if (window.showToast) window.showToast('Xizmatdan faqat 18 yoshdan oshganlar foydalanishi mumkin!');
        return;
    }

    const ok = await saveStepApi(2, { birth_date: state.birthDate });
    if (ok) goToStep(3);
}

// ==================== STEP 3: JINSINGIZ & QIDIRUV ====================
function checkStep3Valid() {
    const btn = document.getElementById('btn-submit-step3');
    const isValid = Boolean(state.gender && state.lookingFor);
    if (btn) {
        btn.disabled = !isValid;
        btn.classList.toggle('disabled', !isValid);
    }
    return isValid;
}

function selectGender(val) {
    haptic();
    state.gender = val;
    document.getElementById('gender-male')?.classList.toggle('selected', val === 'male');
    document.getElementById('gender-female')?.classList.toggle('selected', val === 'female');
    checkStep3Valid();
}

function selectLooking(val) {
    haptic();
    state.lookingFor = val;
    document.getElementById('looking-female')?.classList.toggle('selected', val === 'female');
    document.getElementById('looking-male')?.classList.toggle('selected', val === 'male');
    checkStep3Valid();
}

async function submitStep3() {
    if (!checkStep3Valid()) {
        if (window.showToast) window.showToast('Iltimos, jinsingiz va kimni qidirayotganingizni tanlang!');
        return;
    }

    const ok = await saveStepApi(3, { gender: state.gender, looking_for: state.lookingFor });
    if (ok) goToStep(4);
}

// ==================== STEP 4: SHAHRINGIZ ====================
function checkStep4Valid() {
    const cityVal = document.getElementById('input-city')?.value || '';
    state.city = cityVal;
    const btn = document.getElementById('btn-submit-step4');
    const isValid = cityVal !== '' && cityVal !== null;
    if (btn) {
        btn.disabled = !isValid;
        btn.classList.toggle('disabled', !isValid);
    }
    return isValid;
}

function detectGeolocation() {
    haptic();
    const geoText = document.getElementById('geo-text');
    if (!navigator.geolocation) {
        if (window.showToast) window.showToast('Qurilmangiz geolokatsiyani qo\'llab-quvvatlamaydi');
        return;
    }

    geoText.innerText = 'Aniqlanmoqda...';

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            state.latitude = lat;
            state.longitude = lng;

            const regions = [
                { city: 'tashkent_city',  minLat: 41.15, maxLat: 41.50, minLng: 69.10, maxLng: 69.55 },
                { city: 'tashkent_region',minLat: 40.60, maxLat: 41.80, minLng: 68.70, maxLng: 70.30 },
                { city: 'sirdaryo',       minLat: 40.20, maxLat: 41.00, minLng: 67.80, maxLng: 70.00 },
                { city: 'jizzakh',        minLat: 39.80, maxLat: 40.80, minLng: 65.90, maxLng: 68.80 },
                { city: 'samarkand',      minLat: 38.80, maxLat: 40.20, minLng: 65.60, maxLng: 67.80 },
                { city: 'kashkadarya',    minLat: 38.00, maxLat: 39.70, minLng: 65.00, maxLng: 67.50 },
                { city: 'surkhandarya',   minLat: 37.00, maxLat: 38.60, minLng: 66.80, maxLng: 68.80 },
                { city: 'namangan',       minLat: 40.70, maxLat: 41.80, minLng: 70.50, maxLng: 72.00 },
                { city: 'andijan',        minLat: 40.50, maxLat: 41.20, minLng: 71.80, maxLng: 73.20 },
                { city: 'fergana',        minLat: 39.90, maxLat: 40.90, minLng: 70.60, maxLng: 72.20 },
                { city: 'navoi',          minLat: 39.50, maxLat: 41.50, minLng: 62.00, maxLng: 66.50 },
                { city: 'bukhara',        minLat: 38.50, maxLat: 40.40, minLng: 62.00, maxLng: 65.50 },
                { city: 'khorezm',        minLat: 41.00, maxLat: 42.00, minLng: 59.80, maxLng: 62.00 },
                { city: 'karakalpakstan', minLat: 42.00, maxLat: 45.50, minLng: 55.00, maxLng: 62.00 },
            ];

            let detectedCity = 'tashkent_city';
            for (const r of regions) {
                if (lat >= r.minLat && lat <= r.maxLat && lng >= r.minLng && lng <= r.maxLng) {
                    detectedCity = r.city;
                    break;
                }
            }

            const citySelect = document.getElementById('input-city');
            if (citySelect) {
                citySelect.value = detectedCity;
                state.city = detectedCity;
                checkStep4Valid();
            }

            geoText.innerText = '✅ Geolokatsiya aniqlandi!';
        },
        (err) => {
            geoText.innerText = 'Geolokatsiyani avto-aniqlash';
            if (window.showToast) window.showToast('Lokatsiyani aniqlab bo\'lmadi. Shahringizni ro\'yxatdan tanlang.');
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 60000
        }
    );
}

async function submitStep4() {
    if (!checkStep4Valid()) {
        if (window.showToast) window.showToast('Iltimos, shahringizni tanlang!');
        return;
    }

    state.city = document.getElementById('input-city').value;
    const ok = await saveStepApi(4, { city: state.city, latitude: state.latitude, longitude: state.longitude });
    if (ok) goToStep(5);
}

// ==================== STEP 5: BIO ====================
function updateBioCounter() {
    const val = document.getElementById('input-bio')?.value || '';
    const counter = document.getElementById('bio-counter');
    if (counter) counter.innerText = `${val.length} / 250`;
}

function checkStep5Valid() {
    const bioVal = document.getElementById('input-bio')?.value.trim() || '';
    state.bio = bioVal;
    const btn = document.getElementById('btn-submit-step5');
    const isValid = bioVal.length >= 10;
    if (btn) {
        btn.disabled = !isValid;
        btn.classList.toggle('disabled', !isValid);
    }
    return isValid;
}

async function submitStep5() {
    if (!checkStep5Valid()) {
        if (window.showToast) window.showToast('Iltimos, o\'zingiz haqingizda kamida 10 ta belgi yozing!');
        return;
    }

    state.bio = document.getElementById('input-bio').value.trim();
    const ok = await saveStepApi(5, { bio: state.bio });
    if (ok) goToStep(6);
}

// ==================== STEP 6: RASMLAR ====================
function checkStep6Valid() {
    const btn = document.getElementById('btn-submit-step6');
    const isValid = Array.isArray(state.photos) && state.photos.length >= 1;
    if (btn) {
        btn.disabled = !isValid;
        btn.classList.toggle('disabled', !isValid);
    }
    return isValid;
}

function triggerPhotoUpload(slotIndex) {
    haptic();
    state.currentSlotUploading = slotIndex;
    document.getElementById('file-upload-input').click();
}

async function handlePhotoUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const localPreviewUrl = URL.createObjectURL(file);
    const slotIdx = state.currentSlotUploading || 0;
    const targetSlot = document.getElementById(`photo-slot-${slotIdx}`);
    if (targetSlot) {
        targetSlot.className = 'photo-slot has-image';
        targetSlot.innerHTML = `
            <img src="${localPreviewUrl}" alt="Photo ${slotIdx + 1}" style="opacity: 0.7;">
            <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); border-radius: 18px;">
                <div style="width: 22px; height: 22px; border: 2.5px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
            </div>
        `;
    }

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
                'Accept': 'application/json',
                'lang': 'uz'
            },
            body: formData
        });

        const data = await res.json();
        if (data.status && data.data?.photos) {
            renderPhotoSlots(data.data.photos);
        } else if (data.message) {
            if (window.showToast) window.showToast(data.message);
            renderPhotoSlots(state.photos);
        }
    } catch (err) {
        console.log('Upload error:', err);
        renderPhotoSlots(state.photos);
    }

    e.target.value = '';
}

function renderPhotoSlots(photos = []) {
    state.photos = photos;
    for (let i = 0; i < 3; i++) {
        const slot = document.getElementById(`photo-slot-${i}`);
        if (!slot) continue;

        const rawItem = photos[i];
        let photoUrl = '';
        if (typeof rawItem === 'object' && rawItem !== null) {
            photoUrl = rawItem.url || rawItem.file_path || '';
        } else if (typeof rawItem === 'string') {
            photoUrl = rawItem;
        }

        if (photoUrl.startsWith('http://localhost') || photoUrl.startsWith('http://127.0.0.1')) {
            photoUrl = photoUrl.replace(/^https?:\/\/[^\/]+/, '');
        }

        if (photoUrl) {
            slot.className = 'photo-slot has-image';
            slot.innerHTML = `
                <img src="${photoUrl}" alt="Photo ${i+1}">
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
    checkStep6Valid();
}

async function deletePhoto(index) {
    haptic();
    try {
        const res = await fetch('/api/onboarding/delete-photo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
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
    if (!checkStep6Valid()) {
        if (window.showToast) window.showToast('Profilingizni yakunlash uchun kamida 1 ta fotosurat yuklashingiz shart!');
        return;
    }

    const ok = await saveStepApi(6, { photos: state.photos });
    if (!ok) return;

    localStorage.setItem('matchme_onboarding_completed', 'true');

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
        const res = await fetch('/api/onboarding/step', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'lang': 'uz'
            },
            body: JSON.stringify({
                user_id: state.userId,
                step: step,
                ...payload
            })
        });
        const data = await res.json();
        if (!res.ok || !data.status) {
            if (window.showToast) {
                window.showToast(data.message || `Xatolik yuz berdi (${step}-qadam)`);
            }
            return false;
        }
        return true;
    } catch (e) {
        console.log(`Step ${step} error:`, e);
        if (window.showToast) window.showToast('Server bilan bog\'lanishda xatolik!');
        return false;
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

// Discovery Dynamic Candidates
let discoveryCandidates = [];
let currentCandidateIndex = 0;
let currentCandidatePhotoIndex = 0;

async function fetchDiscoveryCandidates() {
    const userId = state.userId || localStorage.getItem('matchme_user_id');
    if (!userId) return;

    try {
        const res = await fetch(`/api/discovery/candidates?user_id=${userId}`, {
            headers: {
                'Accept': 'application/json',
                'lang': 'uz'
            }
        });
        const data = await res.json();
        if (data.status && Array.isArray(data.data)) {
            discoveryCandidates = data.data;
            currentCandidateIndex = 0;
            currentCandidatePhotoIndex = 0;
            renderCurrentCandidate();
        }
    } catch (e) {
        console.error('Failed to fetch candidates:', e);
    }
}

function renderCurrentCandidate() {
    const card = document.getElementById('profile-card');
    const emptyState = document.getElementById('discovery-empty-state');
    const dockActions = document.querySelector('.discovery-dock-actions');

    if (!discoveryCandidates || discoveryCandidates.length === 0 || currentCandidateIndex >= discoveryCandidates.length) {
        if (card) card.style.display = 'none';
        if (dockActions) dockActions.style.display = 'none';
        if (emptyState) emptyState.style.display = 'flex';
        return;
    }

    if (card) card.style.display = 'flex';
    if (dockActions) dockActions.style.display = 'flex';
    if (emptyState) emptyState.style.display = 'none';

    const candidate = discoveryCandidates[currentCandidateIndex];
    if (!candidate) return;

    // Photos
    const photos = candidate.photos && candidate.photos.length > 0 ? candidate.photos : ['/assets/images/no-avatar.png'];
    const photoUrl = photos[currentCandidatePhotoIndex] || photos[0];
    const imgEl = document.getElementById('current-profile-img');
    if (imgEl) imgEl.src = photoUrl;

    // Dots indicator
    const dotsContainer = document.getElementById('photo-dots');
    if (dotsContainer) {
        if (photos.length > 1) {
            dotsContainer.innerHTML = photos.map((_, i) => 
                `<div class="photo-dot ${i === currentCandidatePhotoIndex ? 'active' : ''}"></div>`
            ).join('');
            dotsContainer.style.display = 'flex';
        } else {
            dotsContainer.style.display = 'none';
        }
    }

    // Name & Age
    const nameStr = `${candidate.name || 'Foydalanuvchi'}${candidate.age ? ', ' + candidate.age : ''}`;
    const nameEl = document.getElementById('card-user-name');
    if (nameEl) nameEl.innerText = nameStr;
    const sheetNameEl = document.getElementById('sheet-user-name');
    if (sheetNameEl) sheetNameEl.innerText = nameStr;

    // VIP Tag
    document.querySelectorAll('.vip-tag').forEach(tag => {
        tag.style.display = candidate.is_vip ? 'inline-block' : 'none';
    });

    // City & Location
    const cityText = candidate.city_label || candidate.city || 'Toshkent';
    const cityEl = document.getElementById('card-user-city');
    if (cityEl) cityEl.innerText = cityText;
    const sheetCityEl = document.getElementById('sheet-user-city');
    if (sheetCityEl) sheetCityEl.innerText = cityText;

    // Bio
    const bioText = candidate.bio || 'O\'zi haqida ma\'lumot kiritilmagan';
    const bioEl = document.getElementById('card-user-bio');
    if (bioEl) bioEl.innerText = bioText;
    const sheetBioEl = document.getElementById('sheet-user-bio');
    if (sheetBioEl) sheetBioEl.innerText = bioText;

    // Details Sheet specific items
    const heightEl = document.getElementById('sheet-user-height');
    if (heightEl) heightEl.innerText = candidate.height ? `${candidate.height} sm` : 'Ko\'rsatilmagan';
    const weightEl = document.getElementById('sheet-user-weight');
    if (weightEl) weightEl.innerText = candidate.weight ? `${candidate.weight} kg` : 'Ko\'rsatilmagan';
    const occEl = document.getElementById('sheet-user-occ');
    if (occEl) occEl.innerText = candidate.occupation || 'Ko\'rsatilmagan';
}

function changePhoto(dir) {
    haptic();
    const candidate = discoveryCandidates[currentCandidateIndex];
    if (!candidate || !candidate.photos || candidate.photos.length <= 1) return;

    currentCandidatePhotoIndex += dir;
    if (currentCandidatePhotoIndex < 0) {
        currentCandidatePhotoIndex = candidate.photos.length - 1;
    } else if (currentCandidatePhotoIndex >= candidate.photos.length) {
        currentCandidatePhotoIndex = 0;
    }

    const imgEl = document.getElementById('current-profile-img');
    if (imgEl) imgEl.src = candidate.photos[currentCandidatePhotoIndex];

    const dots = document.querySelectorAll('.photo-dot');
    dots.forEach((d, i) => d.classList.toggle('active', i === currentCandidatePhotoIndex));
}

function handleCardAction(action) {
    haptic();
    const currentCandidate = discoveryCandidates[currentCandidateIndex];
    if (!currentCandidate) return;

    const card = document.getElementById('profile-card');

    if (action === 'like') {
        if (card) {
            card.style.transform = 'translateX(80px) rotate(8deg)';
            card.style.opacity = '0.3';
        }
        if (typeof confetti === 'function') confetti({ particleCount: 60, spread: 70, origin: { y: 0.7 } });
        
        // Send like to backend
        const userId = state.userId || localStorage.getItem('matchme_user_id');
        if (userId) {
            fetch('/api/likes/like', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    user_id: userId,
                    target_user_id: currentCandidate.id
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.is_match) {
                    if (typeof confetti === 'function') confetti({ particleCount: 100, spread: 80, origin: { y: 0.6 } });
                    if (window.showToast) window.showToast('🎉 Tabriklaymiz! Sizda yangi moslik (Match) bor!');
                }
            })
            .catch(e => console.log('Like err:', e));
        }
    } else if (action === 'dislike') {
        if (card) {
            card.style.transform = 'translateX(-80px) rotate(-8deg)';
            card.style.opacity = '0.3';
        }
    } else if (action === 'gift') {
        openVipModal();
        return;
    }

    setTimeout(() => {
        currentCandidateIndex++;
        currentCandidatePhotoIndex = 0;
        if (card) {
            card.style.transition = 'none';
            card.style.transform = 'none';
            card.style.opacity = '1';
        }
        renderCurrentCandidate();
        setTimeout(() => {
            if (card) card.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
        }, 50);
    }, 250);
}

function openReportModal() {
    haptic();
}

function toggleProfileDetails() {
    haptic();
    const sheet = document.getElementById('profile-sheet');
    if (sheet) sheet.classList.toggle('active');
}

function openBonusModal() {
    haptic();
}

// ==================== BOOST LOGIC ====================
let selectedBoostPlanId = 2;
let selectedBoostPrice = 20000;

function openBoostModal() {
    haptic();
    const modal = document.getElementById('boost-modal-overlay');
    if (!modal) return;
    modal.classList.add('active');
}

function closeBoostModal(e) {
    if (e) e.stopPropagation();
    haptic();
    const modal = document.getElementById('boost-modal-overlay');
    if (modal) modal.classList.remove('active');
}

function selectBoostPlan(planId, price, title = '', formattedPrice = '', subtitle = '', icon = '⚡') {
    haptic();
    selectedBoostPlanId = parseInt(planId, 10);
    selectedBoostPrice = price;

    window.SELECTED_BOOST_PLAN = {
        id: selectedBoostPlanId,
        title: title || `${planId} soatlik Boost`,
        price: price,
        formattedPrice: formattedPrice || `${price.toLocaleString()} UZS`,
        subtitle: subtitle || 'Profilni 1-o\'ringa ko\'tarish',
        icon: icon || '⚡'
    };

    document.querySelectorAll('.boost-plan-card').forEach(card => card.classList.remove('active'));
    const activeCard = document.getElementById(`boost-plan-${planId}`);
    if (activeCard) activeCard.classList.add('active');

    const labelEl = document.getElementById('btn-boost-label');
    if (labelEl) {
        labelEl.innerText = `To'lov qilish va Faollashtirish (${window.SELECTED_BOOST_PLAN.formattedPrice})`;
    }
}

function proceedToBoostPayment() {
    haptic();
    const plan = window.SELECTED_BOOST_PLAN || {
        id: selectedBoostPlanId || 1,
        title: '3 soatlik Boost',
        price: selectedBoostPrice || 20000,
        formattedPrice: '20 000 UZS',
        subtitle: 'Profilni 1-o\'ringa ko\'tarish',
        icon: '🚀'
    };

    closeBoostModal();
    openUniversalPaymentReceipt('boost', plan);
}

// Universal Payment & Receipt Management
let activeReceiptFile = null;
window.ACTIVE_PAYMENT_TARGET = { type: 'boost', id: 1, title: 'Boost', price: 20000 };

window.openUniversalPaymentReceipt = function(type, item) {
    haptic();
    window.ACTIVE_PAYMENT_TARGET = {
        type: type,
        id: item.id,
        title: item.title || item.name || (type === 'boost' ? 'MatchMe Boost' : 'MatchMe Premium'),
        price: item.price,
        formattedPrice: item.formattedPrice || `${item.price.toLocaleString()} UZS`,
        subtitle: item.subtitle || (type === 'boost' ? 'Profilni 1-o\'ringa ko\'tarish' : 'Cheklovlarsiz Premium imkoniyatlar'),
        icon: item.icon || (type === 'boost' ? '⚡' : '👑')
    };

    const modal = document.getElementById('modal-payment-receipt');
    if (!modal) return;

    // Update Modal UI
    const iconEl = document.getElementById('receipt-summary-icon');
    if (iconEl) iconEl.innerText = window.ACTIVE_PAYMENT_TARGET.icon;

    const titleEl = document.getElementById('receipt-summary-plan-title');
    if (titleEl) titleEl.innerText = window.ACTIVE_PAYMENT_TARGET.title;

    const subEl = document.getElementById('receipt-summary-plan-sub');
    if (subEl) subEl.innerText = window.ACTIVE_PAYMENT_TARGET.subtitle;

    const priceEl = document.getElementById('receipt-summary-plan-price');
    if (priceEl) priceEl.innerText = window.ACTIVE_PAYMENT_TARGET.formattedPrice;

    const exactAmountEl = document.getElementById('payment-exact-amount-text');
    if (exactAmountEl) exactAmountEl.innerText = window.ACTIVE_PAYMENT_TARGET.formattedPrice;

    modal.style.display = 'flex';
};

window.closePaymentReceiptModal = function() {
    haptic();
    const modal = document.getElementById('modal-payment-receipt');
    if (modal) modal.style.display = 'none';
};

window.handleReceiptFileSelect = function(input) {
    if (!input.files || !input.files[0]) return;
    activeReceiptFile = input.files[0];

    const reader = new FileReader();
    reader.onload = function(e) {
        const previewImg = document.getElementById('receipt-preview-img');
        if (previewImg) previewImg.src = e.target.result;
        const emptyView = document.getElementById('receipt-empty-view');
        if (emptyView) emptyView.style.display = 'none';
        const previewView = document.getElementById('receipt-preview-view');
        if (previewView) previewView.style.display = 'block';
    };
    reader.readAsDataURL(activeReceiptFile);
};

window.removeReceiptPreview = function() {
    activeReceiptFile = null;
    const fileInput = document.getElementById('receipt-file-input');
    if (fileInput) fileInput.value = '';
    const previewImg = document.getElementById('receipt-preview-img');
    if (previewImg) previewImg.src = '';
    const emptyView = document.getElementById('receipt-empty-view');
    if (emptyView) emptyView.style.display = 'block';
    const previewView = document.getElementById('receipt-preview-view');
    if (previewView) previewView.style.display = 'none';
};

window.copyCardNumber = function() {
    haptic();
    const cardText = document.getElementById('payment-card-number-text')?.innerText || '9860030145287890';
    const cleanDigits = cardText.replace(/\s+/g, '');
    navigator.clipboard.writeText(cleanDigits).then(() => {
        const copyBtnText = document.getElementById('copy-btn-text');
        if (copyBtnText) copyBtnText.innerText = 'Nusxalandi!';
        if (window.showToast) window.showToast('Karta raqami nusxalandi: ' + cleanDigits);
        setTimeout(() => {
            if (copyBtnText) copyBtnText.innerText = 'Nusxalash';
        }, 2000);
    });
};

window.submitUniversalPaymentReceipt = async function() {
    haptic();
    if (!activeReceiptFile) {
        if (window.showToast) window.showToast('Iltimos, to\'lov cheki skrinshotini yuklang! 📸');
        return;
    }

    const btn = document.getElementById('btn-submit-receipt');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = 'Chek yuborilmoqda... ⏳';
    }

    const target = window.ACTIVE_PAYMENT_TARGET || { type: 'boost', id: 1 };
    const notes = document.getElementById('receipt-input-notes')?.value || '';
    const userId = state.userId || localStorage.getItem('matchme_user_id') || window.APP_DEFAULT_USER_ID || 1;

    const formData = new FormData();
    if (target.type === 'boost') {
        formData.append('boost_id', target.id);
    } else {
        formData.append('plan_id', target.id);
    }
    formData.append('receipt', activeReceiptFile);
    if (notes) formData.append('notes', notes);
    if (userId) formData.append('user_id', userId);

    try {
        const res = await fetch('/api/profile/submit-receipt', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await res.json();
        if (res.ok && data.status) {
            if (window.showToast) window.showToast('To\'lov chekingiz qabul qilindi! Admin tasdiqlagach faollashadi ✨');
            removeReceiptPreview();
            const notesInput = document.getElementById('receipt-input-notes');
            if (notesInput) notesInput.value = '';
            closePaymentReceiptModal();
            if (typeof confetti === 'function') {
                confetti({ particleCount: 90, spread: 70, origin: { y: 0.6 } });
            }
        } else {
            if (window.showToast) window.showToast(data.message || 'Chekni yuborishda xatolik yuz berdi');
        }
    } catch (e) {
        console.error('Receipt submission error:', e);
        if (window.showToast) window.showToast('Server bilan bog\'lanishda xatolik');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '📤 To\'lov Chekini Yuborish ➔';
        }
    }
};

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

// ==================== SPEED ROULETTE LOGIC ====================
let selectedRouletteGender = 'all';
let isRouletteSearching = false;
let rouletteSearchTimer = null;

function selectRouletteGender(gender) {
    haptic();
    selectedRouletteGender = gender;

    // Update active card border in UI
    document.querySelectorAll('.roulette-gender-card').forEach(card => card.classList.remove('active'));
    
    if (gender === 'female') {
        document.getElementById('r-gender-female')?.classList.add('active');
        openVipModal();
    } else if (gender === 'male') {
        document.getElementById('r-gender-male')?.classList.add('active');
        openVipModal();
    } else {
        document.getElementById('r-gender-all')?.classList.add('active');
    }
}

function openVipModal() {
    haptic();
    document.getElementById('vip-modal-overlay')?.classList.add('active');
}

function closeVipModal(event) {
    if (event) event.stopPropagation();
    haptic();
    document.getElementById('vip-modal-overlay')?.classList.remove('active');
}

let rouletteNodeTimers = [];

function toggleRouletteSearch() {
    haptic();

    // If VIP filter selected, require VIP subscription
    if (selectedRouletteGender !== 'all') {
        openVipModal();
        return;
    }

    const radarContainer = document.querySelector('.roulette-radar-container');
    const nodesLayer = document.getElementById('radar-nodes-layer');
    const mainBtn = document.getElementById('btn-roulette-main');
    const iconSearch = document.getElementById('btn-roulette-icon-search');
    const iconStop = document.getElementById('btn-roulette-icon-stop');
    const btnText = document.getElementById('btn-roulette-text');
    const statusTitle = document.getElementById('roulette-status-title');
    const statusSub = document.getElementById('roulette-status-sub');

    if (isRouletteSearching) {
        // Stop searching
        isRouletteSearching = false;
        if (rouletteSearchTimer) clearTimeout(rouletteSearchTimer);
        rouletteNodeTimers.forEach(t => clearTimeout(t));
        rouletteNodeTimers = [];

        radarContainer?.classList.remove('searching');
        mainBtn?.classList.remove('searching');
        if (nodesLayer) nodesLayer.innerHTML = '';
        if (iconSearch) iconSearch.style.display = 'block';
        if (iconStop) iconStop.style.display = 'none';
        if (btnText) btnText.innerText = 'Qidiruvni Boshlash';
        if (statusTitle) statusTitle.innerText = 'Tasodifiy suhbatga tayyormisiz?';
        if (statusSub) statusSub.innerText = 'Qidiruv tugmasini bosing va darhol yangi suhbatdosh bilan tanishing';
    } else {
        // Start searching with radar animation
        isRouletteSearching = true;
        radarContainer?.classList.add('searching');
        mainBtn?.classList.add('searching');
        if (nodesLayer) nodesLayer.innerHTML = '';
        if (iconSearch) iconSearch.style.display = 'none';
        if (iconStop) iconStop.style.display = 'block';
        if (btnText) btnText.innerText = 'Qidiruvni To\'xtatish';
        if (statusTitle) statusTitle.innerText = 'Sizga mos suhbatdosh qidirilmoqda...';
        if (statusSub) statusSub.innerText = 'O\'rtacha kutish vaqti: 3–5 soniya';

        // Spawn ShareIt-style discovered user nodes
        const node1Timer = setTimeout(() => {
            if (isRouletteSearching) spawnRadarNode('Anonim #482', 'node-pos-1');
        }, 1200);

        const node2Timer = setTimeout(() => {
            if (isRouletteSearching) spawnRadarNode('Anonim #109', 'node-pos-2');
        }, 2200);

        const node3Timer = setTimeout(() => {
            if (isRouletteSearching) {
                spawnRadarNode('Anonim #731', 'node-pos-3');
                if (statusTitle) statusTitle.innerText = '🎯 3 ta faol suhbatdosh topildi!';
                if (statusSub) statusSub.innerText = 'Suhbatlashish uchun istalgan birining ustiga bosing';
            }
        }, 3200);

        rouletteNodeTimers.push(node1Timer, node2Timer, node3Timer);
    }
}

function spawnRadarNode(name, posClass) {
    const layer = document.getElementById('radar-nodes-layer');
    if (!layer) return;

    haptic();
    const node = document.createElement('div');
    node.className = `radar-discovered-node ${posClass}`;
    node.onclick = () => openAnonymousChat(name);
    node.innerHTML = `
        <div class="node-avatar-circle">
            <svg viewBox="0 0 24 24" class="default-avatar-svg">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
            <div class="node-pulse-ring"></div>
        </div>
        <div class="node-name-pill">${name}</div>
    `;
    layer.appendChild(node);
}

// ==================== TEMPORARY ANONYMOUS CHAT HANDLERS ====================
let currentAnonPartner = 'Anonim Suhbatdosh';

function openAnonymousChat(partnerName) {
    haptic();
    currentAnonPartner = partnerName || 'Anonim Suhbatdosh';
    const titleEl = document.getElementById('anon-chat-username');
    if (titleEl) titleEl.innerText = currentAnonPartner;

    const overlay = document.getElementById('anonymous-chat-overlay');
    overlay?.classList.add('active');

    // Scroll to bottom
    const msgBox = document.getElementById('anon-chat-messages');
    if (msgBox) msgBox.scrollTop = msgBox.scrollHeight;
}

function closeAnonymousChat() {
    haptic();
    const overlay = document.getElementById('anonymous-chat-overlay');
    overlay?.classList.remove('active');
}

function openChatActionsSheet() {
    haptic();
    document.getElementById('chat-actions-overlay')?.classList.add('active');
}

function closeChatActionsSheet() {
    haptic();
    document.getElementById('chat-actions-overlay')?.classList.remove('active');
}

function nextAnonPartner() {
    closeChatActionsSheet();
    closeAnonymousChat();
    haptic();
    toggleRouletteSearch(); // Re-trigger searching
}

function reportAnonPartner() {
    closeChatActionsSheet();
    haptic();
    alert('Shikoyat qabul qilindi. Ushbu foydalanuvchi tekshiruvga yuborildi.');
    closeAnonymousChat();
}

function skipOrEndChat() {
    haptic();
    openChatActionsSheet();
}

function sendAnonChatMessage() {
    const input = document.getElementById('anon-chat-input');
    if (!input || !input.value.trim()) return;

    const text = input.value.trim();
    input.value = '';
    haptic();

    const msgContainer = document.getElementById('anon-chat-messages');
    if (!msgContainer) return;

    const now = new Date();
    const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    // Append outgoing message bubble
    const outBubble = document.createElement('div');
    outBubble.className = 'chat-bubble outgoing';
    outBubble.innerHTML = `
        <div class="bubble-content">
            <p>${escapeHtml(text)}</p>
            <span class="bubble-time">${timeStr} ✓✓</span>
        </div>
    `;
    msgContainer.appendChild(outBubble);
    msgContainer.scrollTop = msgContainer.scrollHeight;

    // Simulate partner response
    setTimeout(() => {
        const inBubble = document.createElement('div');
        inBubble.className = 'chat-bubble incoming';
        const partnerReplies = [
            "Ajoyib! Qaysi sohada o'qiysiz yoki ishlaysiz? 😊",
            "Tushundim, bo'sh vaqtingizda nimalar bilan shug'ullanasiz? ✨",
            "Juda qiziq suhbat bo'lyapti! Qaysi shahardansiz? 🌆",
            "Kofe ichishga vaqtingiz bo'ladimi bu hafta oxirida? ☕"
        ];
        const randomReply = partnerReplies[Math.floor(Math.random() * partnerReplies.length)];

        inBubble.innerHTML = `
            <div class="bubble-content">
                <p>${randomReply}</p>
                <span class="bubble-time">${timeStr}</span>
            </div>
        `;
        msgContainer.appendChild(inBubble);
        msgContainer.scrollTop = msgContainer.scrollHeight;
        haptic();
    }, 1200);
}

function handleAnonChatKeyPress(event) {
    if (event.key === 'Enter') {
        sendAnonChatMessage();
    }
}

function simulateMediaAttach() {
    haptic();
    alert('Anonim chat xavfsizligi tufayli rasm yuborish vaqtincha cheklangan.');
}

function escapeHtml(string) {
    const div = document.createElement('div');
    div.innerText = string;
    return div.innerHTML;
}

