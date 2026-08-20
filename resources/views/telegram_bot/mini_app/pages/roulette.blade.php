@extends('telegram_bot.mini_app.layouts.app')

@section('content')
<div class="roulette-page-wrapper">
    
    <!-- Clean Top Navigation Header (Matching Layklar header layout) -->
    <header class="roulette-clean-header">
        <div class="roulette-online-pill">
            <span class="online-pulse-dot"></span>
            <span class="online-text">1,840 online</span>
        </div>

        <div class="roulette-center-title">
            <svg viewBox="0 0 24 24" class="nav-title-dice">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM7.5 18c-.83 0-1.5-.67-1.5-1.5S6.67 15 7.5 15s1.5.67 1.5 1.5S8.33 18 7.5 18zm0-9C6.67 9 6 8.33 6 7.5S6.67 6 7.5 6 9 6.67 9 7.5 8.33 9 7.5 9zm4.5 4.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4.5 4.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm0-9c-.83 0-1.5-.67-1.5-1.5S15.67 6 16.5 6s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
            </svg>
            <span class="nav-title-text">Ruletka</span>
        </div>

        <div class="header-right-tools">
            <div class="pill-balance" onclick="openBalanceModal()">
                <span class="balance-icon">🪙</span>
                <span class="balance-val" id="roulette-page-balance">0 UZS</span>
                <span class="balance-plus-badge">+</span>
            </div>
        </div>
    </header>

    <!-- Gender Selector Grid (1 Row with 3 options) -->
    <div class="roulette-gender-section">
        <div class="roulette-gender-grid">
            
            <!-- 1. Faqat Qizlar (VIP) -->
            <div class="roulette-gender-card" id="r-gender-female" onclick="selectRouletteGender('female')">
                <div class="card-vip-crown-badge">
                    <svg viewBox="0 0 24 24" class="svg-crown-mini">
                        <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1v-1h14v1z"/>
                    </svg>
                </div>
                <div class="gender-icon-box female">
                    <svg viewBox="0 0 24 24" class="r-gender-svg">
                        <path d="M12 2a6 6 0 0 0-6 6c0 3 2.25 5.5 5.25 5.92V16H9v2h2.25v3h1.5v-3H15v-2h-2.25v-2.08C15.75 13.5 18 11 18 8a6 6 0 0 0-6-6zm0 10.5A4.5 4.5 0 1 1 16.5 8 4.5 4.5 0 0 1 12 12.5z"/>
                    </svg>
                </div>
                <span class="r-gender-name">Faqat Qizlar</span>
            </div>

            <!-- 2. Faqat Yigitlar (VIP) -->
            <div class="roulette-gender-card" id="r-gender-male" onclick="selectRouletteGender('male')">
                <div class="card-vip-crown-badge">
                    <svg viewBox="0 0 24 24" class="svg-crown-mini">
                        <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .55-.45 1-1 1H6c-.55 0-1-.45-1-1v-1h14v1z"/>
                    </svg>
                </div>
                <div class="gender-icon-box male">
                    <svg viewBox="0 0 24 24" class="r-gender-svg">
                        <path d="M20 2h-6v2h2.59L12.5 8.09A6 6 0 1 0 14 13a5.93 5.93 0 0 0-.91-3.09L17.18 5.82V8h2V2h-.18zM8 17a4 4 0 1 1 4-4 4 4 0 0 1-4 4z"/>
                    </svg>
                </div>
                <span class="r-gender-name">Faqat Yigitlar</span>
            </div>

            <!-- 3. Hamma (Bepul) - Default Active -->
            <div class="roulette-gender-card active" id="r-gender-all" onclick="selectRouletteGender('all')">
                <div class="gender-icon-box all">
                    <svg viewBox="0 0 24 24" class="r-gender-svg">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                </div>
                <span class="r-gender-name">Hamma (Bepul)</span>
            </div>

        </div>
    </div>

    <!-- Center Powerful Radar Wave Ripples Section -->
    <div class="roulette-radar-container">
        
        <!-- Concentric High-Powered Neon Radar Waves -->
        <div class="radar-wave wave-4" id="radar-wave-4"></div>
        <div class="radar-wave wave-3" id="radar-wave-3"></div>
        <div class="radar-wave wave-2" id="radar-wave-2"></div>
        <div class="radar-wave wave-1" id="radar-wave-1"></div>

        <!-- ShareIt-style Discovered Floating Anonymous Users Layer -->
        <div class="radar-nodes-layer" id="radar-nodes-layer"></div>

        <!-- Center Glowing Biological Gender Symbols Emblem -->
        <div class="radar-gender-emblem-box" id="radar-emblem-box">
            <div class="emblem-inner-glow"></div>
            <svg viewBox="0 0 64 64" class="biological-dual-symbol-svg">
                <defs>
                    <linearGradient id="femaleGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#ff2d55"/>
                        <stop offset="100%" stop-color="#ff5379"/>
                    </linearGradient>
                    <linearGradient id="maleGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#38bdf8"/>
                        <stop offset="100%" stop-color="#0284c7"/>
                    </linearGradient>
                    <filter id="neonGlow" x="-20%" y="-20%" width="140%" height="140%">
                        <feGaussianBlur stdDeviation="3" result="blur"/>
                        <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                    </filter>
                </defs>

                <!-- Mars ♂ (Male Symbol - Top Right) -->
                <g class="symbol-male" filter="url(#neonGlow)">
                    <path d="M42 10h12v12M53 11L39 25" stroke="url(#maleGrad)" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </g>

                <!-- Venus ♀ (Female Symbol - Bottom Left) -->
                <g class="symbol-female" filter="url(#neonGlow)">
                    <path d="M25 45v11M19 51h12" stroke="url(#femaleGrad)" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </g>

                <!-- Interlocking Circles -->
                <circle cx="25" cy="33" r="12" stroke="url(#femaleGrad)" stroke-width="4.5" fill="none" filter="url(#neonGlow)"/>
                <circle cx="34" cy="24" r="12" stroke="url(#maleGrad)" stroke-width="4.5" fill="none" filter="url(#neonGlow)"/>
            </svg>
            <div class="avatar-neon-ring"></div>
        </div>

    </div>

    <!-- Status & Information Box -->
    <div class="roulette-status-box">
        <h3 class="roulette-status-title" id="roulette-status-title">Tasodifiy suhbatga tayyormisiz?</h3>
        <p class="roulette-status-sub" id="roulette-status-sub">Qidiruv tugmasini bosing va darhol yangi suhbatdosh bilan tanishing</p>
    </div>

    <!-- Bottom Action Button Row -->
    <div class="roulette-action-wrapper">
        <button type="button" class="btn-roulette-main" id="btn-roulette-main" onclick="toggleRouletteSearch()">
            <svg viewBox="0 0 24 24" class="btn-roulette-svg-icon" id="btn-roulette-icon-search">
                <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            </svg>
            <svg viewBox="0 0 24 24" class="btn-roulette-svg-icon stop-icon" id="btn-roulette-icon-stop" style="display: none;">
                <path d="M6 6h12v12H6z"/>
            </svg>
            <span id="btn-roulette-text">Qidiruvni Boshlash</span>
        </button>
    </div>

