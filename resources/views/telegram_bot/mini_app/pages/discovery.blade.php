@extends('telegram_bot.mini_app.layouts.app')

@section('title', 'MatchMe — Tanishuv')

@section('content')
<div class="discovery-full-wrapper" id="discovery-wrapper">
    
    <!-- ==================== FULLSCREEN BACKGROUND PHOTO ==================== -->
    <div class="discovery-bg-photo-wrapper" id="card-photo-box">
        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=1080&auto=format&fit=crop&q=85" alt="Asal" id="card-bg-image" class="discovery-bg-photo">
    </div>

    <!-- ==================== FLOATING TOP OVERLAY BAR ==================== -->
    <div class="discovery-top-overlay">
        <!-- Top Left: Online Badge & Boost -->
        <div class="top-left-tools">
            <div class="card-online-pill">
                <span class="online-dot"></span>
                <span>Online</span>
            </div>
            <div class="tool-boost-pill" onclick="openBoostModal()">
                <span class="boost-icon">⚡</span>
                <span>Boost</span>
            </div>
        </div>

        <!-- Top Right: Filter & Report Icons (Floating on photo) -->
        <div class="top-right-tools">
            <!-- Filter Icon Button -->
            <button class="tool-icon-btn" onclick="openFilterModal()" title="Filtrlar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="4" y1="21" x2="4" y2="14"></line>
                    <line x1="4" y1="10" x2="4" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12" y2="3"></line>
                    <line x1="20" y1="21" x2="20" y2="16"></line>
                    <line x1="20" y1="12" x2="20" y2="3"></line>
                    <line x1="1" y1="14" x2="7" y2="14"></line>
                    <line x1="9" y1="8" x2="15" y2="8"></line>
                    <line x1="17" y1="16" x2="23" y2="16"></line>
                </svg>
            </button>

            <!-- Wallet / Deposit Balance Button -->
            <button class="tool-icon-btn tool-btn-wallet" onclick="openBalanceModal()" title="Balansni to'ldirish">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ffb800" stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
            </button>
        </div>
    </div>

    <!-- ==================== FLOATING BOTTOM USER DETAILS OVERLAY ==================== -->
    <div class="discovery-bottom-overlay">
        <!-- Name, Verified, VIP & Expand Button -->
        <div class="user-title-row">
            <div class="user-name-box">
                <span class="user-name-text" id="user-name-val">Asal, 18</span>
                <span class="user-verified-badge">
                    <svg viewBox="0 0 24 24"><path fill="#00a8ff" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                </span>
                <span class="user-vip-badge">👑VIP</span>
            </div>

            <!-- Expand Full Profile Button -->
            <button class="btn-open-details" onclick="toggleProfileDetails()" title="To'liq profil">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="18 15 12 9 6 15"></polyline>
                </svg>
            </button>
        </div>

        <!-- City & Distance -->
        <div class="user-city-row">
            <span>📍</span>
            <span id="user-city-val">Toshkent, 2 km</span>
        </div>

        <!-- Interest Section Title -->
        <div class="user-section-title">QIZIQISHLAR</div>

        <!-- Tags / Chips -->
        <div class="user-tags-row" id="user-tags-box">
            <span class="user-tag-pill">Milliy musiqa</span>
            <span class="user-tag-pill">Anime</span>
            <span class="user-tag-pill">Sushi</span>
            <span class="user-tag-pill">Kofe</span>
        </div>

        <!-- Floating 3 Action Buttons (Dislike, Gift, Like) -->
        <div class="discovery-actions-dock">
            <!-- Dislike (X) -->
            <button class="action-circle-btn btn-act-dislike" onclick="handleCardAction('dislike')" title="Keyingisi">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <!-- Gift / Top-1 -->
            <button class="action-circle-btn btn-act-gift" onclick="handleCardAction('gift')" title="Sovg'a yuborish">
                <span class="gift-emoji">🎁</span>
            </button>

            <!-- Like (Heart) -->
            <button class="action-circle-btn btn-act-like" onclick="handleCardAction('like')" title="Layk">
                <svg viewBox="0 0 24 24" fill="#ffffff">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </button>
        </div>
    </div>

</div>
@endsection
