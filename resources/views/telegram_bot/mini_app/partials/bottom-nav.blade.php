<!-- Bottom Navigation Bar (4 Main Tabs) -->
<nav class="app-bottom-nav">
    <!-- 1. Tanishuv (Swipe Cards Deck) -->
    <a href="/discovery" class="nav-item {{ request()->is('discovery*') || request()->is('/') || request()->is('app*') ? 'active' : '' }}" id="nav-item-tanishuv">
        <div class="nav-icon-wrapper">
            <svg viewBox="0 0 24 24" class="nav-icon">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
        </div>
        <span class="nav-label">Tanishuv</span>
    </a>

    <!-- 2. Layklar (Likes Heart) -->
    <a href="/likes" class="nav-item {{ request()->is('likes*') ? 'active' : '' }}" id="nav-item-layklar">
        <div class="nav-icon-wrapper">
            <svg viewBox="0 0 24 24" class="nav-icon">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            <span class="nav-badge" id="badge-likes-count">0</span>
        </div>
        <span class="nav-label">Layklar</span>
    </a>

    <!-- 3. Chatlar (Chats) -->
    <a href="#chatlar" class="nav-item {{ request()->is('chats*') ? 'active' : '' }}" id="nav-item-chatlar" onclick="if(window.showToast) showToast('Chatlar tez orada ishga tushadi'); return false;">
        <div class="nav-icon-wrapper">
            <svg viewBox="0 0 24 24" class="nav-icon">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
            </svg>
        </div>
        <span class="nav-label">Chatlar</span>
    </a>

    <!-- 4. Profil (Profile) -->
    <a href="#profil" class="nav-item {{ request()->is('profile*') ? 'active' : '' }}" id="nav-item-profil" onclick="if(window.showToast) showToast('Profil sozlamalari'); return false;">
        <div class="nav-icon-wrapper">
            <svg viewBox="0 0 24 24" class="nav-icon">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
        </div>
        <span class="nav-label">Profil</span>
    </a>
</nav>
