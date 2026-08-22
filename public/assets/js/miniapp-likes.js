/* ==========================================================================
   MINI-APP LIKES PAGE SCRIPTS
   MatchMe Telegram Mini-App
   ========================================================================== */

async function handleLikeAccept(likeId) {
    const card = document.getElementById('like-card-' + likeId);
    if (card) {
        card.style.transform = 'scale(1.05)';
        card.style.opacity = '0';
        setTimeout(() => card.remove(), 250);
    }
    if (window.confetti) {
        confetti({ particleCount: 25, spread: 50 });
    }
    try {
        await fetch('/api/likes/' + likeId + '/accept', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
    } catch (e) {}
}

async function handleLikeReject(likeId) {
    const card = document.getElementById('like-card-' + likeId);
    if (card) {
        card.style.transform = 'scale(0.95)';
        card.style.opacity = '0';
        setTimeout(() => card.remove(), 250);
    }
    try {
        await fetch('/api/likes/' + likeId + '/reject', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
    } catch (e) {}
}
