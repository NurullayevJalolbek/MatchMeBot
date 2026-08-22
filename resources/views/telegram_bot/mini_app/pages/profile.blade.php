@extends('telegram_bot.mini_app.layouts.app')

@section('title', 'Profil — MatchMe')

@section('content')
@php
    $mainUser = auth()->user()
        ?? (session('user_id') ? \App\Models\User::find(session('user_id')) : null)
        ?? \App\Models\User::regularUsers()->latest('id')->first()
        ?? \App\Models\User::latest('id')->first();
    $profileService = app(\App\Contracts\iProfileService::class);
    $profileData = $mainUser ? $profileService->getProfile($mainUser) : null;
    $u = $profileData['user'] ?? [];
    $stats = $profileData['stats'] ?? ['likes_count' => 0, 'matches_count' => 0, 'days_count' => 1];
    $completion = $profileData['completion'] ?? ['percentage' => 85, 'missing' => []];
    $subscriptionPlans = $profileData['subscription_plans'] ?? [];
    $subscriptionFeatures = $profileData['subscription_features'] ?? [];
    $boostPlans = $profileData['boost_plans'] ?? [];
    $filter = $profileData['filter'] ?? null;
@endphp

<div class="profile-page-wrapper">
    <!-- 1. FIXED TOP HERO CONTAINER (QOTIB TURADI) -->
    <div class="profile-hero-fixed-container">
        <div class="profile-hero-card">
            <!-- Edit Button on Top-Right inside Card -->
            <button type="button" class="btn-hero-edit" onclick="window.location.href='/profile/edit'" title="Profilni tahrirlash">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
            </button>

            <!-- Centered Neon Glow Avatar -->
            <div class="avatar-neon-ring">
                <img src="{{ $u['primary_photo_url'] ?? asset('assets/images/avatar/default.png') }}" 
                     alt="{{ $u['name'] ?? 'User' }}" 
                     id="profile-hero-avatar"
                     class="hero-avatar-img"
                     onerror="this.onerror=null; this.src='{{ asset('assets/images/avatar/default.png') }}';">
            </div>

            <!-- Name, Age & Verified Checkmark -->
            <div class="hero-name-row">
                <span class="hero-user-name" id="profile-hero-name">{{ $u['name'] ?? 'Foydalanuvchi' }}</span>
                @if(!empty($u['age']))
                    <span class="hero-user-age" id="profile-hero-age">, {{ $u['age'] }}</span>
                @endif
                <span class="hero-verified-badge" title="Tasdiqlangan">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="#0088cc">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </span>
            </div>

            <!-- Location & Online Status -->
            <div class="hero-location-row">
                <span class="hero-location-text">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#f43f5e">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span id="profile-hero-city">{{ $u['city_label'] ?? 'Toshkent shahri' }}</span>
                </span>
                <span class="hero-dot-separator">•</span>
                <span class="hero-online-badge">
                    <span class="online-green-dot"></span>
                    <span>Online</span>
                </span>
            </div>

            <!-- Stats Dock: Layklar | Mosliklar | Kunlar -->
            <div class="profile-stats-dock">
                <!-- 1. Layklar -->
                <div class="stat-dock-item" onclick="window.location.href='/likes'">
                    <span class="stat-dock-num text-pink" id="stat-likes-count">{{ $stats['likes_count'] }}</span>
                    <span class="stat-dock-label">Layklar</span>
                </div>
                <div class="stat-dock-divider"></div>

                <!-- 2. Mosliklar -->
                <div class="stat-dock-item" onclick="window.location.href='/likes'">
                    <span class="stat-dock-num text-purple" id="stat-matches-count">{{ $stats['matches_count'] }}</span>
                    <span class="stat-dock-label">Mosliklar</span>
                </div>
                <div class="stat-dock-divider"></div>

                <!-- 3. Kunlar -->
                <div class="stat-dock-item">
                    <span class="stat-dock-num text-cyan" id="stat-days-count">{{ $stats['days_count'] }} kun</span>
                    <span class="stat-dock-label">Kunlar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SCROLLABLE CONTAINER (TAGIGA KIRIB KETADI) -->
    <div class="profile-scroll-container">
        
        <!-- PROFILE COMPLETION PROGRESS CARD -->
        <div class="profile-completion-card" onclick="window.location.href='/profile/edit'">
            <div class="completion-header-row">
                <div class="completion-title">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="#fbbf24" class="completion-icon-svg">
                        <path d="M12 2l2.4 7.2h7.6l-6.1 4.5 2.3 7.3-6.2-4.6-6.2 4.6 2.3-7.3-6.1-4.5h7.6z"/>
                    </svg>
                    <span class="completion-label">Profil to'ldirilishi:</span>
                </div>
                <span class="completion-badge" id="completion-percentage-badge">{{ $completion['percentage'] }}% to'ldirildi</span>
            </div>

            <div class="completion-progress-track">
                <div class="completion-progress-fill" id="completion-progress-bar" style="width: {{ $completion['percentage'] }}%;"></div>
            </div>

            <div class="completion-action-pill">
                <div class="action-pill-left">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#fbbf24" stroke-width="2" class="action-gift-svg">
                        <polyline points="20 12 20 22 4 22 4 12"></polyline>
                        <rect x="2" y="7" width="20" height="5"></rect>
                        <line x1="12" y1="22" x2="12" y2="7"></line>
                        <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                        <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
                    </svg>
                    <span class="action-pill-text">100% to'ldiring va <b>3 ta bepul sovg'a</b> oling!</span>
                </div>
                <span class="action-pill-arrow">➔</span>
            </div>
        </div>

        <!-- MENU LIST CARDS -->
        <div class="profile-menu-stack">
            <!-- 1. MatchMe Premium -->
            <div class="profile-menu-card menu-premium" onclick="openPremiumModal()">
                <div class="menu-icon-box bg-gold-gradient">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="#fff">
                        <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/>
                    </svg>
                </div>
                <div class="menu-text-content">
                    <div class="menu-main-title">
                        MatchMe Premium
                    </div>
                    <span class="menu-sub-desc">Cheklovlarsiz foydalaning</span>
                </div>
                <div class="menu-action-badge badge-gold">
                    <span>{{ $u['is_vip'] ? 'Faol Obuna' : 'Obuna bo\'lish' }}</span>
                    <span class="badge-arrow">➔</span>
                </div>
            </div>

            <!-- 2. Instagramni Ulash -->
            <div class="profile-menu-card menu-instagram" onclick="openInstagramModal()">
                <div class="menu-icon-box bg-insta-gradient">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                </div>
                <div class="menu-text-content">
                    <div class="menu-main-title">
                        Instagramni Ulash
                    </div>
                    <span class="menu-sub-desc">Bir martalik ulanish • 40,000 UZS</span>
                </div>
                <div class="menu-action-badge badge-insta">
                    <span id="badge-instagram-val">{{ !empty($u['instagram_username']) ? '@' . $u['instagram_username'] : 'Ulanmagan' }}</span>
                    <span class="badge-arrow">➔</span>
                </div>
            </div>

            <!-- 3. Xarajatlar & Tarix -->
            <div class="profile-menu-card menu-expenses" onclick="openExpensesModal()">
                <div class="menu-icon-box bg-blue-box">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                </div>
                <div class="menu-text-content">
                    <div class="menu-main-title">
                        Xarajatlar & Tarix
                    </div>
                    <span class="menu-sub-desc">Boost, obuna va to'lovlar tarixi</span>
                </div>
                <div class="menu-action-badge badge-subtle">
                    <span>Barcha xarajatlar</span>
                    <span class="badge-arrow">➔</span>
                </div>
            </div>

            <!-- 4. Qidiruv Filtrlari -->
            <div class="profile-menu-card menu-filters" onclick="openFilterModal()">
                <div class="menu-icon-box bg-crimson-box">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#f43f5e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                </div>
                <div class="menu-text-content">
                    <div class="menu-main-title">
                        Qidiruv Filtrlari
                    </div>
                    <span class="menu-sub-desc">Jins va yosh parametrlari</span>
                </div>
                <div class="menu-action-badge badge-subtle">
                    <span id="badge-filters-preview">Qizlar, 18–28 yosh</span>
                    <span class="badge-arrow">➔</span>
                </div>
            </div>

            <!-- 5. Xavfsizlik & Yordam -->
            <div class="profile-menu-card menu-security" onclick="openSupportModal()">
                <div class="menu-icon-box bg-green-box">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </div>
                <div class="menu-text-content">
                    <div class="menu-main-title">
                        Xavfsizlik & Yordam
                    </div>
                    <span class="menu-sub-desc">Qoidalar va qo'llab-quvvatlash</span>
                </div>
                <div class="menu-action-badge badge-arrow-only">
                    <span class="badge-arrow">➔</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== 3. INTERACTIVE MODALS & BOTTOM SHEETS ==================== -->