</div>

<!-- ==================== VAQTINCHALIK ANONIM CHAT OYNASI ==================== -->
<div class="anonymous-chat-overlay" id="anonymous-chat-overlay">
    
    <!-- Chat Header -->
    <header class="anon-chat-header">
        <div class="anon-header-left">
            <button type="button" class="btn-chat-back" onclick="closeAnonymousChat()" title="Orqaga">
                <svg viewBox="0 0 24 24" class="chat-back-svg">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                </svg>
            </button>
            
            <!-- Anonymous Default User Avatar -->
            <div class="anon-avatar-box">
                <svg viewBox="0 0 24 24" class="default-user-svg">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
                <span class="anon-online-dot"></span>
            </div>

            <div class="anon-user-meta">
                <h4 class="anon-user-name" id="anon-chat-username">Anonim Suhbatdosh</h4>
                <span class="anon-user-status">🟢 Tarmoqda (Vaqtinchalik)</span>
            </div>
        </div>

        <div class="anon-header-right">
            <!-- Quick End / Next Chat Actions -->
            <button type="button" class="btn-quick-end-chat" onclick="skipOrEndChat()">
                <span>Tugatish</span>
            </button>
            <button type="button" class="btn-chat-more" onclick="openChatActionsSheet()" title="Boshqa amallar">
                <svg viewBox="0 0 24 24" class="chat-more-svg">
                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
            </button>
        </div>
    </header>

    <!-- Security Warning Banner -->
    <div class="chat-security-banner">
        <svg viewBox="0 0 24 24" class="security-shield-svg">
            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
        </svg>
        <span class="security-text">
            <strong>Xavfsizlik:</strong> Hech qachon shaxsiy bank karta ma'lumotlarini yoki parollaringizni ulashmang.
        </span>
    </div>

    <!-- Messages Container -->
    <div class="anon-chat-messages" id="anon-chat-messages">
        
        <!-- Welcome intro tag -->
        <div class="chat-system-badge">
            <span>🔒 Anonim suhbat boshlandi. Xabarlar saqlanmaydi.</span>
        </div>

        <!-- Incoming Sample Message -->
        <div class="chat-bubble incoming">
            <div class="bubble-content">
                <p>Salom! Qayerdansiz va kayfiyatlar qanday? 😊</p>
                <span class="bubble-time">14:40</span>
            </div>
        </div>

    </div>

    <!-- Bottom Input Bar -->
    <footer class="anon-chat-input-bar">
        <button type="button" class="btn-attach-media" onclick="simulateMediaAttach()">
            <svg viewBox="0 0 24 24" class="attach-svg">
                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
            </svg>
        </button>

        <div class="chat-input-box">
            <input type="text" id="anon-chat-input" placeholder="Xabar yozing..." onkeypress="handleAnonChatKeyPress(event)" autocomplete="off">
        </div>

    </footer>

