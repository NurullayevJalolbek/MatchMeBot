<!-- ==================== BOOST BOTTOM SHEET MODAL ==================== -->
<div class="boost-modal-overlay" id="boost-modal-overlay" onclick="closeBoostModal(event)">
    <div class="boost-bottom-sheet" id="boost-bottom-sheet" onclick="event.stopPropagation()">
        
        <!-- Drag Handle -->
        <div class="sheet-drag-handle"></div>

        <!-- Top Right Header Row with Balance & Close Button -->
        <div class="boost-top-header-row">
            <div class="boost-balance-badge" id="boost-modal-balance">
                Balans: 0 UZS
            </div>
            <button type="button" class="sheet-close-btn" onclick="closeBoostModal(event)" title="Yopish">✕</button>
        </div>

        <!-- Center Glowing Rocket Icon -->
        <div class="boost-center-hero">
            <div class="boost-rocket-circle">
                <span class="boost-rocket-icon">🚀</span>
            </div>
            <h3 class="boost-modal-title">Profilni Boost Qilish ⚡</h3>
            <p class="boost-modal-subtitle">Profilingizni 1–o'ringa ko'taring va 5x ko'proq mosliklar oling</p>
        </div>

        <!-- Plans Selection List (Dynamically loaded from database / admin) -->
        <div class="boost-plans-section">
            <div class="boost-section-label">Boost muddatini tanlang:</div>

            <div id="boost-plans-list" class="boost-plans-list">
                <!-- Fallback/Default Plans -->
                @php
                    $plans = \App\Models\BoostPlan::where('is_active', true)->orderBy('order')->get();
                @endphp

                @forelse($plans as $index => $plan)
                    <div class="boost-plan-card {{ $index === 1 ? 'active' : '' }}" id="boost-plan-{{ $plan->id }}" onclick="selectBoostPlan({{ $plan->id }}, {{ $plan->price }})">
                        @if($plan->badge)
                            <span class="plan-floating-badge {{ $plan->badge_type === 'super' ? 'badge-super' : 'badge-popular' }}">{{ $plan->badge }}</span>
                        @endif
                        <div class="plan-info-left">
                            <div class="plan-title-row">
                                <span class="plan-emoji">{{ $plan->icon ?? '⚡' }}</span>
                                <span class="plan-name">{{ $plan->name }}</span>
                            </div>
                            <div class="plan-subtext">{{ $plan->subtitle }}</div>
                        </div>
                        <div class="plan-price-right">
                            <span class="plan-current-price">{{ $plan->formatted_price }}</span>
                            @if($plan->formatted_original_price)
                                <span class="plan-old-price">{{ $plan->formatted_original_price }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Default 3 plans if none seeded yet -->
                    <div class="boost-plan-card active" id="boost-plan-1" onclick="selectBoostPlan(1, 20000)">
                        <span class="plan-floating-badge badge-popular">MASHHUR 🔥</span>
                        <div class="plan-info-left">
                            <div class="plan-title-row">
                                <span class="plan-emoji">🚀</span>
                                <span class="plan-name">3 soatlik Boost</span>
                            </div>
                            <div class="plan-subtext">3 soat TOP-1 • 33% tejash</div>
                        </div>
                        <div class="plan-price-right">
                            <span class="plan-current-price">20,000 UZS</span>
                            <span class="plan-old-price">30,000 UZS</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Submit Button -->
        <div class="boost-action-row">
            <button type="button" class="btn-activate-boost" id="btn-activate-boost" onclick="activateSelectedBoost()">
                <span class="btn-icon">⚡</span>
                <span id="btn-boost-label">Balansdan Faollashtirish (20,000 UZS)</span>
            </button>
        </div>

    </div>
</div>
