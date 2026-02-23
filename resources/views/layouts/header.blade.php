<header class="nxl-header">
    <div class="header-wrapper">
        <!--! [Start] Header Left !-->
        <div class="header-left d-flex align-items-center gap-4">
            <!--! [Start] nxl-head-mobile-toggler !-->
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <!--! [Start] nxl-head-mobile-toggler !-->
            <!--! [Start] nxl-navigation-toggle !-->
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            <!--! [End] nxl-navigation-toggle !-->
            <!--! [Start] nxl-lavel-mega-menu-toggle !-->
            <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open">
                    <i class="feather-align-left"></i>
                </a>
            </div>
        </div>
        <!--! [End] Header Left !-->
        <!--! [Start] Header Right !-->
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                <div class="nxl-h-item nxl-header-rates d-none d-xxl-flex">
                    <div class="rates-marquee" aria-label="Exchange rates" aria-live="polite">
                        <div class="rates-rotator">
                            <span class="rate-pill">USD: 12 850</span>
                            <span class="rate-pill">RUB: 145</span>
                            <span class="rate-pill">EUR: 13 980</span>
                            <span class="rate-pill">KZT: 25</span>
                            <span class="rate-pill rate-clone">USD: 12 850</span>
                        </div>
                    </div>
                </div>

                <div class="nxl-h-item nxl-header-search-inline d-none d-lg-flex">
                    <div class="header-search-box">
                        <i class="feather-search"></i>
                        <input type="text" class="header-search-input" placeholder="Search..." />
                    </div>
                </div>

                <div class="nxl-h-item nxl-header-language d-none d-sm-flex p-2">
                    <div class="header-language-inline">
                        <a href="javascript:void(0);" class="lang-item active" title="Uzbek">
                            <img src="{{ asset('assets/vendors/img/flags/4x3/uz.svg') }}" alt="UZ" />
                            <span>O'Z</span>
                        </a>
                        <a href="javascript:void(0);" class="lang-item" title="Russian">
                            <img src="{{ asset('assets/vendors/img/flags/4x3/ru.svg') }}" alt="RU" />
                            <span>РУ</span>
                        </a>
                        <a href="javascript:void(0);" class="lang-item" title="English">
                            <img src="{{ asset('assets/vendors/img/flags/4x3/gb.svg') }}" alt="EN" />
                            <span>EN</span>
                        </a>
                    </div>
                </div>
                
                <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div>
                <div class="nxl-h-item dark-light-theme">
                    <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                        <i class="feather-moon"></i>
                    </a>
                    <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                        <i class="feather-sun"></i>
                    </a>
                </div>

                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="assets/images/avatar/1.png" alt="user-image" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="assets/images/avatar/1.png" alt="user-image" class="img-fluid user-avtar" />
                                <div>
                                    <h6 class="text-dark mb-0">Alexandra Della <span class="badge bg-soft-success text-success ms-1">PRO</span></h6>
                                    <span class="fs-12 fw-medium text-muted">alex@example.com</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="dropdown">
                                <span class="hstack">
                                    <i class="wd-10 ht-10 border border-2 border-gray-1 bg-success rounded-circle me-2"></i>
                                    <span>Active</span>
                                </span>
                                <i class="feather-chevron-right ms-auto me-0"></i>
                            </a>
                            <div class="dropdown-menu">
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-10 ht-10 border border-2 border-gray-1 bg-warning rounded-circle me-2"></i>
                                        <span>Always</span>
                                    </span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-10 ht-10 border border-2 border-gray-1 bg-success rounded-circle me-2"></i>
                                        <span>Active</span>
                                    </span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-10 ht-10 border border-2 border-gray-1 bg-danger rounded-circle me-2"></i>
                                        <span>Bussy</span>
                                    </span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-10 ht-10 border border-2 border-gray-1 bg-info rounded-circle me-2"></i>
                                        <span>Inactive</span>
                                    </span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-10 ht-10 border border-2 border-gray-1 bg-dark rounded-circle me-2"></i>
                                        <span>Disabled</span>
                                    </span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-10 ht-10 border border-2 border-gray-1 bg-primary rounded-circle me-2"></i>
                                        <span>Cutomization</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="dropdown">
                                <span class="hstack">
                                    <i class="feather-dollar-sign me-2"></i>
                                    <span>Subscriptions</span>
                                </span>
                                <i class="feather-chevron-right ms-auto me-0"></i>
                            </a>
                            <div class="dropdown-menu">
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                        <span>Plan</span>
                                    </span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                        <span>Billings</span>
                                    </span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                        <span>Referrals</span>
                                    </span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                        <span>Payments</span>
                                    </span>
                                </a>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                        <span>Statements</span>
                                    </span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item">
                                    <span class="hstack">
                                        <i class="wd-5 ht-5 bg-gray-500 rounded-circle me-3"></i>
                                        <span>Subscriptions</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-user"></i>
                            <span>Profile Details</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-activity"></i>
                            <span>Activity Feed</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-dollar-sign"></i>
                            <span>Billing Details</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-bell"></i>
                            <span>Notifications</span>
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="feather-settings"></i>
                            <span>Account Settings</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="./auth-login-minimal.html" class="dropdown-item">
                            <i class="feather-log-out"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!--! [End] Header Right !-->
    </div>
