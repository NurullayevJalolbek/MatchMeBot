<!-- Top Header Bar with Logo, Daily Bonus, and Balance -->
<header class="app-top-bar">
    <div class="top-brand-box">
        <div class="top-logo-badge">
            <svg viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </div>
        <div class="top-brand-text">
            <h1 class="top-title">MatchMe</h1>
            <span class="top-subtitle">● DATING APP</span>
        </div>
    </div>

    <div class="top-actions-box">
        <!-- Daily Streak / Bonus Pill -->
        <div class="top-streak-pill" onclick="openBonusModal()">
            <span class="streak-icon">🎁</span>
            <span class="streak-text" id="header-streak-text">1-Kun</span>
            <span class="streak-dot"></span>
        </div>

        <!-- Balance Pill -->
        <div class="top-balance-pill" onclick="openBalanceModal()">
            <span class="balance-icon">🪙</span>
            <span class="balance-text" id="header-balance-text">0 <small>UZS</small></span>
            <button class="balance-plus-btn">+</button>
        </div>
    </div>
</header>
