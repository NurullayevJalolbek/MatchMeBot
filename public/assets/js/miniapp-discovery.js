/* ==========================================================================
   MINI-APP DISCOVERY SCRIPTS
   MatchMe Telegram Mini-App
   ========================================================================== */

// Photo Carousel State
    const userPhotos = [
        'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=1080&auto=format&fit=crop&q=85',
        'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=1080&auto=format&fit=crop&q=85',
        'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=1080&auto=format&fit=crop&q=85'
    ];
    let currentPhotoIndex = 0;

    function renderPhotoDots() {
        const dotsBox = document.getElementById('photo-dots');
        if (!dotsBox) return;
        dotsBox.innerHTML = '';
        userPhotos.forEach((_, idx) => {
            const dot = document.createElement('div');
            dot.className = 'photo-dot ' + (idx === currentPhotoIndex ? 'active' : '');
            dotsBox.appendChild(dot);
        });
    }

    function changePhoto(direction) {
        currentPhotoIndex += direction;
        if (currentPhotoIndex >= userPhotos.length) currentPhotoIndex = 0;
        if (currentPhotoIndex < 0) currentPhotoIndex = userPhotos.length - 1;

        const img = document.getElementById('current-profile-img');
        if (img) {
            img.style.opacity = '0.4';
            setTimeout(() => {
                img.src = userPhotos[currentPhotoIndex];
                img.style.opacity = '1';
            }, 100);
        }
        renderPhotoDots();
    }

    function toggleProfileDetails() {
        const sheet = document.getElementById('profile-sheet');
        if (sheet) {
            sheet.classList.toggle('show');
        }
    }

    function handleCardAction(action) {
        const card = document.getElementById('profile-card');
        if (!card) return;

        if (action === 'like') {
            card.style.transform = 'translateX(100px) rotate(15deg)';
            card.style.opacity = '0';
            if (window.confetti) {
                confetti({ particleCount: 30, spread: 60, origin: { y: 0.7 } });
            }
        } else {
            card.style.transform = 'translateX(-100px) rotate(-15deg)';
            card.style.opacity = '0';
        }

        setTimeout(() => {
            card.style.transition = 'none';
            card.style.transform = 'none';
            card.style.opacity = '1';
            setTimeout(() => {
                card.style.transition = '';
            }, 50);
        }, 350);
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderPhotoDots();
    });