</header>
<style>
    .nxl-header .header-right {
        min-width: 0;
    }

    .nxl-header .header-right > .d-flex {
        flex-wrap: nowrap;
        gap: 2px;
    }

    .nxl-header .nxl-header-rates {
        width: min(260px, 20vw);
        padding-right: 12px;
    }

    .nxl-header .rates-marquee {
        width: 100%;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
        height: 40px;
        display: flex;
        align-items: center;
    }

    .nxl-header .rates-rotator {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: row;
        transform: translateX(0);
        transition: transform .55s ease;
    }

    .nxl-header .rate-pill {
        min-width: 100%;
        height: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .01em;
        padding: 0 14px;
    }

    .nxl-header .nxl-header-search-inline {
        padding-right: 10px;
    }

    .nxl-header .header-search-box {
        width: min(420px, 30vw);
        height: 40px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 14px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
        transition: all .2s ease;
    }

    .nxl-header .header-search-box i {
        color: #94a3b8;
        font-size: 16px;
        line-height: 1;
    }

    .nxl-header .header-search-input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: #1e293b;
        font-size: 14px;
        font-weight: 500;
    }

    .nxl-header .header-search-input::placeholder {
        color: #94a3b8;
    }

    .nxl-header .header-search-box:focus-within {
        border-color: #3454d1;
        box-shadow: 0 0 0 3px rgba(52, 84, 209, .12);
        background: #ffffff;
    }

    .nxl-header .header-language-inline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px;
        border-radius: 999px;
        background: #f3f4f6;
    }

    .nxl-header .header-language-inline .lang-item {
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        gap: 6px;
        padding: 2px 10px 2px 8px;
        color: #334155;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .03em;
        transition: all .2s ease;
    }

    .nxl-header .header-language-inline .lang-item img {
        width: 20px;
        height: 14px;
        object-fit: cover;
        border-radius: 3px;
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, .25);
    }

    .nxl-header .header-language-inline .lang-item:hover {
        background: #e5e7eb;
    }

    .nxl-header .header-language-inline .lang-item.active {
        color: #0f172a;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .12);
    }

    html.app-skin-dark .nxl-header .header-language-inline {
        background: #0f172a;
        border: 1px solid #1b2436;
    }

    html.app-skin-dark .nxl-header .rates-marquee {
        background: #0f172a;
        border-color: #1b2436;
    }

    html.app-skin-dark .nxl-header .rate-pill {
        color: #cbd5e1;
    }

    html.app-skin-dark .nxl-header .header-search-box {
        background: #0f172a;
        border-color: #1b2436;
    }

    html.app-skin-dark .nxl-header .header-search-box i,
    html.app-skin-dark .nxl-header .header-search-input::placeholder {
        color: #64748b;
    }

    html.app-skin-dark .nxl-header .header-search-input {
        color: #e2e8f0;
    }

    html.app-skin-dark .nxl-header .header-search-box:focus-within {
        border-color: #3454d1;
        box-shadow: 0 0 0 3px rgba(52, 84, 209, .2);
        background: #111b31;
    }

    html.app-skin-dark .nxl-header .header-language-inline .lang-item {
        color: #e2e8f0;
    }

    html.app-skin-dark .nxl-header .header-language-inline .lang-item:hover {
        background: #1b2436;
    }

    html.app-skin-dark .nxl-header .header-language-inline .lang-item.active {
        color: #ffffff;
        background: #3454d1;
        box-shadow: 0 4px 12px rgba(52, 84, 209, .35);
    }

    @media (max-width: 1440px) {
        .nxl-header .nxl-header-rates {
            width: min(220px, 18vw);
        }

        .nxl-header .header-search-box {
            width: min(320px, 24vw);
        }
    }

</style>
<script>
    (function () {
        const rotator = document.querySelector(".rates-rotator");
        if (!rotator) return;

        const items = rotator.querySelectorAll(".rate-pill");
        if (items.length < 2) return;

        const realCount = items.length - 1; // last item is clone of the first
        let index = 0;
        let timer = null;

        const move = () => {
            index += 1;
            rotator.style.transform = `translateX(-${index * 100}%)`;
        };

        const start = () => {
            if (timer) return;
            timer = setInterval(move, 1800);
        };

        const stop = () => {
            if (!timer) return;
            clearInterval(timer);
            timer = null;
        };

        rotator.addEventListener("transitionend", () => {
            if (index !== realCount) return;
            rotator.style.transition = "none";
            index = 0;
            rotator.style.transform = "translateX(0)";
            rotator.offsetHeight; // reflow
            rotator.style.transition = "transform .55s ease";
        });

        rotator.addEventListener("mouseenter", stop);
        rotator.addEventListener("mouseleave", start);

        start();
    })();
</script>
