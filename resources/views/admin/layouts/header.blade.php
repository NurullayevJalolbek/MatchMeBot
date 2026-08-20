<header class="nxl-header">
    <div class="header-wrapper">
        <!--! [Start] Header Left !-->
        <div class="header-left d-flex align-items-center gap-4">
            <!--! [Start] nxl-head-mobile-toggler !-->
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler d-flex d-lg-none" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <!--! [Start] nxl-head-mobile-toggler !-->
            <!--! [Start] nxl-navigation-toggle !-->
            <div class="nxl-navigation-toggle d-none d-lg-flex align-items-center">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            <!--! [End] nxl-navigation-toggle !-->
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
                        <img src="{{ asset('assets/images/avatar/1.png') }}" alt="user-image" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/avatar/1.png') }}" alt="user-image" class="img-fluid user-avtar" />
                                <div>
                                    <h6 class="text-dark mb-0">{{ Auth::user()->name ?? 'Admin' }} <span class="badge bg-soft-success text-success ms-1">ADMIN</span></h6>
                                    <span class="fs-12 fw-medium text-muted">{{ Auth::user()->email ?? 'admin@matchme.uz' }}</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('logout') }}" class="dropdown-item text-danger">
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
    .nxl-header {
        height: 80px !important;
        min-height: 80px !important;
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        z-index: 1025 !important;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
    }

    .nxl-header .header-wrapper {
        height: 80px !important;
        min-height: 80px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 0 30px !important;
    }

    .nxl-header .header-left {
        height: 80px !important;
        display: flex !important;
        align-items: center !important;
    }

    .nxl-header .header-right {
        height: 80px !important;
        display: flex !important;
        align-items: center !important;
        min-width: 0;
    }

    .nxl-header .header-right > .d-flex {
        height: 80px !important;
        display: flex !important;
        align-items: center !important;
        flex-wrap: nowrap !important;
        gap: 8px !important;
    }

    .nxl-header .nxl-h-item {
        height: 80px !important;
        min-height: 80px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 4px !important;
        margin: 0 !important;
    }

    .nxl-header .nxl-head-link {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 40px !important;
        height: 40px !important;
        padding: 0 !important;
        border-radius: 50%;
    }

    .nxl-header .user-avtar {
        width: 38px !important;
        height: 38px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
    }

    .nxl-header .nxl-header-rates {
        width: min(260px, 20vw);
        padding-right: 8px;
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

    [data-theme="dark"] .nxl-header,
    .app-skin-dark .nxl-header,
    .app-dark .nxl-header,
    .dark-theme .nxl-header,
    body.dark .nxl-header {
        background: #111827 !important;
        border-bottom-color: #1f2937 !important;
    }

    [data-theme="dark"] .nxl-header .rates-marquee,
    .app-skin-dark .nxl-header .rates-marquee,
    .app-dark .nxl-header .rates-marquee,
    .dark-theme .nxl-header .rates-marquee,
    body.dark .nxl-header .rates-marquee {
        background: #0f172a !important;
        border-color: #1b2436;
    }

    [data-theme="dark"] .nxl-header .rate-pill,
    .app-skin-dark .nxl-header .rate-pill,
    .app-dark .nxl-header .rate-pill,
    .dark-theme .nxl-header .rate-pill,
    body.dark .nxl-header .rate-pill {
        color: #cbd5e1 !important;
    }
</style>

<script>
    (function () {
        const rotator = document.querySelector(".rates-rotator");
        if (!rotator) return;

        const items = rotator.querySelectorAll(".rate-pill");
        if (items.length < 2) return;

        const realCount = items.length - 1;
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
            rotator.offsetHeight;
            rotator.style.transition = "transform .55s ease";
        });

        rotator.addEventListener("mouseenter", stop);
        rotator.addEventListener("mouseleave", start);

        start();
    })();
</script>
