@extends('telegram_bot.mini_app.layouts.app')

@section('title', 'MatchMe — Tanishuv')

@push('styles')
<style>
    /* Discovery Full-Screen Smooth Card Layout */
    .discovery-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 62px);
        height: calc(100dvh - 62px);
        width: 100%;
        max-width: 480px;
        margin: 0 auto;
        padding: 6px 10px 4px 10px;
        position: relative;
        box-sizing: border-box;
        overflow: hidden;
    }

    /* Main Profile Card - Wide & Proportionate */
    .profile-card-wrapper {
        flex: 1;
        position: relative;
        border-radius: 26px;
        overflow: hidden;
        background: #161824;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.45);
        display: flex;
        flex-direction: column;
        min-height: 0;
        width: 100%;
    }

    /* Photo Slider Container */
    .photo-slider-box {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .profile-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        transition: opacity 0.25s ease;
    }

    /* Tap zones to change photos */
    .photo-tap-left, .photo-tap-right {
        position: absolute;
        top: 0;
        bottom: 120px;
        width: 50%;
        z-index: 10;
        cursor: pointer;
    }
    .photo-tap-left { left: 0; }
    .photo-tap-right { right: 0; }

    /* Indicator Dots on Top of Card */
    .photo-dots-container {
        position: absolute;
        top: 12px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        z-index: 25;
        padding: 0 16px;
    }
    .photo-dot {
        height: 3.5px;
        border-radius: 3px;
        background: rgba(255, 255, 255, 0.35);
        flex: 1;
        max-width: 60px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .photo-dot.active {
        background: #ffffff;
        box-shadow: 0 0 8px rgba(255, 255, 255, 0.85);
    }

    /* Top Floating Tools ON TOP OF PHOTO */
    .card-top-tools {
        position: absolute;
        top: 24px;
        left: 14px;
        right: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 25;
        pointer-events: none;
    }
    .card-top-tools > * {
        pointer-events: auto;
    }

    /* Online Badge (Top Left) */
    .card-online-badge {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 14px;
        padding: 4px 10px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
    }
    .online-indicator {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #2ed573;
        box-shadow: 0 0 6px #2ed573;
    }

    /* Top Right Floating Actions (Boost & Filter) */
    .card-top-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .btn-boost-floating {
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 184, 0, 0.5);
        color: #ffb800;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-boost-floating:active {
        transform: scale(0.94);
    }
    .btn-filter-floating {
        width: 34px;
        height: 34px;
        border-radius: 12px;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-filter-floating:active {
        transform: scale(0.94);
    }

    /* Bottom Gradient Overlay & User Info */
    .card-info-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(180deg, rgba(15, 17, 26, 0) 0%, rgba(15, 17, 26, 0.78) 25%, rgba(15, 17, 26, 0.98) 85%);
        padding: 36px 18px 16px 18px;
        z-index: 15;
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .user-main-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .user-name-title {
        font-size: 22px;
        font-weight: 800;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 0;
    }
    .vip-tag {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        font-size: 10px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }
    .btn-expand-profile {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    .btn-expand-profile:active {
        transform: scale(0.9);
    }

    .user-meta-chips {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .meta-chip {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border-radius: 12px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 500;
        color: #e2e8f0;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }

    .user-bio-text {
        font-size: 13px;
        line-height: 1.4;
        color: #cbd5e1;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .user-interests-row {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 2px;
    }
    .interest-tag {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 18px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        color: #f1f5f9;
        white-space: nowrap;
    }

    /* 3 Action Buttons Dock (Dislike, Gift, Like) */
    .discovery-dock-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        padding: 10px 0 4px 0;
        flex-shrink: 0;
    }
    .btn-action-circle {
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.15s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
        border: none;
        outline: none;
    }
    .btn-action-circle:active {
        transform: scale(0.9);
    }
    
    /* 1. Dislike */
    .btn-dislike {
        width: 56px;
        height: 56px;
        background: #1e202c;
        border: 2px solid rgba(255, 255, 255, 0.1);
        color: #ff4757;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
    }

    /* 2. Gift in Center */
    .btn-gift {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #ffffff;
        font-size: 22px;
        box-shadow: 0 4px 16px rgba(245, 158, 11, 0.4);
    }

    /* 3. Like */
    .btn-like {
        width: 66px;
        height: 66px;
        background: linear-gradient(135deg, #ff4757, #ff6b81);
        color: #ffffff;
        box-shadow: 0 6px 22px rgba(255, 71, 87, 0.45);
    }

    /* Bottom Navigation Bar Setup */
    .app-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 480px;
        height: 58px;
        padding: 4px 4px max(4px, env(safe-area-inset-bottom, 6px)) 4px;
        background-color: #0c0e17;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        justify-content: space-around;
        align-items: center;
        z-index: 100;
        box-sizing: border-box;
    }

    /* ==================== EXPANDED PROFILE DETAILS SHEET ==================== */
    .profile-details-sheet {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        z-index: 150;
        display: none;
        align-items: flex-end;
    }
    .profile-details-sheet.show {
        display: flex;
    }
    .sheet-card-content {
        background: #11131c;
        border-radius: 28px 28px 0 0;
        width: 100%;
        max-height: 88vh;
        overflow-y: auto;
        padding: 16px 18px 45px 18px;
        color: #fff;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.7);
        animation: sheetSlideUp 0.28s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes sheetSlideUp {
        from { transform: translateY(100%); }
        to { transform: translateY(0); }
    }
    .sheet-handle {
        width: 44px;
        height: 5px;
        border-radius: 3px;
        background: rgba(255, 255, 255, 0.25);
        margin: 0 auto 16px auto;
    }

    /* Sheet Header */
    .sheet-header-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .sheet-name {
        font-size: 22px;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .sheet-location {
        font-size: 13px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .btn-sheet-close {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    /* Structured Section Cards */
    .detail-card {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 14px 16px;
        margin-bottom: 12px;
    }
    .detail-card-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.6px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Quick Stats Grid (Bo'yi, Vazni, Yoshi, Kasbi) */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .stat-box {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 16px;
        padding: 10px 8px;
        text-align: center;
    }
    .stat-label {
        font-size: 11px;
        color: #94a3b8;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
    }
    .stat-value {
        font-size: 14px;
        font-weight: 800;
        color: #ffffff;
    }

    /* Parameter Rows (Key-Value) */
    .param-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .param-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .param-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .param-key {
        font-size: 13px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .param-val {
        font-size: 13px;
        font-weight: 700;
        color: #f1f5f9;
        text-align: right;
        background: rgba(255, 255, 255, 0.08);
        padding: 4px 10px;
        border-radius: 10px;
        white-space: nowrap;
    }

    /* Pills Container (Never awkward breaks) */
    .pill-flex-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .pill-badge {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 12.5px;
        font-weight: 600;
        color: #f1f5f9;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .pill-badge.primary {
        background: rgba(52, 84, 209, 0.15);
        border-color: rgba(52, 84, 209, 0.35);
        color: #70a1ff;
    }
    .pill-badge.success {
        background: rgba(46, 213, 115, 0.15);
        border-color: rgba(46, 213, 115, 0.35);
        color: #2ed573;
    }
    .pill-badge.warning {
        background: rgba(255, 184, 0, 0.15);
        border-color: rgba(255, 184, 0, 0.35);
        color: #ffb800;
    }
</style>
@endpush

@section('content')
<div class="discovery-container">
    <!-- Discovery Empty State (Shown when no matching users) -->
    <div class="discovery-empty-state" id="discovery-empty-state" style="display: none;">
        <div class="empty-radar-circle">
            <span class="radar-pulse"></span>
            <span class="radar-icon">🎴</span>
        </div>
        <h3 class="empty-radar-title">Mos anketalar hozircha yo'q</h3>
        <p class="empty-radar-sub">
            Qidiruv mezonlariga mos yangi nomzodlar topilmadi. Filtrni kengaytirib ko'ring yoki yangi foydalanuvchilar qo'shilishini kuting ✨
        </p>
        <button type="button" class="btn-radar-filter" onclick="openFilterModal()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
            <span>Qidiruv Filtrlarini O'zgartirish</span>
        </button>
    </div>

    <!-- Profile Card (Wide & Proportionate) -->
    <div class="profile-card-wrapper" id="profile-card">
        <!-- Photo Dots Indicator (Top Center) -->
        <div class="photo-dots-container" id="photo-dots" style="display: none;"></div>

        <!-- Floating Tools ON TOP OF PHOTO -->
        <div class="card-top-tools">
            <!-- Top Left: Online Badge -->
            <div class="card-online-badge">
                <span class="online-indicator"></span>
                <span>Online</span>
            </div>

            <!-- Top Right: Boost & Filter Floating Buttons -->
            <div class="card-top-actions">
                <button type="button" class="btn-boost-floating" onclick="openBoostModal()">
                    <span>⚡</span>
                    <span>Boost</span>
                </button>
                <button type="button" class="btn-filter-floating" onclick="openFilterModal()" title="Filtr">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
            </div>
        </div>

        <!-- Photo Slider -->
        <div class="photo-slider-box">
            <img src="{{ asset('assets/images/no-avatar.png') }}" 
                 alt="Profile" 
                 class="profile-photo" 
                 id="current-profile-img">
            <!-- Tap areas -->
            <div class="photo-tap-left" onclick="changePhoto(-1)"></div>
            <div class="photo-tap-right" onclick="changePhoto(1)"></div>
        </div>

        <!-- Bottom Info Gradient Overlay -->
        <div class="card-info-overlay">
            <div class="user-main-row">
                <h2 class="user-name-title">
                    <span id="card-user-name">...</span>
                    <span class="vip-tag" style="display: none;">👑 VIP</span>
                </h2>
                <button type="button" class="btn-expand-profile" onclick="toggleProfileDetails()" title="Batafsil">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                </button>
            </div>

            <div class="user-meta-chips">
                <span class="meta-chip">📍 <span id="card-user-city">...</span></span>
            </div>

            <p class="user-bio-text" id="card-user-bio"></p>
        </div>
    </div>

    <!-- 3 Action Buttons Dock (Dislike, Gift, Like) -->
    <div class="discovery-dock-actions">
        <!-- 1. Dislike Button -->
        <button type="button" class="btn-action-circle btn-dislike" onclick="handleCardAction('dislike')" title="Keyingisi">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <!-- 2. Luxury VIP Gift Button in Center -->
        <button type="button" class="btn-action-circle btn-gift" onclick="openVipModal()" title="VIP Sovg'a yuborish">
            <div class="gift-icon-container">
                <svg viewBox="0 0 24 24" class="svg-luxury-gift" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="giftGoldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#fffbeb" />
                            <stop offset="35%" stop-color="#fef08a" />
                            <stop offset="100%" stop-color="#f59e0b" />
                        </linearGradient>
                        <linearGradient id="ribbonGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#ffffff" />
                            <stop offset="100%" stop-color="#fef9c3" />
                        </linearGradient>
                    </defs>
                    <!-- Gift Box Base -->
                    <rect x="3.5" y="10" width="17" height="11" rx="2.5" fill="url(#giftGoldGrad)" />
                    <!-- Gift Box Lid -->
                    <rect x="2" y="6.5" width="20" height="4" rx="1.5" fill="url(#giftGoldGrad)" />
                    <!-- Vertical Ribbon -->
                    <rect x="10.5" y="6.5" width="3" height="14.5" rx="0.5" fill="url(#ribbonGrad)" opacity="0.95" />
                    <!-- Horizontal Ribbon on Lid -->
                    <rect x="2" y="8" width="20" height="1.2" fill="url(#ribbonGrad)" opacity="0.7" />
                    <!-- Bow Left Loop -->
                    <path d="M12 7C10 3.8 6.5 4.2 6.5 5.8C6.5 7.4 10.5 7 12 7Z" fill="url(#ribbonGrad)" />
                    <!-- Bow Right Loop -->
                    <path d="M12 7C14 3.8 17.5 4.2 17.5 5.8C17.5 7.4 13.5 7 12 7Z" fill="url(#ribbonGrad)" />
                    <!-- Bow Center Knot -->
                    <circle cx="12" cy="7" r="1.3" fill="#ffffff" />
                </svg>
                <span class="gift-sparkle-dot"></span>
            </div>
        </button>

        <!-- 3. Like Button -->
        <button type="button" class="btn-action-circle btn-like" onclick="handleCardAction('like')" title="Yoqtirish">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </button>
    </div>
</div>

<!-- ==================== PREMIUM STRUCTURED PROFILE DETAILS SHEET ==================== -->
<div class="profile-details-sheet" id="profile-sheet" onclick="toggleProfileDetails()">
    <div class="sheet-card-content" onclick="event.stopPropagation()">
        <div class="sheet-handle"></div>

        <!-- Header Row -->
        <div class="sheet-header-box">
            <div>
                <h3 class="sheet-name">
                    <span id="sheet-user-name">...</span>
                    <span class="vip-tag" style="display: none;">👑 VIP</span>
                </h3>
                <div class="sheet-location">
                    <span>📍</span>
                    <span id="sheet-user-city">...</span>
                </div>
            </div>
            <button type="button" class="btn-sheet-close" onclick="toggleProfileDetails()">✕</button>
        </div>

        <!-- 1. Tezkor Ko'rsatkichlar (Bo'yi, Vazni, Kasbi) -->
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">📏 Bo'yi</div>
                <div class="stat-value" id="sheet-user-height">Ko'rsatilmagan</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">⚖️ Vazni</div>
                <div class="stat-value" id="sheet-user-weight">Ko'rsatilmagan</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">💼 Kasbi</div>
                <div class="stat-value" id="sheet-user-occ">Ko'rsatilmagan</div>
            </div>
        </div>

        <!-- 2. O'zi Haqida / Bio -->
        <div class="detail-card">
            <div class="detail-card-title">📝 O'zi haqida</div>
            <p class="m-0 text-light fs-14" style="line-height: 1.5;" id="sheet-user-bio"></p>
        </div>

        <!-- 3. Asosiy Maqsad & Oilaviy Holati (Tartibli Karta) -->
        <div class="detail-card">
            <div class="detail-card-title">💍 Maqsad & Oilaviy Holati</div>
            <div class="param-list">
                <div class="param-item">
                    <span class="param-key">🎯 Tanishishdan maqsad:</span>
                    <span class="param-val" id="sheet-user-purpose">💍 Nikoh va oila</span>
                </div>
                <div class="param-item">
                    <span class="param-key">👨‍👩‍👧 Oilaviy holati:</span>
                    <span class="param-val">💍 Birinchi marta turmush</span>
                </div>
            </div>
        </div>

        <!-- 4. Men Haqimda (Ta'lim & Muloqot Uslubi) -->
        <div class="detail-card">
            <div class="detail-card-title">🎓 Ta'lim & Muloqot</div>
            <div class="param-list">
                <div class="param-item">
                    <span class="param-key">🎓 Ta'lim darajasi:</span>
                    <span class="param-val">Oliy (Bakalavr)</span>
                </div>
                <div class="param-item">
                    <span class="param-key">💬 Muloqot uslubi:</span>
                    <span class="param-val">Yozishmalar (SMS)</span>
                </div>
            </div>
        </div>

        <!-- 5. Turmush Tarzi (Lifestyle Parametrlari) -->
        <div class="detail-card">
            <div class="detail-card-title">🍷 Turmush Tarzi</div>
            <div class="param-list">
                <div class="param-item">
                    <span class="param-key">🚭 Chekish odati:</span>
                    <span class="param-val">Chekmayman</span>
                </div>
                <div class="param-item">
                    <span class="param-key">🚫 Ichimliklar:</span>
                    <span class="param-val">Ichmayman</span>
                </div>
                <div class="param-item">
                    <span class="param-key">🥇 Sport mashg'uloti:</span>
                    <span class="param-val">Har kuni sport</span>
                </div>
                <div class="param-item">
                    <span class="param-key">🏠 Tungi hayot:</span>
                    <span class="param-val">Uyda tinch o'tirish</span>
                </div>
            </div>
        </div>

        <!-- 6. Biladigan Tillari -->
        <div class="detail-card">
            <div class="detail-card-title">🌐 Biladigan Tillari</div>
            <div class="pill-flex-container">
                <span class="pill-badge primary">🇺🇿 O'zbek tili</span>
                <span class="pill-badge primary">🇷🇺 Rus tili</span>
                <span class="pill-badge primary">🇬🇧 Ingliz tili</span>
            </div>
        </div>

        <!-- 7. Qiziqishlar -->
        <div class="detail-card">
            <div class="detail-card-title">💖 Qiziqishlar</div>
            <div class="pill-flex-container" id="sheet-interests-box">
                <span class="pill-badge">☕ Qahva</span>
                <span class="pill-badge">🎨 UI/UX</span>
                <span class="pill-badge">✈️ Sayohat</span>
                <span class="pill-badge">🎬 Netflix</span>
                <span class="pill-badge">📚 Kitob o'qish</span>
                <span class="pill-badge">🎧 Lo-Fi musiqa</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
</script>
@endpush