<!-- Modal 2: MatchMe Premium Bottom-Sheet (Exact Design from Screenshot) -->
<div class="profile-modal-overlay" id="modal-premium" onclick="if(event.target === this) closePremiumModal()">
    <div class="profile-sheet-card premium-bottom-sheet">
        <div class="sheet-drag-handle"></div>
        
        <!-- Glowing Crown Header -->
        <div class="premium-glow-header">
            <div class="premium-crown-circle">
                <svg viewBox="0 0 24 24" width="32" height="32" fill="#0c0e17">
                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/>
                </svg>
            </div>
            <h2 class="premium-sheet-maintitle">MatchMeDating Premium</h2>
            <p class="premium-sheet-maindesc">Cheklovlarsiz foydalaning va sevgingizni 3x tezroq toping</p>
        </div>

        <!-- Features List From Admin DB -->
        <div class="premium-features-scroll-box">
            @forelse($subscriptionFeatures as $feat)
                <div class="premium-feat-row">
                    <div class="feat-icon-bubble">
                        @if($loop->index == 0)
                            <div class="feat-icon-inner feat-icon-pink">💖</div>
                        @elseif($loop->index == 1)
                            <div class="feat-icon-inner feat-icon-yellow">👁️</div>
                        @elseif($loop->index == 2)
                            <div class="feat-icon-inner feat-icon-cyan">🔄</div>
                        @elseif($loop->index == 3)
                            <div class="feat-icon-inner feat-icon-blue">✈️</div>
                        @else
                            <div class="feat-icon-inner feat-icon-gold">👑</div>
                        @endif
                    </div>
                    <div class="feat-text-details">
                        <h4 class="feat-heading-title">{{ $feat->title }}</h4>
                        <p class="feat-heading-desc">{{ $feat->description }}</p>
                    </div>
                </div>
            @empty
                <div class="empty-sheet-msg">Xususiyatlar tez orada yuklanadi.</div>
            @endforelse
        </div>

        <!-- Subscription Plans Selection Grid -->
        <div class="plans-selection-block">
            <label class="plans-block-label">Obuna tarifini tanlang:</label>
            <div class="premium-plans-grid-row" id="premium-plans-row">
                @foreach($subscriptionPlans as $plan)
                    @php
                        $isMiddle = $loop->index == 1 || ($loop->count == 1);
                        $dailyPrice = round($plan->price / max(1, $plan->days));
                    @endphp
                    <div class="premium-plan-box {{ $isMiddle ? 'active' : '' }}"
                         data-plan-id="{{ $plan->id }}"
                         data-price="{{ (int)$plan->price }}"
                         data-formatted-price="{{ format_price($plan->price) }}"
                         data-title="{{ $plan->title }}"
                         onclick="selectSubscriptionPlan(this, {{ $plan->id }}, '{{ addslashes($plan->title) }}', {{ (int)$plan->price }}, '{{ format_price($plan->price) }}')">
                        @if($plan->badge)
                            <span class="plan-top-floating-badge {{ str_contains(strtoupper($plan->badge), 'TEJAM') ? 'badge-save' : 'badge-popular' }}">
                                {{ $plan->badge }}
                            </span>
                        @endif
                        <span class="plan-days-label">{{ $plan->title }}</span>
                        <span class="plan-price-highlight">{{ format_price($plan->price) }}</span>
                        <span class="plan-daily-calc-text">kuniga ~{{ number_format($dailyPrice, 0, '', ' ') }} UZS</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Action Button: Opens Payment & Screenshot Proof Modal -->
        <button type="button" class="btn-golden-activate" id="btn-activate-subscription" onclick="openPaymentReceiptModal()">
            <span class="btn-crown-icon">👑</span>
            <span id="btn-activate-label-text">
                @if(count($subscriptionPlans) > 1 && isset($subscriptionPlans[1]))
                    Obuna bo'lish ({{ format_price($subscriptionPlans[1]->price) }})
                @elseif(count($subscriptionPlans) > 0)
                    Obuna bo'lish ({{ format_price($subscriptionPlans[0]->price) }})
                @else
                    Obuna bo'lish
                @endif
            </span>
        </button>
    </div>
