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
    } catch (e) {
        console.log('Init error:', e);
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

    if (tabName !== 'tanishuv') {
        alert(`📌 "${tabName.toUpperCase()}" bo'limi tez orada ishga tushadi!`);
    }
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
        alert('🎁 TOP-1 Sovg\'angiz Asalga yuborildi!');
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
    alert("⚠️ Ushbu profil ustidan shikoyat qilish yoki xavfsizlikka xabar berish.");
}

function toggleProfileDetails() {
    haptic();
    alert("📄 Foydalanuvchining to'liq anketasi ochilmoqda...");
}

function openBonusModal() {
    haptic();
    alert("🎁 Kunlik bonus: Sizga 1-kunlik bepul Layklar va VIP imkoniyat berildi!");
}

function openBalanceModal() {
    haptic();
    alert("🪙 Balansni to'ldirish (Click / Payme) tez orada ulanadi!");
}

function openBoostModal() {
    haptic();
    alert("⚡ Boost faollashtirildi! Anketangiz 30 daqiqa davomida 1-o'rinda ko'rinadi!");
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
            closeFilterModal();
            // Refresh card stack
            currentProfileIndex = 0;
            loadProfileCard(sampleProfiles[0]);
            alert('✅ Filtrlash sozlamalari muvaffaqiyatli saqlandi!');
        } else {
            alert(data.message || 'Xatolik yuz berdi');
        }
    } catch (e) {
        console.log('Filter save error:', e);
        closeFilterModal();
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
