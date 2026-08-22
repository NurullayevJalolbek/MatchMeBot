<!-- ==================== BOOST BOTTOM SHEET MODAL ==================== -->
<div class="boost-modal-overlay" id="boost-modal-overlay" onclick="closeBoostModal(event)">
    <div class="boost-bottom-sheet" id="boost-bottom-sheet" onclick="event.stopPropagation()">
        
        <!-- Drag Handle -->
        <div class="sheet-drag-handle"></div>

        <!-- Top Right Header Row with Close Button -->
        <div class="boost-top-header-row">
            <div></div>
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
                    $defaultBoost = $plans->where('badge_type', 'popular')->first() ?? $plans->first();
                @endphp

                @forelse($plans as $index => $plan)
                    @php
                        $isActive = ($defaultBoost && $defaultBoost->id === $plan->id) || (!$defaultBoost && $index === 0);
                        $planTitle = $plan->title ?: ($plan->name ?: ($plan->hours . ' soatlik Boost'));
                        $planSub = $plan->subtitle ?: ($plan->description ?: ($plan->hours . " soat TOP-1 bo'lish"));
                        $planPriceFormatted = $plan->formatted_price ?: format_price($plan->price);
                    @endphp
                    <div class="boost-plan-card {{ $isActive ? 'active' : '' }}" 
                         id="boost-plan-{{ $plan->id }}" 
                         onclick="selectBoostPlan({{ $plan->id }}, {{ $plan->price }}, '{{ addslashes($planTitle) }}', '{{ $planPriceFormatted }}', '{{ addslashes($planSub) }}', '{{ $plan->icon ?? '⚡' }}')">
                        @if($plan->badge)
                            <span class="plan-floating-badge {{ $plan->badge_type === 'super' ? 'badge-super' : 'badge-popular' }}">{{ $plan->badge }}</span>
                        @endif
                        <div class="plan-info-left">
                            <div class="plan-title-row">
                                <span class="plan-emoji">{{ $plan->icon ?? '⚡' }}</span>
                                <span class="plan-name">{{ $planTitle }}</span>
                            </div>
                            <div class="plan-subtext">{{ $planSub }}</div>
                        </div>
                        <div class="plan-price-right">
                            <span class="plan-current-price">{{ $planPriceFormatted }}</span>
                            @if($plan->formatted_original_price || $plan->original_price)
                                <span class="plan-old-price">{{ $plan->formatted_original_price ?: format_price($plan->original_price) }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Fallback 3 default plans if DB empty -->
                    <div class="boost-plan-card active" id="boost-plan-1" onclick="selectBoostPlan(1, 20000, '3 soatlik Boost', '20 000 UZS', '3 soat TOP-1 • 33% tejash', '🚀')">
                        <span class="plan-floating-badge badge-popular">MASHHUR 🔥</span>
                        <div class="plan-info-left">
                            <div class="plan-title-row">
                                <span class="plan-emoji">🚀</span>
                                <span class="plan-name">3 soatlik Boost</span>
                            </div>
                            <div class="plan-subtext">3 soat TOP-1 • 33% tejash</div>
                        </div>
                        <div class="plan-price-right">
                            <span class="plan-current-price">20 000 UZS</span>
                            <span class="plan-old-price">30 000 UZS</span>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Submit Button -->
        <div class="boost-action-row">
            <button type="button" class="btn-activate-boost" id="btn-activate-boost" onclick="proceedToBoostPayment()">
                <span class="btn-icon">⚡</span>
                <span id="btn-boost-label">To'lov qilish va Faollashtirish ({{ $defaultBoost ? ($defaultBoost->formatted_price ?: format_price($defaultBoost->price)) : '20 000 UZS' }})</span>
            </button>
        </div>

        <script>
            window.SELECTED_BOOST_PLAN = {
                id: {{ $defaultBoost ? $defaultBoost->id : 1 }},
                title: "{{ $defaultBoost ? addslashes($defaultBoost->title ?: $defaultBoost->name) : '3 soatlik Boost' }}",
                price: {{ $defaultBoost ? $defaultBoost->price : 20000 }},
                formattedPrice: "{{ $defaultBoost ? ($defaultBoost->formatted_price ?: format_price($defaultBoost->price)) : '20 000 UZS' }}",
                subtitle: "{{ $defaultBoost ? addslashes($defaultBoost->subtitle ?: $defaultBoost->description) : '3 soat TOP-1' }}",
                icon: "{{ $defaultBoost ? ($defaultBoost->icon ?? '⚡') : '⚡' }}"
            };
        </script>

    </div>
</div>