</div>

<!-- Modal 2.1: To'lov & Chek Yuborish Sheet (Payment & Screenshot Proof Modal) -->
<div class="profile-modal-overlay" id="modal-payment-receipt" onclick="if(event.target === this) closePaymentReceiptModal()">
    <div class="profile-sheet-card payment-receipt-sheet">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-header">
            <div class="payment-title-box">
                <h3 class="sheet-title">💳 To'lov & Chekni Yuborish</h3>
            </div>
            <button type="button" class="sheet-close-btn" onclick="closePaymentReceiptModal()">✕</button>
        </div>

        <!-- Selected Plan Summary Badge -->
        <div class="selected-plan-summary-card">
            <div class="plan-summary-left">
                <span class="summary-crown-icon">👑</span>
                <div>
                    <div class="summary-plan-title" id="receipt-summary-plan-title">MatchMe Premium 1 Oylik</div>
                    <div class="summary-plan-sub">Cheklovlarsiz Premium imkoniyatlar</div>
                </div>
            </div>
            <div class="summary-plan-price" id="receipt-summary-plan-price">79 000 UZS</div>
        </div>

        <!-- Bank Card Payment Box -->
        <div class="bank-card-details-box">
            <div class="bank-card-top-row">
                <span class="bank-label">HUMO / UZCARD orqali to'lov qiling:</span>
                <span class="bank-online-tag">24/7 Qabul</span>
            </div>
            <div class="card-number-row">
                <span class="card-digits" id="payment-card-number-text">9860 0301 4528 7890</span>
                <button type="button" class="btn-copy-card" onclick="copyCardNumber()" title="Nusxalash">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span id="copy-btn-text">Nusxalash</span>
                </button>
            </div>
            <div class="bank-card-holder-row">
                <span>Karta egasi: <b>MatchMe Official</b></span>
                <span>To'lov: <b id="payment-exact-amount-text" style="color: #fbbf24;">79 000 UZS</b></span>
            </div>
        </div>

        <!-- Screenshot Upload Dropzone -->
        <div class="receipt-upload-section">
            <label class="receipt-section-label">📸 To'lov cheki skrinshotini yuklang:</label>
            
            <div class="receipt-dropzone" id="receipt-dropzone-box" onclick="document.getElementById('receipt-file-input').click()">
                <div id="receipt-empty-view" class="receipt-empty-state">
                    <div class="dropzone-icon">📷</div>
                    <div class="dropzone-main-text">Skrinshotni tanlash uchun bosing</div>
                    <div class="dropzone-hint-text">JPG, PNG yoki WebP formatida (Maksimal 10MB)</div>
                </div>

                <div id="receipt-preview-view" class="receipt-preview-state" style="display: none;">
                    <img id="receipt-preview-img" src="" alt="To'lov Cheki" class="receipt-img-thumb">
                    <button type="button" class="btn-remove-receipt" onclick="event.stopPropagation(); removeReceiptPreview()">✕ O'chirish</button>
                </div>
            </div>

            <input type="file" id="receipt-file-input" accept="image/jpeg,image/png,image/webp,image/jpg,image/heic" style="display: none;" onchange="handleReceiptFileSelect(this)">
        </div>

        <!-- Optional Notes -->
        <div class="sheet-form-group" style="margin-bottom: 0;">
            <label class="sheet-label">Qo'shimcha izoh / Telegram username (ixtiyoriy):</label>
            <input type="text" id="receipt-input-notes" class="sheet-input" placeholder="Masalan: @username yoki tranzaksiya raqami">
        </div>

        <!-- Submit Button -->
        <button type="button" class="sheet-submit-btn bg-gold-btn" id="btn-submit-receipt" onclick="submitPaymentReceipt()">
            📤 To'lov Chekini Yuborish ➔
        </button>
    </div>
