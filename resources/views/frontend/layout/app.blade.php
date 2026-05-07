<!doctype html>
<html lang="bn" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="ইসলামি শিক্ষা কেন্দ্র - কুরআন ও আরবি শিক্ষার জন্য বাংলাদেশের শীর্ষস্থানীয় প্রতিষ্ঠান। তাজবিদ, হিফজ ও আরবি ব্যাকরণ শিক্ষা।">
    <meta name="keywords" content="ইসলামি শিক্ষা, কুরআন শিক্ষা, আরবি শিক্ষা, তাজবিদ, হিফজ, ইসলামিক এডুকেশন, বাংলাদেশ">
    <meta property="og:title" content="Islamic Education - ইসলামিক এডুকেশন">
    <meta property="og:description" content="কুরআন ও আরবি শিক্ষার জন্য বাংলাদেশের শীর্ষস্থানীয় প্রতিষ্ঠান">
    <meta property="og:type" content="website">
    <title>Islamic Education - ইসলামিক এডুকেশন</title>
    <link rel="preload" as="image" href="/assets/frontend/images/hero-islamic-education.png">
    <link rel="stylesheet" href="/assets/frontend/css/style.css">
    <link rel="stylesheet"
        href="/assets/frontend/css/custom.css?v={{ env('APP_VERSION', rand(1000000000, 9999999999)) }}">
</head>

