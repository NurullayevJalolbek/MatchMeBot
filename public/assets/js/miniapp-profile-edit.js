/* ==========================================================================
   MINI-APP PROFILE EDIT SCRIPTS
   MatchMe Telegram Mini-App
   ========================================================================== */

// 1. Toast Notification Handler
    window.showToast = function (msg) {
        let toast = document.getElementById('edit-page-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'edit-page-toast';
            toast.className = 'mini-app-toast';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.classList.add('show');
        if (window._toastTimeout) clearTimeout(window._toastTimeout);
        window._toastTimeout = setTimeout(() => {
            toast.classList.remove('show');
        }, 2500);
    };

    // 2. State Store
    let editState = window.INITIAL_EDIT_STATE || {
        name: "",
        gender: "male",
        birthDate: "",
        height: 178,
        weight: 72,
        bio: "",
        livingRegionId: "",
        livingDistrictId: "",
        birthRegionId: "",
        birthDistrictId: "",
        photos: [],
        selectedOptionIds: [],
        districts: []
    };

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

    // 3. Initial Load & Render
    document.addEventListener('DOMContentLoaded', async function () {
        // Ensure interests modal is firmly hidden on startup
        const modal = document.getElementById('modal-interests-picker');
        if (modal) {
            modal.classList.remove('is-active');
            modal.style.display = 'none';
        }

        await loadFullEditData();
        renderPhotos();
        renderSelectedInterestsPreview();
        populateDistricts('living', editState.livingRegionId, editState.livingDistrictId);
        populateDistricts('birth', editState.birthRegionId, editState.birthDistrictId);
        updateLocationPreview();
    });

    async function loadFullEditData() {
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

            const res = await fetch('/api/profile/edit-data?' + params.toString(), { headers });
            const data = await res.json();

            if (data.status && data.data) {
                const d = data.data;
                if (d.user) {
                    editState.name = d.user.name || editState.name;
                    editState.gender = d.user.gender || editState.gender;
                    editState.birthDate = d.user.birth_date || editState.birthDate;
                    editState.height = d.user.height || editState.height;
                    editState.weight = d.user.weight || editState.weight;
                    editState.bio = d.user.bio || editState.bio;
                    editState.livingRegionId = d.user.living_region_id || editState.livingRegionId;
                    editState.livingDistrictId = d.user.living_district_id || editState.livingDistrictId;
                    editState.birthRegionId = d.user.birth_region_id || editState.birthRegionId;
                    editState.birthDistrictId = d.user.birth_district_id || editState.birthDistrictId;
                    editState.photos = d.user.photos || editState.photos;
                    editState.selectedOptionIds = d.user.selected_option_ids || editState.selectedOptionIds;

                    // Sync DOM inputs
                    document.getElementById('edit-name').value = editState.name;
                    if (editState.birthDate) document.getElementById('edit-birth-date').value = editState.birthDate;
                    document.getElementById('edit-bio').value = editState.bio;
                    document.getElementById('bio-char-counter').textContent = `${editState.bio.length}/250`;
                    document.getElementById('slider-height').value = editState.height;
                    document.getElementById('height-val-display').textContent = editState.height + ' sm';
                    document.getElementById('slider-weight').value = editState.weight;
                    document.getElementById('weight-val-display').textContent = editState.weight + ' kg';
                    
                    if (editState.livingRegionId) document.getElementById('edit-living-region').value = editState.livingRegionId;
                    if (editState.birthRegionId) document.getElementById('edit-birth-region').value = editState.birthRegionId;

                    selectGender(editState.gender);
                }

                if (d.districts) editState.districts = d.districts;
                if (d.completion) updateCompletionUI(d.completion);

                renderPhotos();
                renderSelectedInterestsPreview();
                populateDistricts('living', editState.livingRegionId, editState.livingDistrictId);
                populateDistricts('birth', editState.birthRegionId, editState.birthDistrictId);
                updateLocationPreview();
            }
        } catch (e) {
            console.error("Edit data sync error:", e);
        }
    }

    // 4. Photos Management (Max 3) with Instant Preview & Slot Updating
    function renderPhotos() {
        const grid = document.getElementById('photos-grid-container');
        const counter = document.getElementById('photos-counter-pill');
        const photos = editState.photos || [];

        counter.textContent = `${photos.length} / 3 rasm`;

        let html = '';

        // Main photo slot (Slot 1)
        if (photos.length > 0) {
            const p1 = photos[0];
            html += `
                <div class="photo-slot-main" onclick="triggerPhotoUpload(0)" style="cursor: pointer;">
                    <img src="${p1.url}" class="slot-img" alt="Main Photo">
                    <span class="badge-main-photo">Asosiy</span>
                </div>
            `;
        } else {
            html += `
                <div class="photo-slot-main photo-slot-empty" onclick="triggerPhotoUpload(null)">
                    <span class="empty-plus-icon">+</span>
                    <span class="empty-slot-text">Asosiy rasm yuklash</span>
                </div>
            `;
        }

        // Secondary photo slot (Slot 2)
        if (photos.length > 1) {
            const p2 = photos[1];
            html += `
                <div class="photo-slot-secondary" onclick="triggerPhotoUpload(1)" style="cursor: pointer;">
                    <img src="${p2.url}" class="slot-img" alt="Photo 2">
                </div>
            `;
        } else {
            html += `
                <div class="photo-slot-secondary photo-slot-empty" onclick="triggerPhotoUpload(null)">
                    <span class="empty-plus-icon">+</span>
                    <span class="empty-slot-text">Rasm qo'shish</span>
                </div>
            `;
        }

        // Third photo slot (Slot 3)
        if (photos.length > 2) {
            const p3 = photos[2];
            html += `
                <div class="photo-slot-secondary" onclick="triggerPhotoUpload(2)" style="cursor: pointer;">
                    <img src="${p3.url}" class="slot-img" alt="Photo 3">
                </div>
            `;
        } else {
            html += `
                <div class="photo-slot-secondary photo-slot-empty" onclick="triggerPhotoUpload(null)">
                    <span class="empty-plus-icon">+</span>
                    <span class="empty-slot-text">Rasm qo'shish</span>
                </div>
            `;
        }

        grid.innerHTML = html;
    }

    let targetUploadIndex = null;
    function triggerPhotoUpload(index = null) {
        if (editState.photos.length >= 3 && index === null) {
            showToast("Maksimal 3 ta rasm yuklash mumkin.");
            return;
        }
        targetUploadIndex = index;
        document.getElementById('photo-upload-input').click();
    }

    async function handlePhotoUpload(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];

        // 1. Instant local preview on the UI right there in the slot!
        const localBlobUrl = URL.createObjectURL(file);
        const replaceIndex = targetUploadIndex;
        let replacePhotoId = null;

        if (replaceIndex !== null && editState.photos[replaceIndex]) {
            replacePhotoId = editState.photos[replaceIndex].id;
            editState.photos[replaceIndex].url = localBlobUrl;
        } else {
            editState.photos.push({
                id: 'temp_' + Date.now(),
                url: localBlobUrl,
                is_main: editState.photos.length === 0,
                order: editState.photos.length + 1
            });
        }
        renderPhotos(); // INSTANT VISUAL UPDATE!

        const { tgUser, userId } = getClientAuth();
        const formData = new FormData();
        formData.append('photo', file);
        if (replaceIndex === 0 || (replaceIndex === null && editState.photos.length === 1)) {
            formData.append('is_main', '1');
        }
        if (replacePhotoId && !String(replacePhotoId).startsWith('temp_')) {
            formData.append('replace_photo_id', replacePhotoId);
        }
        if (userId) formData.append('user_id', userId);
        if (tgUser && tgUser.id) formData.append('telegram_id', tgUser.id);

        showToast("Rasm saqlanmoqda... 📸");

        try {
            const headers = {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            };
            if (tgUser && tgUser.id) headers['X-Telegram-Id'] = tgUser.id;
            if (userId) headers['X-User-Id'] = userId;

            const res = await fetch('/api/profile/upload-photo', {
                method: 'POST',
                headers: headers,
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.status && data.data?.photo) {
                showToast("Rasm muvaffaqiyatli yuklandi! ✨");
                // Replace temp photo with real persisted photo from server
                if (replaceIndex !== null && editState.photos[replaceIndex]) {
                    editState.photos[replaceIndex] = data.data.photo;
                } else {
                    const lastIdx = editState.photos.length - 1;
                    if (lastIdx >= 0) editState.photos[lastIdx] = data.data.photo;
                }
                if (data.data.completion) updateCompletionUI(data.data.completion);
                renderPhotos();
            } else {
                showToast(data.message || "Yuklashda xatolik yuz berdi");
                // Re-sync on failure
                await loadFullEditData();
            }
        } catch (e) {
            console.error("Photo upload error:", e);
            showToast("Server bilan bog'lanishda xatolik");
        } finally {
            input.value = '';
        }
    }

    async function deletePhoto(photoId) {
        if (!confirm("Haqiqatan ham ushbu rasmni o'chirmoqchimisiz?")) return;

        const { tgUser, userId } = getClientAuth();
        try {
            const headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            };
            if (tgUser && tgUser.id) headers['X-Telegram-Id'] = tgUser.id;
            if (userId) headers['X-User-Id'] = userId;

            const res = await fetch(`/api/profile/delete-photo/${photoId}`, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify({ user_id: userId, telegram_id: tgUser?.id })
            });

            const data = await res.json();
            if (data.status) {
                showToast("Rasm o'chirildi.");
                editState.photos = editState.photos.filter(p => p.id !== photoId);
                if (data.data?.completion) updateCompletionUI(data.data.completion);
                renderPhotos();
            } else {
                showToast(data.message || "Xatolik yuz berdi");
            }
        } catch (e) {
            showToast("Server bilan bog'lanishda xatolik");
        }
    }

    // 5. Bio Counter
    function updateBioCounter(textarea) {
        editState.bio = textarea.value;
        document.getElementById('bio-char-counter').textContent = `${textarea.value.length}/250`;
    }

    // 6. Sliders
    function onHeightChange(val) {
        editState.height = parseInt(val);
        document.getElementById('height-val-display').textContent = val + ' sm';
    }

    function onWeightChange(val) {
        editState.weight = parseInt(val);
        document.getElementById('weight-val-display').textContent = val + ' kg';
    }

    // 7. Gender Selection
    function selectGender(gender) {
        editState.gender = gender;
        document.getElementById('gender-btn-male').classList.toggle('active', gender === 'male');
        document.getElementById('gender-btn-female').classList.toggle('active', gender === 'female');
    }

    // 8. Region & District Cascading Dropdowns
    function populateDistricts(type, regionId, selectedDistrictId = '') {
        const districtSelect = document.getElementById(`edit-${type}-district`);
        if (!regionId) {
            districtSelect.innerHTML = '<option value="">Avval viloyatni tanlang...</option>';
            return;
        }

        const filtered = editState.districts.filter(d => d.region_id == regionId);
        let optionsHtml = '<option value="">Tuman / Shaharni tanlang...</option>';

        filtered.forEach(d => {
            const isSel = String(d.id) === String(selectedDistrictId) ? 'selected' : '';
            optionsHtml += `<option value="${d.id}" ${isSel}>${d.name_uz}</option>`;
        });

        districtSelect.innerHTML = optionsHtml;
    }

    function onLivingRegionChange(regionId) {
        editState.livingRegionId = regionId;
        populateDistricts('living', regionId);
        updateLocationPreview();
    }

    function onBirthRegionChange(regionId) {
        editState.birthRegionId = regionId;
        populateDistricts('birth', regionId);
    }

    function updateLocationPreview() {
        const regionSelect = document.getElementById('edit-living-region');
        const districtSelect = document.getElementById('edit-living-district');
        const previewEl = document.getElementById('location-preview-text');

        const regionText = regionSelect.options[regionSelect.selectedIndex]?.text || '';
        const districtText = districtSelect.options[districtSelect.selectedIndex]?.text || '';

        if (regionText && regionText !== 'Tanlang...') {
            if (districtText && districtText !== 'Tuman / Shaharni tanlang...' && districtText !== 'Avval viloyatni tanlang...') {
                previewEl.textContent = `${regionText}, ${districtText} (Aniq lokatsiya)`;
            } else {
                previewEl.textContent = `${regionText} (Aniq lokatsiya)`;
            }
        } else {
            previewEl.textContent = "Toshkent, Yunusobod (Aniq lokatsiya)";
        }
    }

    document.getElementById('edit-living-district').addEventListener('change', function () {
        editState.livingDistrictId = this.value;
        updateLocationPreview();
    });

    document.getElementById('edit-birth-district').addEventListener('change', function () {
        editState.birthDistrictId = this.value;
    });

    // 9. Profile Options (Pills) Single & Category Selection
    function toggleSingleOption(btn, type) {
        const optionId = parseInt(btn.dataset.optionId);
        const container = btn.closest('.pill-options-grid');

        // Deactivate siblings
        container.querySelectorAll('.btn-option-pill').forEach(b => {
            b.classList.remove('active');
            const bId = parseInt(b.dataset.optionId);
            editState.selectedOptionIds = editState.selectedOptionIds.filter(id => id !== bId);
        });

        btn.classList.add('active');
        if (!editState.selectedOptionIds.includes(optionId)) {
            editState.selectedOptionIds.push(optionId);
        }
    }

    function toggleCategoryOption(btn, category) {
        const optionId = parseInt(btn.dataset.optionId);
        const container = btn.closest('.pill-options-grid');

        container.querySelectorAll('.btn-option-pill').forEach(b => {
            b.classList.remove('active');
            const bId = parseInt(b.dataset.optionId);
            editState.selectedOptionIds = editState.selectedOptionIds.filter(id => id !== bId);
        });

        btn.classList.add('active');
        if (!editState.selectedOptionIds.includes(optionId)) {
            editState.selectedOptionIds.push(optionId);
        }
    }

    // 10. Interests Multi-Selection (Max 10)
    window.openInterestsModal = function () {
        const modal = document.getElementById('modal-interests-picker');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('is-active');
        }, 10);
        updateModalInterestsCount();
    };

    window.closeInterestsModal = function () {
        const modal = document.getElementById('modal-interests-picker');
        modal.classList.remove('is-active');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 250);
        renderSelectedInterestsPreview();
    };

    function updateModalInterestsCount() {
        const activeCount = document.querySelectorAll('.interest-pill-item.active').length;
        const counterEl = document.getElementById('modal-interests-counter');
        if (counterEl) counterEl.textContent = `${activeCount} / 10 ta`;
    }

    function toggleInterestItem(btn, optionId) {
        optionId = parseInt(optionId);
        const activeButtons = document.querySelectorAll('.interest-pill-item.active');

        if (btn.classList.contains('active')) {
            btn.classList.remove('active');
            editState.selectedOptionIds = editState.selectedOptionIds.filter(id => id !== optionId);
        } else {
            if (activeButtons.length >= 10) {
                showToast("Maksimal 10 ta qiziqish tanlash mumkin!");
                return;
            }
            btn.classList.add('active');
            if (!editState.selectedOptionIds.includes(optionId)) {
                editState.selectedOptionIds.push(optionId);
            }
        }
        updateModalInterestsCount();
    }

    function renderSelectedInterestsPreview() {
        const previewContainer = document.getElementById('selected-interests-preview');
        const countPill = document.getElementById('interests-pill-count');
        const activeButtons = document.querySelectorAll('.interest-pill-item.active');

        countPill.textContent = `${activeButtons.length} / 10 ta ➔`;

        let html = '';
        activeButtons.forEach(btn => {
            const name = btn.dataset.name;
            const icon = btn.dataset.icon;
            html += `
                <div class="btn-option-pill active" style="padding: 6px 12px; font-size: 0.76rem;">
                    <span>${icon}</span>
                    <span>${name}</span>
                </div>
            `;
        });

        previewContainer.innerHTML = html || '<span style="font-size: 0.76rem; color: #64748b;">Hali qiziqishlar belgilanmagan.</span>';
    }

    // 11. Completion UI Updater
    function updateCompletionUI(comp) {
        document.getElementById('comp-percent-text').textContent = comp.percentage + '%';
        document.getElementById('comp-progress-bar').style.width = comp.percentage + '%';

        const remaining = 100 - comp.percentage;
        document.getElementById('comp-remaining-badge').textContent = remaining > 0 ? `+${remaining}% qoldi` : '100% To\'liq';
    }

    // 12. Save All Full Profile
    async function saveFullProfile() {
        const btn = document.getElementById('btn-save-all');
        btn.disabled = true;
        btn.innerHTML = '<span>Saqlanmoqda...</span>';

        const { tgUser, userId } = getClientAuth();

        const livingReg = document.getElementById('edit-living-region').value;
        const livingDist = document.getElementById('edit-living-district').value;
        const birthReg = document.getElementById('edit-birth-region').value;
        const birthDist = document.getElementById('edit-birth-district').value;
        const bDate = document.getElementById('edit-birth-date').value;

        const payload = {
            user_id: userId || null,
            telegram_id: tgUser?.id || null,
            name: document.getElementById('edit-name').value.trim(),
            birth_date: bDate || null,
            gender: editState.gender || 'male',
            height: editState.height ? parseInt(editState.height) : null,
            weight: editState.weight ? parseInt(editState.weight) : null,
            bio: document.getElementById('edit-bio').value.trim(),
            living_region_id: livingReg ? parseInt(livingReg) : null,
            living_district_id: livingDist ? parseInt(livingDist) : null,
            birth_region_id: birthReg ? parseInt(birthReg) : null,
            birth_district_id: birthDist ? parseInt(birthDist) : null,
            option_ids: editState.selectedOptionIds,
        };

        try {
            const headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            };
            if (tgUser && tgUser.id) headers['X-Telegram-Id'] = tgUser.id;
            if (userId) headers['X-User-Id'] = userId;

            const res = await fetch('/api/profile/update', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok && data.status) {
                showToast("Barcha ma'lumotlar saqlandi! ✨");
                if (window.Telegram?.WebApp?.HapticFeedback) {
                    try { window.Telegram.WebApp.HapticFeedback.notificationOccurred('success'); } catch(e){}
                }
                setTimeout(() => {
                    window.location.href = '/profile';
                }, 600);
            } else {
                let errMessage = data.message || "Saqlashda xatolik yuz berdi";
                if (data.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    if (firstKey && data.errors[firstKey][0]) {
                        errMessage = data.errors[firstKey][0];
                    }
                }
                showToast(errMessage);
                btn.disabled = false;
                btn.innerHTML = '<span>Saqlash</span><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
            }
        } catch (e) {
            console.error("Save profile error:", e);
            showToast("Server bilan bog'lanishda xatolik");
            btn.disabled = false;
            btn.innerHTML = '<span>Saqlash</span><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        }
    }