</div>

<!-- Modal 3: Instagramni Ulash (Instagram Link Sheet) -->
<div class="profile-modal-overlay" id="modal-instagram" onclick="if(event.target === this) closeInstagramModal()">
    <div class="profile-sheet-card">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-header">
            <h3 class="sheet-title">📸 Instagramni Ulash</h3>
            <button type="button" class="sheet-close-btn" onclick="closeInstagramModal()">✕</button>
        </div>
        <div class="sheet-body">
            <p class="sheet-desc">Profilingizga Instagram hisobingizni ulang va yangi tanishuvlarga to'g'ridan-to'g'ri o'ting!</p>
            <form onsubmit="submitInstagramLink(event)">
                <div class="sheet-form-group">
                    <label class="sheet-label">Instagram Username</label>
                    <div class="input-with-prefix">
                        <span class="input-prefix">@</span>
                        <input type="text" id="input-instagram-username" class="sheet-input with-prefix-input" 
                               value="{{ $u['instagram_username'] ?? '' }}" 
                               placeholder="username">
                    </div>
                </div>

                <button type="submit" class="sheet-submit-btn bg-insta-btn" id="btn-save-instagram">
                    📸 Instagramni Saqlash
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal 4: Xarajatlar Tarixi (Expenses History Sheet) -->
<div class="profile-modal-overlay" id="modal-expenses" onclick="if(event.target === this) closeExpensesModal()">
    <div class="profile-sheet-card">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-header">
            <h3 class="sheet-title">🧾 Xarajatlar Tarixi</h3>
            <button type="button" class="sheet-close-btn" onclick="closeExpensesModal()">✕</button>
        </div>
        <div class="sheet-body" id="expenses-history-content">
            <div class="loading-spinner-box">
                <div class="spinner-dot"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal 5: Qidiruv Filtrlari (Search Filters Sheet) -->