</div>

<!-- Chat Options Action Sheet -->
<div class="chat-actions-overlay" id="chat-actions-overlay" onclick="closeChatActionsSheet()">
    <div class="chat-actions-sheet" onclick="event.stopPropagation()">
        <div class="sheet-drag-handle"></div>
        <div class="chat-actions-title">Suhbat Boshqaruvi</div>
        
        <div class="chat-actions-list">
            <!-- 1. Next Partner -->
            <button type="button" class="btn-action-sheet-item primary" onclick="nextAnonPartner()">
                <span class="action-item-icon">⏭️</span>
                <div class="action-item-text">
                    <strong>Keyingi suhbatdoshni qidirish</strong>
                    <span>Hozirgi suhbatni yopib, yangisini qidiradi</span>
                </div>
            </button>

            <!-- 2. End Chat -->
            <button type="button" class="btn-action-sheet-item danger" onclick="closeAnonymousChat(); closeChatActionsSheet();">
                <span class="action-item-icon">⛔</span>
                <div class="action-item-text">
                    <strong>Suhbatni butunlay tugatish</strong>
                    <span>Ruletka oynasiga qaytish</span>
                </div>
            </button>

            <!-- 3. Report User -->
            <button type="button" class="btn-action-sheet-item warning" onclick="reportAnonPartner()">
                <span class="action-item-icon">🚩</span>
                <div class="action-item-text">
                    <strong>Shikoyat qilish (Report)</strong>
                    <span>Nomaqbul xatti-harakat haqida xabar berish</span>
                </div>
            </button>
        </div>

        <button type="button" class="btn-action-sheet-cancel" onclick="closeChatActionsSheet()">Bekor qilish</button>
    </div>
</div>
@endsection