<body>
    <a class="skip-link" href="#main-body-section">মূল বিষয়বস্তুতে যান</a>

    <header id="header-section" class="brand-header" role="banner" aria-label="সাইট হেডার">
        <div class="container brand-row">
            <div class="brand-cluster">
                <a href="#hero-section" class="brand" aria-label="ইসলামিক এডুকেশন হোম">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 64 64" role="img">
                            <path fill="currentColor"
                                d="M32 9c8 6 13 12 13 21v20h8v6H11v-6h8V30C19 21 24 15 32 9Zm-7 41h14V31c0-5-3-9-7-13-4 4-7 8-7 13v19Zm-8-1h6V32h-6v17Zm24 0h6V32h-6v17Z" />
                        </svg>
                    </span>
                    <span>
                        <span class="arabic-name">التعليم الإسلامي</span>
                        <span class="bangla-name">ইসলামিক এডুকেশন</span>
                        <span class="english-name">Islamic Education</span>
                    </span>
                </a>
            </div>
            <div class="header-actions">
                <form class="search-box" id="site-search" role="search">
                    <label class="visually-hidden" for="search-input">অনুসন্ধান</label>
                    <input id="search-input" type="search" placeholder="খুঁজুন...">
                    <button type="submit" aria-label="অনুসন্ধান করুন"><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-search"></use>
                        </svg></button>
                </form>
                <a class="btn btn-primary" href="#admission-form-section"><svg class="icon" aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check"></use>
                    </svg><span>ভর্তি আবেদন করুন</span></a>
            </div>
        </div>
    </header>

    <nav id="navbar-section" class="nav-wrap" role="navigation" aria-label="প্রধান নেভিগেশন">
        <div class="container">
            <div class="nav-row">
                <button id="mobile-menu-toggle" class="mobile-toggle" type="button" aria-expanded="false"
                    aria-controls="mobile-menu">
                    <span>মেনু</span><svg class="icon" aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-menu"></use>
                    </svg>
                </button>

                <ul class="nav-menu">
                    <li><a class="nav-link active" href="#hero-section">হোম</a></li>
                    <li><a class="nav-link" href="#about-section">আমাদের সম্পর্কে</a></li>
                    <li class="dropdown">
                        <button class="nav-link" type="button" aria-haspopup="true" aria-expanded="false">কোর্স <svg
                                class="icon icon-sm" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-chevron-down"></use>
                            </svg></button>
                        <ul class="dropdown-menu" aria-label="কোর্স সাবমেনু">
                            <li><a class="dropdown-link" href="#courses-section">কুরআন শিক্ষা</a></li>
                            <li><a class="dropdown-link" href="#courses-section">আরবি শিক্ষা</a></li>
                            <li><a class="dropdown-link" href="#courses-section">তাফসির ও অনুবাদ</a></li>
                            <li><a class="dropdown-link" href="#courses-section">হাদিস অধ্যয়ন</a></li>
                            <li><a class="dropdown-link" href="#courses-section">আকিদা ও ফিকহ</a></li>
                            <li><a class="dropdown-link" href="#training-section">শিক্ষক প্রশিক্ষণ</a></li>
                            <li><a class="dropdown-link" href="#digital-courses-section">ডিজিটাল কোর্স</a></li>
                        </ul>
                    </li>
                    <li><a class="nav-link" href="#training-section">প্রশিক্ষণ</a></li>
                    <li><a class="nav-link" href="#maktab-section">মক্তব</a></li>
                    <li><a class="nav-link" href="#muallim-application-section">মুয়াল্লিম আবেদন</a></li>
                    <li><a class="nav-link" href="#calendar-section">ক্যালেন্ডার</a></li>
                    <li><a class="nav-link" href="#results-section">ফলাফল</a></li>
                    <li><a class="nav-link" href="#quick-downloads-section">ডাউনলোড</a></li>
                    <li><a class="nav-link" href="#donation-section">ডোনেশন</a></li>
                    <li><a class="nav-link" href="#contact-us-section">যোগাযোগ</a></li>
                    <li><a class="nav-link" href="#student-login-section">লগইন</a></li>
                </ul>

            </div>

            <ul id="mobile-menu" class="mobile-menu">
                <li><a class="mobile-nav-link" href="#hero-section">হোম</a></li>
                <li><a class="mobile-nav-link" href="#about-section">আমাদের সম্পর্কে</a></li>
                <li>
                    <button id="mobile-course-toggle" class="mobile-nav-link" type="button" aria-expanded="false"
                        aria-controls="mobile-course-menu">
                        <span>কোর্স</span><svg class="icon icon-sm" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-chevron-down"></use>
                        </svg>
                    </button>
                    <ul id="mobile-course-menu" class="mobile-submenu">
                        <li><a class="mobile-nav-link" href="#courses-section">কুরআন শিক্ষা</a></li>
                        <li><a class="mobile-nav-link" href="#courses-section">আরবি শিক্ষা</a></li>
                        <li><a class="mobile-nav-link" href="#courses-section">তাফসির ও অনুবাদ</a></li>
                        <li><a class="mobile-nav-link" href="#courses-section">হাদিস অধ্যয়ন</a></li>
                        <li><a class="mobile-nav-link" href="#courses-section">আকিদা ও ফিকহ</a></li>
                        <li><a class="mobile-nav-link" href="#training-section">শিক্ষক প্রশিক্ষণ</a></li>
                        <li><a class="mobile-nav-link" href="#digital-courses-section">ডিজিটাল কোর্স</a></li>
                    </ul>
                </li>
                <li><a class="mobile-nav-link" href="#training-section">প্রশিক্ষণ</a></li>
                <li><a class="mobile-nav-link" href="#maktab-section">মক্তব</a></li>
                <li><a class="mobile-nav-link" href="#muallim-application-section">মুয়াল্লিম আবেদন</a></li>
                <li><a class="mobile-nav-link" href="#calendar-section">ক্যালেন্ডার</a></li>
                <li><a class="mobile-nav-link" href="#results-section">ফলাফল</a></li>
                <li><a class="mobile-nav-link" href="#quick-downloads-section">ডাউনলোড</a></li>
                <li><a class="mobile-nav-link" href="#donation-section">ডোনেশন</a></li>
                <li><a class="mobile-nav-link" href="#contact-us-section">যোগাযোগ</a></li>
                <li><a class="mobile-nav-link" href="#student-login-section">লগইন</a></li>
            </ul>
        </div>
    </nav>

    <section id="hero-section" class="hero" aria-label="হিরো ব্যানার">
        <div class="container hero-grid">
            <div class="hero-copy reveal">
                <span class="eyebrow">বাংলাদেশের শীর্ষস্থানীয় ইসলামি শিক্ষা প্রতিষ্ঠান</span>
                <h1>কুরআন ও আরবি শিক্ষার <span>আলোকিত পথে স্বাগতম</span></h1>
                <p class="hero-text">তাজবিদ, হিফজ ও আরবি ব্যাকরণ শিক্ষার মাধ্যমে ইসলামের গভীর জ্ঞান অর্জন করুন। অভিজ্ঞ
                    শিক্ষকদের
                    তত্ত্বাবধানে শিখুন, নিজেকে গড়ে তুলুন।</p>
                <div class="hero-actions">
                    <a class="btn btn-gold" href="#admission-form-section"><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check"></use>
                        </svg><span>ভর্তি আবেদন করুন</span></a>
                    <a class="btn btn-outline" href="#courses-section"><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-book-open"></use>
                        </svg><span>কোর্স দেখুন</span></a>
                </div>
                <div class="hero-stats" aria-label="দ্রুত পরিসংখ্যান">
                    <div class="hero-stat"><span class="hero-stat-icon" aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-graduation"></use>
                            </svg></span><span class="hero-stat-copy"><strong
                                data-count="5000">৫০০০+</strong><span>মোট
                                শিক্ষার্থী</span></span></div>
                    <div class="hero-stat"><span class="hero-stat-icon" aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-user-check"></use>
                            </svg></span><span class="hero-stat-copy"><strong data-count="50">৫০+</strong><span>অভিজ্ঞ
                                শিক্ষক</span></span></div>
                    <div class="hero-stat"><span class="hero-stat-icon" aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                            </svg></span><span class="hero-stat-copy"><strong
                                data-count="25">২৫+</strong><span>শাখা</span></span>
                    </div>
                    <div class="hero-stat"><span class="hero-stat-icon" aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-calendar"></use>
                            </svg></span><span class="hero-stat-copy"><strong data-count="10">১০+</strong><span>বছরের
                                অভিজ্ঞতা</span></span></div>
                </div>
            </div>

            <aside class="notice-board reveal" aria-label="নোটিশ বোর্ড">
                <div class="notice-head">
                    <span>নোটিশ বোর্ড</span>
                    <span class="live-badge">Live</span>
                </div>
                <ul class="notice-list">
                    <li><span class="notice-dot"></span><span>নতুন শাখা খোলা হয়েছে ঢাকায় - মিরপুর ও উত্তরায়</span>
                    </li>
                    <li><span class="notice-dot urgent"></span><span>২০২৪ সালের ভর্তি চলছে - আসন সীমিত</span></li>
                    <li><span class="notice-dot"></span><span>কুরআন হিফজ কোর্স শুরু হচ্ছে ১ রমজান থেকে</span></li>
                    <li><span class="notice-dot"></span><span>আরবি ব্যাকরণ (নাহু-সরফ) কোর্সে ভর্তি চলছে</span></li>
                    <li><span class="notice-dot urgent"></span><span>শিক্ষক প্রশিক্ষণ কর্মশালা - ১৫ মার্চ, ঢাকা</span>
                    </li>
                    <li><span class="notice-dot"></span><span>তাজবিদ পরীক্ষার ফলাফল প্রকাশিত হয়েছে</span></li>
                    <li><span class="notice-dot"></span><span>অনলাইন কোর্স রেজিস্ট্রেশন শুরু হয়েছে</span></li>
                    <li><span class="notice-dot"></span><span>ইসলামি সেমিনার - ২০ মার্চ, চট্টগ্রাম</span></li>
                </ul>
            </aside>
        </div>
    </section>

    <main id="main-body-section" class="site-main" tabindex="-1">
        @yield('content')

    </main>

    <footer id="footer-section" class="footer" role="contentinfo">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <div class="footer-brand">
                        <span class="footer-logo" aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-home"></use>
                            </svg></span>
                        <span><span class="footer-brand-arabic">التعليم الإسلامي</span><span
                                class="footer-brand-bangla">ইসলামিক
                                এডুকেশন</span></span>
                    </div>
                    <p>কুরআন তিলাওয়াত, তাজবিদ, হিফজ, মক্তব, প্রশিক্ষণ ও আরবি ব্যাকরণ শিক্ষায় নিবেদিত একটি আধুনিক
                        ইসলামি শিক্ষা
                        প্ল্যাটফর্ম।</p>
                    <div class="footer-trust"><span>১০+ বছরের অভিজ্ঞতা</span><span>৬৪ জেলা প্রতিনিধি</span><span>৫০০০+
                            শিক্ষার্থী</span></div>
                    <div class="social-row" aria-label="সামাজিক যোগাযোগ">
                        <a href="#" data-demo="Facebook লিংক ডেমো" aria-label="Facebook"><svg class="icon"
                                aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-facebook"></use>
                            </svg></a>
                        <a href="#" data-demo="YouTube লিংক ডেমো" aria-label="YouTube"><svg class="icon"
                                aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-youtube"></use>
                            </svg></a>
                        <a href="#" data-demo="Instagram লিংক ডেমো" aria-label="Instagram"><svg class="icon"
                                aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-instagram"></use>
                            </svg></a>
                        <a href="https://wa.me/8801712345678" aria-label="WhatsApp"><svg class="icon"
                                aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-whatsapp"></use>
                            </svg></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>দ্রুত লিংক</h3>
                    <div class="footer-link-groups">
                        <div>
                            <h4>প্রধান মেনু</h4>
                            <ul class="footer-links">
                                <li><a href="#hero-section">হোম</a></li>
                                <li><a href="#courses-section">কোর্সসমূহ</a></li>
                                <li><a href="#maktab-section">মক্তব</a></li>
                                <li><a href="#training-section">প্রশিক্ষণ</a></li>
                                <li><a href="#about-section">আমাদের সম্পর্কে</a></li>
                                <li><a href="#contact-us-section">যোগাযোগ</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4>সেবা</h4>
                            <ul class="footer-links">
                                <li><a href="#calendar-section">ক্যালেন্ডার</a></li>
                                <li><a href="#quick-downloads-section">ডাউনলোড</a></li>
                                <li><a href="#results-section">ফলাফল</a></li>
                                <li><a href="#representatives-section">প্রতিনিধি</a></li>
                                <li><a href="#muallim-application-section">মুয়াল্লিম আবেদন</a></li>
                                <li><a href="#donation-section">ডোনেশন</a></li>
                                <li><a href="#student-login-section">লগইন</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>যোগাযোগ</h3>
                    <div class="footer-contact">
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin"></use>
                            </svg><span>১২৩ ইসলামিক সেন্টার রোড, ঢাকা-১০০০</span></span>
                        <a href="tel:+8801712345678"><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-phone"></use>
                            </svg><span>০১৭১২৩৪৫৬৭৮</span></a>
                        <a href="mailto:info@islamiceducation.com"><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-mail"></use>
                            </svg><span>info@islamiceducation.com</span></a>
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                            </svg><span>শনি-বৃহস্পতিবার, সকাল ৮টা - রাত ৮টা</span></span>
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                            </svg><span>৬৪ জেলা ও উপজেলা/থানা প্রতিনিধি সহায়তা</span></span>
                    </div>
                </div>
                <div class="footer-newsletter">
                    <h3>আপডেট সাবস্ক্রিপশন</h3>
                    <p>নতুন কোর্স, নোটিশ, ক্যালেন্ডার, ফলাফল ও ডাউনলোড আপডেট সরাসরি পেতে সাবস্ক্রাইব করুন।</p>
                    <form id="footer-newsletter-form" novalidate>
                        <label class="visually-hidden" for="footer-email">ইমেইল ঠিকানা</label>
                        <div class="footer-subscribe-field"><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-mail"></use>
                            </svg><input class="input" id="footer-email" type="email" required
                                placeholder="ইমেইল ঠিকানা"></div>
                        <button class="btn btn-gold" type="submit"><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-send"></use>
                            </svg><span>সাবস্ক্রাইব করুন</span></button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© <span id="copyright-year">২০২৪</span> ইসলামিক এডুকেশন। সর্বস্বত্ব সংরক্ষিত।</span>
                <span>শিক্ষা, সেবা ও প্রযুক্তির সমন্বয়ে তৈরি</span>
            </div>
        </div>
    </footer>

    <div class="fixed-social" aria-label="স্থির সামাজিক যোগাযোগ">
        <a href="#" data-demo="Facebook লিংক ডেমো" aria-label="Facebook"><svg class="icon"
                aria-hidden="true">
                <use href="/assets/frontend/images/icons-sprite.svg#icon-facebook"></use>
            </svg></a>
        <a href="#" data-demo="YouTube লিংক ডেমো" aria-label="YouTube"><svg class="icon"
                aria-hidden="true">
                <use href="/assets/frontend/images/icons-sprite.svg#icon-youtube"></use>
            </svg></a>
        <a href="#" data-demo="Instagram লিংক ডেমো" aria-label="Instagram"><svg class="icon"
                aria-hidden="true">
                <use href="/assets/frontend/images/icons-sprite.svg#icon-instagram"></use>
            </svg></a>
        <a href="mailto:info@islamiceducation.com" aria-label="Email"><svg class="icon" aria-hidden="true">
                <use href="/assets/frontend/images/icons-sprite.svg#icon-mail"></use>
            </svg></a>
        <a href="tel:+8801712345678" aria-label="Phone"><svg class="icon" aria-hidden="true">
                <use href="/assets/frontend/images/icons-sprite.svg#icon-phone"></use>
            </svg></a>
    </div>

    <button id="settings-gear" class="settings-toggle" type="button" aria-label="সেটিংস" aria-expanded="false"
        aria-controls="settings-toolbar"><svg class="icon" aria-hidden="true">
            <use href="/assets/frontend/images/icons-sprite.svg#icon-settings"></use>
        </svg></button>
    <div id="settings-toolbar" class="settings-panel" role="dialog" aria-label="সেটিংস প্যানেল">
        <h3>সেটিংস</h3>
        <p class="card-text" style="margin-bottom:6px">থিম</p>
        <div class="segmented">
            <button type="button" data-theme-choice="light">লাইট</button>
            <button type="button" data-theme-choice="dark">ডার্ক</button>
        </div>
        <p class="card-text" style="margin-bottom:6px">ফন্টের আকার</p>
        <div class="segmented three">
            <button type="button" data-font-choice="small">ছোট</button>
            <button type="button" data-font-choice="base">মাঝারি</button>
            <button type="button" data-font-choice="large">বড়</button>
        </div>
    </div>

    <div class="chat-stack" aria-label="দ্রুত যোগাযোগ">
        <a class="messenger" href="https://m.me/islamiceducation" target="_blank" rel="noopener"
            aria-label="Messenger"><svg class="icon" aria-hidden="true">
                <use href="/assets/frontend/images/icons-sprite.svg#icon-messenger"></use>
            </svg></a>
        <a class="whatsapp" href="https://wa.me/8801712345678" target="_blank" rel="noopener"
            aria-label="WhatsApp"><svg class="icon" aria-hidden="true">
                <use href="/assets/frontend/images/icons-sprite.svg#icon-whatsapp"></use>
            </svg></a>
    </div>
    <button id="back-to-top" class="back-top" type="button" aria-label="উপরে যান"><svg class="icon"
            aria-hidden="true">
            <use href="/assets/frontend/images/icons-sprite.svg#icon-arrow-up"></use>
        </svg></button>

    <div id="gallery-lightbox" class="lightbox" role="dialog" aria-modal="true" aria-label="গ্যালারি প্রিভিউ">
        <div class="lightbox-card">
            <div id="lightbox-visual" class="lightbox-visual"></div>
            <div class="lightbox-body">
                <strong id="lightbox-title">গ্যালারি</strong>
                <button class="btn btn-primary" type="button" id="lightbox-close"><svg class="icon"
                        aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-arrow-right"></use>
                    </svg><span>বন্ধ করুন</span></button>
            </div>
        </div>
    </div>

    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    <script src="/assets/frontend/js/bd-locations.js"></script>
    <script src="/assets/frontend/js/main.js"></script>
</body>

</html>
