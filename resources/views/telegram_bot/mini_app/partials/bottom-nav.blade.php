<!-- Bottom Navigation Bar (5 Main Tabs) -->
<nav class="app-bottom-nav">
    <!-- 1. Tanishuv (Swipe Cards Deck) -->
    <a href="/discovery" class="nav-item {{ request()->is('discovery*') || request()->is('/') ? 'active' : '' }}" id="nav-item-tanishuv">
        <div class="nav-icon-wrapper">
            <svg viewBox="0 0 24 24" class="nav-icon">
                <path d="M3 5v14c0 1.1.89 2 2 2h14v-2H5V5H3zm16-4H8c-1.11 0-2 .9-2 2v12c0 1.1.89 2 2 2h11c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 14H8V3h11v12z"/>
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
            <span class="nav-badge" id="badge-likes-count">14</span>
        </div>
        <span class="nav-label">Layklar</span>
    </a>

    <!-- 3. Ruletka (Vector Game Dice) -->
    <a href="/roulette" class="nav-item {{ request()->is('roulette*') ? 'active' : '' }}" id="nav-item-ruletka">
        <div class="nav-icon-wrapper">
            <svg viewBox="0 0 24 24" class="nav-icon">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM7.5 18c-.83 0-1.5-.67-1.5-1.5S6.67 15 7.5 15s1.5.67 1.5 1.5S8.33 18 7.5 18zm0-9C6.67 9 6 8.33 6 7.5S6.67 6 7.5 6 9 6.67 9 7.5 8.33 9 7.5 9zm4.5 4.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4.5 4.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm0-9c-.83 0-1.5-.67-1.5-1.5S15.67 6 16.5 6s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
            </svg>
        </div>
        <span class="nav-label">Ruletka</span>
    </a>

    <!-- 4. Chatlar (Chats) -->
    <a href="#chatlar" class="nav-item {{ request()->is('chats*') ? 'active' : '' }}" id="nav-item-chatlar">
        <div class="nav-icon-wrapper">
            <svg viewBox="0 0 24 24" class="nav-icon">
                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
            </svg>
            <span class="nav-badge" id="badge-chats-count">3</span>
        </div>
        <span class="nav-label">Chatlar</span>
    </a>

    <!-- 5. Profil (Profile) -->
    <a href="#profil" class="nav-item {{ request()->is('profile*') ? 'active' : '' }}" id="nav-item-profil">
        <div class="nav-icon-wrapper">
            <svg viewBox="0 0 24 24" class="nav-icon">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
        </div>
        <span class="nav-label">Profil</span>
    </a>
</nav>
