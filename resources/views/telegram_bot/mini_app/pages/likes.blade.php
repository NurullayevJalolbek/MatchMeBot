@extends('telegram_bot.mini_app.layouts.app')

@section('content')
@php
    $mainUser = auth()->user() ?? \App\Models\User::first();
    $allLikes = \App\Models\UserLike::with('fromUser')
        ->where('to_user_id', $mainUser?->id ?? 1)
        ->where('status', 'pending')
        ->get();
    $vipLikes = $allLikes->where('is_gift', true);
    $regularLikes = $allLikes->where('is_gift', false);
@endphp

<div class="likes-page-wrapper">
    <!-- Content Area -->
    <div class="likes-scroll-content">
        
        @if($allLikes->isEmpty())
            <!-- Completely Empty State (No VIP gifts & No likes) -->
            <div class="likes-empty-container" id="likes-empty-container">
                <div class="empty-glow-circle">
                    <svg viewBox="0 0 24 24" class="empty-heart-svg">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
                <h3 class="empty-title">Hozircha yangi layklar yo'q</h3>
                <p class="empty-description">
                    Profilni yanada ko'proq insonlar ko'rishi uchun tanishuvlar orqali birinchi bo'lib layk bosing yoki profilingizni faollashtiring ✨
                </p>
                <a href="/discovery" class="btn-go-discovery">
                    <span class="btn-go-icon">🎴</span>
                    <span>Tanishuvlarga O'tish</span>
                    <span class="btn-go-arrow">➔</span>
                </a>
            </div>
        @else
            <!-- 1. VIP Gift Senders Section (Only if there are gifts) -->
            @if($vipLikes->isNotEmpty())
                <div class="vip-gifts-card" id="vip-gifts-section">
                    <div class="vip-gifts-header">
                        <div class="vip-title-left">
                            <span class="vip-crown">👑</span>
                            <span class="vip-header-text">VIP Sovg'a Yuborganlar (Top-1)</span>
                        </div>
                        <div class="vip-count-badge" id="vip-count-badge">{{ $vipLikes->count() }} ta sovg'a</div>
                    </div>

                    <div class="vip-cards-row" id="vip-cards-row">
                        @foreach($vipLikes as $like)
                            <div class="vip-profile-card" id="like-card-{{ $like->id }}">
                                <div class="vip-gift-floating-pill">
                                    <span>🎁 {{ $like->gift_icon ?? '🌹' }} {{ $like->gift_name ?? 'Atirgul' }}</span>
                                </div>
                                <img src="{{ $like->fromUser?->primary_photo_url ?? 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=600&auto=format&fit=crop&q=80' }}" alt="{{ $like->fromUser?->name }}" class="vip-card-img">
                                <div class="vip-card-gradient">
                                    <div class="vip-card-info">
                                        <div class="vip-user-name">{{ $like->fromUser?->name ?? 'Diyora' }}, {{ $like->fromUser?->age ?? 22 }}</div>
                                        <div class="vip-user-sub">Top-1 Moslik</div>
                                    </div>
                                    <div class="vip-card-actions">
                                        <button type="button" class="btn-vip-action btn-vip-reject" onclick="handleLikeReject({{ $like->id }})" title="Rad etish">✕</button>
                                        <button type="button" class="btn-vip-action btn-vip-accept" onclick="handleLikeAccept({{ $like->id }})" title="Qabul qilish">
                                            <svg viewBox="0 0 24 24" class="btn-svg-heart">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 2. Regular Likes Section (Only if there are regular likes) -->
            @if($regularLikes->isNotEmpty())
                <div class="regular-likes-section" id="regular-likes-section">
                    <div class="regular-likes-header">
                        <h3 class="regular-likes-title">Barcha Layklar</h3>
                        <span class="regular-count-badge" id="regular-count-badge">{{ $regularLikes->count() }} ta yangi</span>
                    </div>

                    <div class="likes-cards-grid" id="regular-likes-grid">
                        @foreach($regularLikes as $like)
                            <div class="regular-like-card" id="like-card-{{ $like->id }}">
                                <div class="card-image-box">
                                    <img src="{{ $like->fromUser?->primary_photo_url ?? 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=600&auto=format&fit=crop&q=80' }}" alt="{{ $like->fromUser?->name }}" class="regular-card-img">
                                    <div class="card-info-gradient">
                                        <div class="card-user-title">{{ $like->fromUser?->name ?? 'Madina' }}, {{ $like->fromUser?->age ?? 23 }}</div>
                                        <div class="card-user-city">📍 {{ ucfirst(str_replace('_', ' ', $like->fromUser?->city ?? 'Samarkand')) }}</div>
                                    </div>
                                </div>
                                <div class="card-actions-row">
                                    <button type="button" class="btn-card-reject" onclick="handleLikeReject({{ $like->id }})" title="Rad etish">✕</button>
                                    <button type="button" class="btn-card-accept" onclick="handleLikeAccept({{ $like->id }})" title="Layk qaytarish">
                                        <svg viewBox="0 0 24 24" class="btn-svg-heart">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

    </div>

</div>
@endsection