<div class="profile-modal-overlay" id="modal-filters" onclick="if(event.target === this) closeFilterModal()">
    <div class="profile-sheet-card">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-header">
            <h3 class="sheet-title">🎛️ Qidiruv Filtrlari</h3>
            <button type="button" class="sheet-close-btn" onclick="closeFilterModal()">✕</button>
        </div>
        <div class="sheet-body">
            <p class="sheet-desc">Sizga mos profillarni aniqroq topish uchun parametrlarni o'rnating:</p>
            <form onsubmit="submitFilterPreferences(event)">
                <div class="sheet-form-group">
                    <label class="sheet-label">Kimni qidiryapsiz?</label>
                    <select id="filter-input-gender" class="sheet-input">
                        <option value="all" {{ ($filter->gender ?? 'all') === 'all' ? 'selected' : '' }}>Barchasini</option>
                        <option value="female" {{ ($filter->gender ?? '') === 'female' ? 'selected' : '' }}>Qizlar</option>
                        <option value="male" {{ ($filter->gender ?? '') === 'male' ? 'selected' : '' }}>Yigitlar</option>
                    </select>
                </div>

                <div class="sheet-form-group">
                    <label class="sheet-label">Yosh oralig'i</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="number" id="filter-min-age" class="sheet-input" min="18" max="99" value="{{ $filter->min_age ?? 18 }}" placeholder="Dan">
                        <span style="color: #94a3b8;">—</span>
                        <input type="number" id="filter-max-age" class="sheet-input" min="18" max="99" value="{{ $filter->max_age ?? 35 }}" placeholder="Gacha">
                    </div>
                </div>

                <button type="submit" class="sheet-submit-btn bg-crimson-btn">
                    🎛️ Filtrlarni Saqlash
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal 6: Xavfsizlik & Yordam (Support Sheet) -->
<div class="profile-modal-overlay" id="modal-support" onclick="if(event.target === this) closeSupportModal()">
    <div class="profile-sheet-card">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-header">
            <h3 class="sheet-title">🛡️ Xavfsizlik & Yordam</h3>
            <button type="button" class="sheet-close-btn" onclick="closeSupportModal()">✕</button>
        </div>
        <div class="sheet-body">
            <div class="support-faq-item">
                <div class="support-faq-q">🔒 Xavfsizlik qoidalari</div>
                <div class="support-faq-a">Hech qachon bank karta ma'lumotlaringizni yoki SMS kodlarni boshqalarga yubormang.</div>
            </div>

            <div class="support-faq-item">
                <div class="support-faq-q">💬 Qo'llab-quvvatlash xizmati</div>
                <div class="support-faq-a">Savollar, takliflar yoki muammolar bo'lsa, rasmiy adminimizga murojaat qiling.</div>
            </div>

            <a href="https://t.me/MatchMeSupportBot" target="_blank" class="sheet-submit-btn bg-support-btn">
                📩 Administratorga Yozish
            </a>
        </div>
    </div>
</div>

@endsection

@push("styles")
    <link rel="stylesheet" href="{{ asset('assets/css/miniapp-profile.css') }}?v={{ filemtime(public_path('assets/css/miniapp-profile.css')) }}">
@endpush

@push("scripts")
    <script>
        @php
            $defaultPlan = !empty($subscriptionPlans) ? $subscriptionPlans->first() : null;
        @endphp
        window.DEFAULT_SELECTED_PLAN = {
            id: {{ $defaultPlan ? $defaultPlan->id : "null" }},
            title: "{{ $defaultPlan ? addslashes($defaultPlan->title) : "MatchMe Premium" }}",
            price: {{ $defaultPlan ? $defaultPlan->price : 0 }},
            formattedPrice: "{{ $defaultPlan ? format_price($defaultPlan->price) : "" }}"
        };
    </script>
    <script src="{{ asset('assets/js/miniapp-profile.js') }}?v={{ time() }}"></script>
@endpush
