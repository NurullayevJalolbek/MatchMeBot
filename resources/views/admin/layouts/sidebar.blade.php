<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand">
                <!-- ========   Logo   ============ -->
                <img src="{{ asset('assets/images/logo-full.png') }}" alt="Logo" class="logo logo-lg" />
                <img src="{{ asset('assets/images/logo-abbr.png') }}" alt="Logo" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('admin.boosts*') ? 'active' : '' }}">
                    <a href="{{ route('admin.boosts.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-zap"></i></span>
                        <span class="nxl-mtext">Boost</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('admin.subscriptions*') ? 'active' : '' }}">
                    <a href="{{ route('admin.subscriptions.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-award"></i></span>
                        <span class="nxl-mtext">Obuna Tariflari</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('admin.subscription-features*') ? 'active' : '' }}">
                    <a href="{{ route('admin.subscription-features.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-check-circle"></i></span>
                        <span class="nxl-mtext">Obuna Afzalliklari</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('admin.admins*') ? 'active' : '' }}">
                    <a href="{{ route('admin.admins.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-shield"></i></span>
                        <span class="nxl-mtext">Adminlar</span>
                    </a>
                </li>

                <!-- Moliya Dropdown -->
                @php
                    $isFinanceActive = request()->routeIs('admin.payments*') || request()->routeIs('admin.expenses*') || request()->routeIs('admin.income-categories*') || request()->routeIs('admin.expense-categories*');
                @endphp
                <li class="nxl-item nxl-hasmenu {{ $isFinanceActive ? 'active nxl-trigger' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                        <span class="nxl-mtext">Moliya</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu" style="{{ $isFinanceActive ? 'display: block;' : 'display: none;' }}">
                        <li class="nxl-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                            <a href="{{ route('admin.payments.index') }}" class="nxl-link">
                                <span class="nxl-mtext">Tushumlar (To'lovlar)</span>
                            </a>
                        </li>
                        <li class="nxl-item {{ request()->routeIs('admin.expenses*') ? 'active' : '' }}">
                            <a href="{{ route('admin.expenses.index') }}" class="nxl-link">
                                <span class="nxl-mtext">Xarajatlar</span>
                            </a>
                        </li>
                        <li class="nxl-item {{ request()->routeIs('admin.income-categories*') ? 'active' : '' }}">
                            <a href="{{ route('admin.income-categories.index') }}" class="nxl-link">
                                <span class="nxl-mtext">Tushum Kategoriyalari</span>
                            </a>
                        </li>
                        <li class="nxl-item {{ request()->routeIs('admin.expense-categories*') ? 'active' : '' }}">
                            <a href="{{ route('admin.expense-categories.index') }}" class="nxl-link">
                                <span class="nxl-mtext">Xarajat Kategoriyalari</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Xizmatlar Tarixi Dropdown -->
                @php
                    $isHistoryActive = request()->routeIs('admin.user-subscriptions*') || request()->routeIs('admin.user-boosts*');
                @endphp
                <li class="nxl-item nxl-hasmenu {{ $isHistoryActive ? 'active nxl-trigger' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-clock"></i></span>
                        <span class="nxl-mtext">Xizmatlar Tarixi</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu" style="{{ $isHistoryActive ? 'display: block;' : 'display: none;' }}">
                        <li class="nxl-item {{ request()->routeIs('admin.user-subscriptions*') ? 'active' : '' }}">
                            <a href="{{ route('admin.user-subscriptions.index') }}" class="nxl-link">
                                <span class="nxl-mtext">Obunalar Tarixi</span>
                            </a>
                        </li>
                        <li class="nxl-item {{ request()->routeIs('admin.user-boosts*') ? 'active' : '' }}">
                            <a href="{{ route('admin.user-boosts.index') }}" class="nxl-link">
                                <span class="nxl-mtext">Boostlar Tarixi</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Ma'lumotnomalar Dropdown -->
                @php
                    $isOptionsActive = request()->routeIs('admin.profile-options*');
                    $currentTypeParam = request()->get('type');
                @endphp
                <li class="nxl-item nxl-hasmenu {{ $isOptionsActive ? 'active nxl-trigger' : '' }}">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-list"></i></span>
                        <span class="nxl-mtext">Ma'lumotnomalar</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu" style="{{ $isOptionsActive ? 'display: block;' : 'display: none;' }}">
                        <li class="nxl-item {{ $isOptionsActive && $currentTypeParam === 'interest' ? 'active' : '' }}">
                            <a href="{{ route('admin.profile-options.index', ['type' => 'interest']) }}" class="nxl-link">
                                <span class="nxl-mtext">💖 Qiziqishlar</span>
                            </a>
                        </li>
                        <li class="nxl-item {{ $isOptionsActive && $currentTypeParam === 'dating_purpose' ? 'active' : '' }}">
                            <a href="{{ route('admin.profile-options.index', ['type' => 'dating_purpose']) }}" class="nxl-link">
                                <span class="nxl-mtext">💍 Tanishishdan Maqsad</span>
                            </a>
                        </li>
                        <li class="nxl-item {{ $isOptionsActive && $currentTypeParam === 'lifestyle' ? 'active' : '' }}">
                            <a href="{{ route('admin.profile-options.index', ['type' => 'lifestyle']) }}" class="nxl-link">
                                <span class="nxl-mtext">🍷 Turmush Tarzi</span>
                            </a>
                        </li>
                        <li class="nxl-item {{ $isOptionsActive && $currentTypeParam === 'about_me' ? 'active' : '' }}">
                            <a href="{{ route('admin.profile-options.index', ['type' => 'about_me']) }}" class="nxl-link">
                                <span class="nxl-mtext">🎓 Men Haqimda</span>
                            </a>
                        </li>
                        <li class="nxl-item {{ $isOptionsActive && $currentTypeParam === 'marital_status' ? 'active' : '' }}">
                            <a href="{{ route('admin.profile-options.index', ['type' => 'marital_status']) }}" class="nxl-link">
                                <span class="nxl-mtext">👨‍👩‍👧 Oilaviy Holati</span>
                            </a>
                        </li>
                        <li class="nxl-item {{ $isOptionsActive && $currentTypeParam === 'language' ? 'active' : '' }}">
                            <a href="{{ route('admin.profile-options.index', ['type' => 'language']) }}" class="nxl-link">
                                <span class="nxl-mtext">🌐 Biladigan Tillari</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
