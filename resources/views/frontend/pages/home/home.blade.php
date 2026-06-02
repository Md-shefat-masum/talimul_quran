@extends('frontend.layout.app')

@section('content')
    <div class="container home-flow">
        <section id="courses-section" class="section-panel reveal">
            <div class="section-head split-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-book-open"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">আমাদের কোর্সসমূহ</h2>
                        <p class="section-subtitle">প্রতি কোর্সের ফি ৳ ৫০০। ন্যূনতম ৩০ জন শিক্ষার্থী হলে নতুন ব্যাচ
                            শুরু হবে।</p>
                    </div>
                </div>
                <a class="btn btn-soft" href="#admission-form-section"><svg class="icon" aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check"></use>
                    </svg><span>ভর্তি আবেদন করুন</span></a>
            </div>
            <div class="grid grid-4 course-showcase">
                <article class="card course-card-premium">
                    <div class="course-visual"><span class="badge">জনপ্রিয়</span><span class="course-icon"
                            aria-hidden="true">ق</span></div>
                    <div class="card-body">
                        <h3 class="card-title">কুরআন তিলাওয়াত ও তাজবিদ</h3>
                        <p class="card-text">সহিহ তিলাওয়াত, মাখরাজ ও তাজবিদের নিয়ম শিক্ষা। শিশু থেকে
                            প্রাপ্তবয়স্ক
                            সকলের জন্য।
                        </p>
                        <div class="meta"><span>সকল বয়স</span><span>শাখা ক্লাস</span></div>
                        <div class="course-detail-strip" aria-label="কোর্স ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                                </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                                </svg><small>ক্লাস মোড</small><strong>শাখা ক্লাস</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg><small>মেয়াদ</small><strong>৩ মাস</strong></span>
                        </div>
                        <div class="course-actions">
                            <a class="btn btn-soft" href="#admission-form-section" data-course-select="quran"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check">
                                    </use>
                                </svg><span>ভর্তি আবেদন করুন</span></a>
                            <button class="btn btn-gold" type="button"
                                data-demo="কুরআন তিলাওয়াত ও তাজবিদ কোর্সের পেমেন্ট ডেমো চালু হয়েছে।"><svg class="icon"
                                    aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><span>পেমেন্ট</span></button>
                        </div>
                    </div>
                </article>
                <article class="card course-card-premium">
                    <div class="course-visual teal"><span class="badge blue">নতুন</span><span class="course-icon"
                            aria-hidden="true">ع</span></div>
                    <div class="card-body">
                        <h3 class="card-title">আরবি ব্যাকরণ (নাহু-সরফ)</h3>
                        <p class="card-text">কুরআন বোঝার জন্য আরবি ভাষার মূল ব্যাকরণ - নাহু, সরফ ও বালাগাত শিক্ষা।
                        </p>
                        <div class="meta"><span>১৫+</span><span>শাখা ক্লাস</span></div>
                        <div class="course-detail-strip" aria-label="কোর্স ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                                </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                                </svg><small>ক্লাস মোড</small><strong>শাখা ক্লাস</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg><small>মেয়াদ</small><strong>৬ মাস</strong></span>
                        </div>
                        <div class="course-actions">
                            <a class="btn btn-soft" href="#admission-form-section" data-course-select="arabic"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check">
                                    </use>
                                </svg><span>ভর্তি আবেদন করুন</span></a>
                            <button class="btn btn-gold" type="button"
                                data-demo="আরবি ব্যাকরণ কোর্সের পেমেন্ট ডেমো চালু হয়েছে।"><svg class="icon"
                                    aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><span>পেমেন্ট</span></button>
                        </div>
                    </div>
                </article>
                <article class="card course-card-premium">
                    <div class="course-visual amber"><span class="course-icon" aria-hidden="true">ح</span></div>
                    <div class="card-body">
                        <h3 class="card-title">কুরআন হিফজ</h3>
                        <p class="card-text">সম্পূর্ণ কুরআন মুখস্থ করার পূর্ণ কোর্স। অভিজ্ঞ হাফেজদের তত্ত্বাবধানে
                            নিয়মিত
                            পর্যালোচনাসহ।</p>
                        <div class="meta"><span>৭-১৮</span><span>নিয়মিত ক্লাস</span></div>
                        <div class="course-detail-strip" aria-label="কোর্স ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                                </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                                </svg><small>ক্লাস মোড</small><strong>নিয়মিত ক্লাস</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg><small>মেয়াদ</small><strong>২-৩ বছর</strong></span>
                        </div>
                        <div class="course-actions">
                            <a class="btn btn-soft" href="#admission-form-section" data-course-select="hifz"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check">
                                    </use>
                                </svg><span>ভর্তি আবেদন করুন</span></a>
                            <button class="btn btn-gold" type="button"
                                data-demo="কুরআন হিফজ কোর্সের পেমেন্ট ডেমো চালু হয়েছে।"><svg class="icon"
                                    aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><span>পেমেন্ট</span></button>
                        </div>
                    </div>
                </article>
                <article class="card course-card-premium">
                    <div class="course-visual navy"><span class="course-icon" aria-hidden="true">ت</span></div>
                    <div class="card-body">
                        <h3 class="card-title">শিক্ষক প্রশিক্ষণ</h3>
                        <p class="card-text">ইসলামি শিক্ষা পদ্ধতি, শিশু মনোবিজ্ঞান ও আধুনিক শিক্ষণ কৌশল নিয়ে
                            পেশাদার প্রশিক্ষণ।
                        </p>
                        <div class="meta"><span>শিক্ষক</span><span>ওয়ার্কশপ</span></div>
                        <div class="course-detail-strip" aria-label="কোর্স ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                                </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                                </svg><small>ক্লাস মোড</small><strong>শাখা / অনলাইন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg><small>মেয়াদ</small><strong>৩ মাস</strong></span>
                        </div>
                        <div class="course-actions">
                            <a class="btn btn-soft" href="#admission-form-section"
                                data-course-select="teacher-training"><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check">
                                    </use>
                                </svg><span>ভর্তি আবেদন করুন</span></a>
                            <button class="btn btn-gold" type="button"
                                data-demo="শিক্ষক প্রশিক্ষণ কোর্সের পেমেন্ট ডেমো চালু হয়েছে।"><svg class="icon"
                                    aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><span>পেমেন্ট</span></button>
                        </div>
                    </div>
                </article>
                <article class="card course-card-premium">
                    <div class="course-visual teal"><span class="badge">বিশেষ</span><span class="course-icon"
                            aria-hidden="true">ت</span></div>
                    <div class="card-body">
                        <h3 class="card-title">কুরআন অনুবাদ ও তাফসির</h3>
                        <p class="card-text">নির্বাচিত সূরা ও আয়াতের অর্থ, প্রেক্ষাপট এবং জীবনঘনিষ্ঠ শিক্ষা সহজ
                            ভাষায় অধ্যয়ন।
                        </p>
                        <div class="meta"><span>১৬+</span><span>সাপ্তাহিক ক্লাস</span></div>
                        <div class="course-detail-strip" aria-label="কোর্স ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                                </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                                </svg><small>ক্লাস মোড</small><strong>সাপ্তাহিক ক্লাস</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg><small>মেয়াদ</small><strong>৬ মাস</strong></span>
                        </div>
                        <div class="course-actions">
                            <a class="btn btn-soft" href="#admission-form-section" data-course-select="tafsir"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check">
                                    </use>
                                </svg><span>ভর্তি আবেদন করুন</span></a>
                            <button class="btn btn-gold" type="button"
                                data-demo="কুরআন অনুবাদ ও তাফসির কোর্সের পেমেন্ট ডেমো চালু হয়েছে।"><svg class="icon"
                                    aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><span>পেমেন্ট</span></button>
                        </div>
                    </div>
                </article>
                <article class="card course-card-premium">
                    <div class="course-visual amber"><span class="course-icon" aria-hidden="true">ح</span></div>
                    <div class="card-body">
                        <h3 class="card-title">হাদিস অধ্যয়ন</h3>
                        <p class="card-text">নির্বাচিত সহিহ হাদিস, আদব-আখলাক এবং দৈনন্দিন জীবনের আমলভিত্তিক শিক্ষা।
                        </p>
                        <div class="meta"><span>সকল বয়স</span><span>শাখা ক্লাস</span></div>
                        <div class="course-detail-strip" aria-label="কোর্স ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                                </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                                </svg><small>ক্লাস মোড</small><strong>শাখা ক্লাস</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg><small>মেয়াদ</small><strong>৪ মাস</strong></span>
                        </div>
                        <div class="course-actions">
                            <a class="btn btn-soft" href="#admission-form-section" data-course-select="hadith"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check">
                                    </use>
                                </svg><span>ভর্তি আবেদন করুন</span></a>
                            <button class="btn btn-gold" type="button"
                                data-demo="হাদিস অধ্যয়ন কোর্সের পেমেন্ট ডেমো চালু হয়েছে।"><svg class="icon"
                                    aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><span>পেমেন্ট</span></button>
                        </div>
                    </div>
                </article>
                <article class="card course-card-premium">
                    <div class="course-visual navy"><span class="badge blue">নতুন</span><span class="course-icon"
                            aria-hidden="true">ف</span></div>
                    <div class="card-body">
                        <h3 class="card-title">ইসলামি আকিদা ও ফিকহ</h3>
                        <p class="card-text">বিশুদ্ধ বিশ্বাস, ইবাদতের মৌলিক বিধান, পবিত্রতা, নামাজ ও প্রয়োজনীয়
                            মাসআলা শিক্ষা।
                        </p>
                        <div class="meta"><span>১৫+</span><span>শাখা ক্লাস</span></div>
                        <div class="course-detail-strip" aria-label="কোর্স ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                                </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                                </svg><small>ক্লাস মোড</small><strong>শাখা ক্লাস</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg><small>মেয়াদ</small><strong>৪ মাস</strong></span>
                        </div>
                        <div class="course-actions">
                            <a class="btn btn-soft" href="#admission-form-section" data-course-select="fiqh"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check">
                                    </use>
                                </svg><span>ভর্তি আবেদন করুন</span></a>
                            <button class="btn btn-gold" type="button"
                                data-demo="ইসলামি আকিদা ও ফিকহ কোর্সের পেমেন্ট ডেমো চালু হয়েছে।"><svg class="icon"
                                    aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><span>পেমেন্ট</span></button>
                        </div>
                    </div>
                </article>
                <article class="card course-card-premium">
                    <div class="course-visual"><span class="course-icon" aria-hidden="true">ط</span></div>
                    <div class="card-body">
                        <h3 class="card-title">শিশু মক্তব ও নৈতিক শিক্ষা</h3>
                        <p class="card-text">শিশুদের জন্য কায়দা, ছোট সূরা, দোয়া, আদব এবং সুন্দর চরিত্র গঠনের
                            প্রাথমিক কোর্স।</p>
                        <div class="meta"><span>শিশু</span><span>সকাল / বিকেল</span></div>
                        <div class="course-detail-strip" aria-label="কোর্স ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                                </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                                </svg><small>ক্লাস মোড</small><strong>সকাল / বিকেল</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg><small>মেয়াদ</small><strong>৬ মাস</strong></span>
                        </div>
                        <div class="course-actions">
                            <a class="btn btn-soft" href="#admission-form-section" data-course-select="maktab"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check">
                                    </use>
                                </svg><span>ভর্তি আবেদন করুন</span></a>
                            <button class="btn btn-gold" type="button"
                                data-demo="শিশু মক্তব ও নৈতিক শিক্ষা কোর্সের পেমেন্ট ডেমো চালু হয়েছে।"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><span>পেমেন্ট</span></button>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section id="training-section" class="service-page training-page reveal" aria-labelledby="training-title">
            <div class="service-hero training-hero">
                <div class="service-copy">
                    <h2 id="training-title">প্রশিক্ষণ</h2>
                    <p>কুরআন শিক্ষক, মক্তব মুয়াল্লিম এবং ইসলামি শিক্ষা পরিচালকদের জন্য পেশাদার প্রশিক্ষণ। পাঠ
                        পরিকল্পনা, শিশু
                        মনোবিজ্ঞান, তাজবিদ সংশোধন, ক্লাস ব্যবস্থাপনা ও মূল্যায়ন পদ্ধতি হাতে-কলমে শেখানো হয়।</p>
                    <div class="service-actions">
                        <a class="btn btn-gold" href="#admission-form-section" data-course-select="teacher-training"><svg
                                class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check"></use>
                            </svg><span>প্রশিক্ষণ আবেদন করুন</span></a>
                        <a class="btn btn-outline" href="#muallim-application-section"><svg class="icon"
                                aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-user-check"></use>
                            </svg><span>মুয়াল্লিম আবেদন</span></a>
                    </div>
                </div>
                <aside class="service-stat-panel" aria-label="প্রশিক্ষণ ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                    <span class="badge">পেশাদার ধাপ</span>
                    <div class="service-stat-grid">
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                            </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                            </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                            </svg><small>ক্লাস মোড</small><strong>শাখা / অনলাইন</strong></span>
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                            </svg><small>মেয়াদ</small><strong>৩ মাস</strong></span>
                    </div>
                </aside>
            </div>
            <div class="service-card-grid">
                <article class="card service-card"><span class="service-card-icon"><svg class="icon"
                            aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-book-open"></use>
                        </svg></span>
                    <h3>পাঠদান কৌশল</h3>
                    <p>শিশু ও প্রাপ্তবয়স্ক শিক্ষার্থীদের জন্য ধাপে ধাপে পাঠ পরিকল্পনা ও ক্লাস পরিচালনা।</p>
                </article>
                <article class="card service-card"><span class="service-card-icon"><svg class="icon"
                            aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-badge-check"></use>
                        </svg></span>
                    <h3>তাজবিদ সংশোধন</h3>
                    <p>মাখরাজ, সিফাত ও সাধারণ ভুল নির্ণয়ের কার্যকর পদ্ধতি।</p>
                </article>
                <article class="card service-card"><span class="service-card-icon"><svg class="icon"
                            aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-message"></use>
                        </svg></span>
                    <h3>অভিভাবক যোগাযোগ</h3>
                    <p>প্রগতি রিপোর্ট, মতামত এবং শৃঙ্খলাবদ্ধ ফলোআপ ব্যবস্থা।</p>
                </article>
            </div>
        </section>

        <section id="maktab-section" class="maktab-page reveal" aria-labelledby="maktab-title">
            <div class="maktab-hero">
                <div class="maktab-copy">
                    <h2 id="maktab-title">শিশু মক্তব ও নৈতিক শিক্ষা</h2>
                    <p>শিশুদের জন্য কায়দা, কুরআন তিলাওয়াতের প্রাথমিক নিয়ম, ছোট সূরা, দোয়া, আদব ও সুন্দর চরিত্র
                        গঠনের
                        সুপরিকল্পিত মক্তব প্রোগ্রাম। অভিভাবকবান্ধব সময়সূচি ও যত্নশীল শিক্ষকের তত্ত্বাবধানে নিয়মিত
                        অগ্রগতি
                        মূল্যায়ন করা হয়।</p>
                    <div class="maktab-actions">
                        <a class="btn btn-gold" href="#admission-form-section" data-course-select="maktab"><svg
                                class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check"></use>
                            </svg><span>মক্তব আবেদন করুন</span></a>
                        <button class="btn btn-outline maktab-outline" type="button"
                            data-demo="মক্তব সিলেবাস ডেমো ডাউনলোড প্রস্তুত।"><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-download"></use>
                            </svg><span>সিলেবাস দেখুন</span></button>
                    </div>
                </div>
                <aside class="maktab-info-card" aria-label="মক্তব ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                    <span class="badge">নতুন ব্যাচ</span>
                    <h3>মক্তব ব্যাচ তথ্য</h3>
                    <div class="maktab-detail-grid">
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                            </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                            </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                            </svg><small>ক্লাস মোড</small><strong>শাখা ক্লাস</strong></span>
                        <span><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                            </svg><small>মেয়াদ</small><strong>৬ মাস</strong></span>
                    </div>
                    <button class="btn btn-primary" type="button" data-demo="মক্তব পেমেন্ট ডেমো চালু হয়েছে।"><svg
                            class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                        </svg><span>পেমেন্ট করুন</span></button>
                </aside>
            </div>
            <div class="maktab-feature-grid">
                <article class="card maktab-feature-card"><span class="maktab-feature-icon" aria-hidden="true"><svg
                            class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-book-open"></use>
                        </svg></span>
                    <h3>কায়দা ও তিলাওয়াত</h3>
                    <p>হরফ চেনা, মাখরাজ, সহজ তাজবিদ এবং ধাপে ধাপে কুরআন তিলাওয়াতের অনুশীলন।</p>
                </article>
                <article class="card maktab-feature-card"><span class="maktab-feature-icon" aria-hidden="true"><svg
                            class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-badge-check"></use>
                        </svg></span>
                    <h3>সূরা ও দোয়া</h3>
                    <p>নামাজের প্রয়োজনীয় সূরা, দৈনন্দিন দোয়া এবং অর্থসহ মৌলিক ইসলামি শিক্ষা।</p>
                </article>
                <article class="card maktab-feature-card"><span class="maktab-feature-icon" aria-hidden="true"><svg
                            class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-heart"></use>
                        </svg></span>
                    <h3>আদব ও আখলাক</h3>
                    <p>সুন্দর আচরণ, সালাম, সত্যবাদিতা, মা-বাবার সম্মান এবং সহপাঠীদের সঙ্গে ভালো ব্যবহার।</p>
                </article>
                <article class="card maktab-feature-card"><span class="maktab-feature-icon" aria-hidden="true"><svg
                            class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-message"></use>
                        </svg></span>
                    <h3>অভিভাবক আপডেট</h3>
                    <p>শিক্ষার্থীর অগ্রগতি, উপস্থিতি এবং শেখার উন্নতি নিয়ে নিয়মিত অভিভাবককে জানানো।</p>
                </article>
            </div>
            <div class="maktab-syllabus-panel">
                <div>
                    <h3>৩ ধাপের মক্তব সিলেবাস</h3>
                </div>
                <div class="maktab-step-list">
                    <span><strong>প্রথম ধাপ</strong><small>হরফ, কায়দা, উচ্চারণ</small></span>
                    <span><strong>দ্বিতীয় ধাপ</strong><small>ছোট সূরা, দোয়া, নামাজ</small></span>
                    <span><strong>তৃতীয় ধাপ</strong><small>তিলাওয়াত, আদব, মূল্যায়ন</small></span>
                </div>
            </div>
        </section>

        <section id="about-section" class="feature-band reveal" aria-labelledby="about-title">
            <div class="feature-copy">
                <h2 id="about-title">আলোকিত সমাজ গঠনের জন্য আধুনিক ইসলামি শিক্ষা</h2>
                <p>বাংলাদেশের শীর্ষস্থানীয় ইসলামি শিক্ষা প্রতিষ্ঠান। কুরআন তিলাওয়াত, তাজবিদ, হিফজ ও আরবি ব্যাকরণ
                    শিক্ষায়
                    আমরা দশ বছরেরও বেশি সময় ধরে নিবেদিত।</p>
                <div class="mission-grid">
                    <div><strong>বিশুদ্ধ তিলাওয়াত</strong><span>মাখরাজ, তাজবিদ ও নিয়মিত অনুশীলন।</span></div>
                    <div><strong>অভিজ্ঞ শিক্ষক</strong><span>যোগ্য শিক্ষকদের তত্ত্বাবধানে সুসংগঠিত পাঠদান।</span>
                    </div>
                    <div><strong>দেশজুড়ে শাখা</strong><span>ঢাকা, চট্টগ্রাম, সিলেট, রাজশাহীসহ বিভিন্ন
                            বিভাগে।</span></div>
                </div>
            </div>
            <aside class="chairman-panel">
                <div class="chairman-arabic">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>
                <blockquote class="chairman-quote"><span>"ইসলামি শিক্ষার মাধ্যমে আমরা এমন একটি আলোকিত সমাজ গড়ে
                        তুলতে চাই,
                        যেখানে প্রতিটি মানুষ কুরআনের জ্ঞানে আলোকিত হবে।"</span></blockquote>
                <div class="chairman-author"><span class="author-mark">মা</span><span><strong>মাওলানা
                            আবদুল্লাহ</strong><small>চেয়ারম্যান</small></span></div>
            </aside>
        </section>

        <section id="digital-courses-section" class="section-panel reveal">
            <div class="section-head split-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">ডিজিটাল কোর্স</h2>
                        <p class="section-subtitle">ঘরে বসেই শিখুন - যেকোনো সময়, যেকোনো জায়গা থেকে</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-2 digital-grid">
                <article class="card digital-card">
                    <div class="digital-visual"><span class="badge">শিক্ষানবিশ</span><span class="play-button"
                            aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                            </svg></span></div>
                    <div class="card-body">
                        <div class="digital-topline"><span>অনলাইন কোর্স</span><span>নিজস্ব গতিতে</span></div>
                        <h3 class="card-title">কুরআন তিলাওয়াত অনলাইন</h3>
                        <div class="meta"><span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                                </svg>৪৫টি ভিডিও</span><span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg>৩০ ঘণ্টা</span></div>
                        <p class="card-text">তাজবিদসহ সহিহ তিলাওয়াত শেখার সম্পূর্ণ ভিডিও কোর্স।</p>
                        <div class="digital-detail-grid" aria-label="ডিজিটাল কোর্স ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                                </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                                </svg><small>ক্লাস মোড</small><strong>অনলাইন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg><small>মেয়াদ</small><strong>৩০ ঘণ্টা</strong></span>
                        </div>
                        <div class="digital-footer">
                            <div class="digital-actions">
                                <a class="btn btn-soft" href="#admission-form-section" data-course-select="digital"><svg
                                        class="icon" aria-hidden="true">
                                        <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check">
                                        </use>
                                    </svg><span>ভর্তি আবেদন</span></a>
                                <button class="btn btn-gold" type="button"
                                    data-demo="অনলাইন কুরআন তিলাওয়াত কোর্সের পেমেন্ট ডেমো চালু হয়েছে।"><svg
                                        class="icon" aria-hidden="true">
                                        <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card">
                                        </use>
                                    </svg><span>পেমেন্ট</span></button>
                            </div>
                        </div>
                    </div>
                </article>
                <article class="card digital-card">
                    <div class="digital-visual teal"><span class="badge blue">মধ্যবর্তী</span><span class="play-button"
                            aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                            </svg></span></div>
                    <div class="card-body">
                        <div class="digital-topline"><span>অনলাইন কোর্স</span><span>উন্নত ধাপ</span></div>
                        <h3 class="card-title">আরবি ব্যাকরণ অনলাইন</h3>
                        <div class="meta"><span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                                </svg>৬০টি ভিডিও</span><span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg>৫০ ঘণ্টা</span></div>
                        <p class="card-text">নাহু-সরফ থেকে বালাগাত পর্যন্ত সম্পূর্ণ আরবি ব্যাকরণ।</p>
                        <div class="digital-detail-grid" aria-label="ডিজিটাল কোর্স ফি, ব্যাচ, ক্লাস মোড ও মেয়াদ">
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card"></use>
                                </svg><small>ফি</small><strong>৳ ৫০০</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                                </svg><small>ব্যাচ শিক্ষার্থী</small><strong>৩০ জন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                                </svg><small>ক্লাস মোড</small><strong>অনলাইন</strong></span>
                            <span><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                                </svg><small>মেয়াদ</small><strong>৫০ ঘণ্টা</strong></span>
                        </div>
                        <div class="digital-footer">
                            <div class="digital-actions">
                                <a class="btn btn-soft" href="#admission-form-section" data-course-select="digital"><svg
                                        class="icon" aria-hidden="true">
                                        <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check">
                                        </use>
                                    </svg><span>ভর্তি আবেদন</span></a>
                                <button class="btn btn-gold" type="button"
                                    data-demo="অনলাইন আরবি ব্যাকরণ কোর্সের পেমেন্ট ডেমো চালু হয়েছে।"><svg class="icon"
                                        aria-hidden="true">
                                        <use href="/assets/frontend/images/icons-sprite.svg#icon-credit-card">
                                        </use>
                                    </svg><span>পেমেন্ট</span></button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section id="admission-form-section" class="admission-split reveal">
            <div class="admission-copy">
                <h2>ভর্তি আবেদন ফর্ম</h2>
                <p>নিচের ফর্মটি পূরণ করুন - আমরা শীঘ্রই যোগাযোগ করব। সব কোর্সের ফি ৳ ৫০০ এবং ন্যূনতম ৩০ জন
                    শিক্ষার্থী হলে নতুন
                    ব্যাচ শুরু হবে।</p>
                <div class="admission-points"><span>আসন সীমিত</span><span>ফি ৳ ৫০০</span><span>ন্যূনতম ৩০ জন
                        ব্যাচ</span>
                </div>
            </div>
            <form class="form-panel" id="admission-form" novalidate>
                <div class="form-grid">
                    <div class="field"><label for="name">পূর্ণ নাম <span class="required">*</span></label><input
                            class="input" type="text" id="name" name="name" required
                            placeholder="আপনার পূর্ণ নাম লিখুন"></div>
                    <div class="field"><label for="phone">মোবাইল নম্বর <span class="required">*</span></label><input
                            class="input" type="tel" id="phone" name="phone" required
                            placeholder="০১XXXXXXXXX">
                    </div>
                    <div class="field"><label for="email">ইমেইল</label><input class="input" type="email"
                            id="email" name="email" placeholder="আপনার ইমেইল"></div>
                    <div class="field"><label for="branch">শাখা নির্বাচন <span
                                class="required">*</span></label><select class="select" id="branch" name="branch"
                            required>
                            <option value="">শাখা নির্বাচন করুন</option>
                            <option value="dhaka">ঢাকা</option>
                            <option value="chittagong">চট্টগ্রাম</option>
                            <option value="sylhet">সিলেট</option>
                            <option value="rajshahi">রাজশাহী</option>
                            <option value="khulna">খুলনা</option>
                        </select></div>
                    <div class="field field-full"><label for="course">কোর্স নির্বাচন <span
                                class="required">*</span></label><select class="select" id="course" name="course"
                            required>
                            <option value="">কোর্স নির্বাচন করুন</option>
                            <option value="quran">কুরআন তিলাওয়াত ও তাজবিদ</option>
                            <option value="arabic">আরবি ব্যাকরণ (নাহু-সরফ)</option>
                            <option value="hifz">কুরআন হিফজ</option>
                            <option value="teacher-training">শিক্ষক প্রশিক্ষণ</option>
                            <option value="tafsir">কুরআন অনুবাদ ও তাফসির</option>
                            <option value="hadith">হাদিস অধ্যয়ন</option>
                            <option value="fiqh">ইসলামি আকিদা ও ফিকহ</option>
                            <option value="maktab">শিশু মক্তব ও নৈতিক শিক্ষা</option>
                            <option value="digital">ডিজিটাল অনলাইন কোর্স</option>
                        </select></div>
                </div>
                <div id="form-message" class="message" role="status"></div><button class="btn btn-primary"
                    type="submit" style="margin-top:16px"><svg class="icon" aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-send"></use>
                    </svg><span>আবেদন জমা দিন</span></button>
            </form>
        </section>

        <section id="muallim-application-section" class="muallim-application reveal" aria-labelledby="muallim-title">
            <div class="muallim-copy">
                <h2 id="muallim-title">মুয়াল্লিম আবেদন</h2>
                <p>যোগ্য আলেম, হাফেজ, কারি ও আরবি শিক্ষকদের জন্য আমাদের শাখা ও অনলাইন ক্লাসে পাঠদান করার সুযোগ।
                    আবেদন যাচাই,
                    সাক্ষাৎকার ও প্রশিক্ষণের মাধ্যমে নির্বাচিত মুয়াল্লিম নিয়োগ করা হবে।</p>
                <div class="muallim-summary-grid" aria-label="মুয়াল্লিম আবেদন তথ্য">
                    <span><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-badge-check"></use>
                        </svg><strong>যোগ্যতা যাচাই</strong><small>কুরআন, তাজবিদ, আরবি বা হিফজে
                            দক্ষতা</small></span>
                    <span><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                        </svg><strong>ব্যাচ সহায়তা</strong><small>৩০ জনের ব্যাচ পরিচালনায় প্রস্তুতি</small></span>
                    <span><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-graduation"></use>
                        </svg><strong>শিক্ষক প্রশিক্ষণ</strong><small>নির্বাচিতদের জন্য পেশাদার
                            ওরিয়েন্টেশন</small></span>
                </div>
                <div class="muallim-flow" aria-label="আবেদন প্রক্রিয়া">
                    <span>আবেদন</span><span>যাচাই</span><span>সাক্ষাৎকার</span><span>নিয়োগ</span>
                </div>
            </div>
            <form class="form-panel muallim-form" id="muallim-form" novalidate>
                <div class="form-grid">
                    <div class="field"><label for="muallim-name">পূর্ণ নাম <span class="required">*</span></label><input
                            class="input" type="text" id="muallim-name" name="muallim-name" required
                            placeholder="আপনার পূর্ণ নাম লিখুন">
                    </div>
                    <div class="field"><label for="muallim-phone">মোবাইল নম্বর <span
                                class="required">*</span></label><input class="input" type="tel" id="muallim-phone"
                            name="muallim-phone" required placeholder="০১XXXXXXXXX">
                    </div>
                    <div class="field"><label for="muallim-email">ইমেইল</label><input class="input" type="email"
                            id="muallim-email" name="muallim-email" placeholder="আপনার ইমেইল">
                    </div>
                    <div class="field"><label for="muallim-district">জেলা <span class="required">*</span></label><input
                            class="input" type="text" id="muallim-district" name="muallim-district" required
                            placeholder="আপনার জেলার নাম">
                    </div>
                    <div class="field"><label for="muallim-qualification">শিক্ষাগত যোগ্যতা <span
                                class="required">*</span></label><select class="select" id="muallim-qualification"
                            name="muallim-qualification" required>
                            <option value="">যোগ্যতা নির্বাচন করুন</option>
                            <option value="alim">আলিম / ফাজিল / কামিল</option>
                            <option value="hafez">হাফেজ / কারি</option>
                            <option value="arabic">আরবি ভাষা ও ব্যাকরণে দক্ষ</option>
                            <option value="teacher">শিক্ষক প্রশিক্ষণপ্রাপ্ত</option>
                        </select></div>
                    <div class="field"><label for="muallim-experience">অভিজ্ঞতা <span
                                class="required">*</span></label><select class="select" id="muallim-experience"
                            name="muallim-experience" required>
                            <option value="">অভিজ্ঞতা নির্বাচন করুন</option>
                            <option value="0-1">০-১ বছর</option>
                            <option value="2-3">২-৩ বছর</option>
                            <option value="4-6">৪-৬ বছর</option>
                            <option value="7+">৭+ বছর</option>
                        </select></div>
                    <div class="field"><label for="muallim-speciality">পাঠদানের বিষয় <span
                                class="required">*</span></label><select class="select" id="muallim-speciality"
                            name="muallim-speciality" required>
                            <option value="">বিষয় নির্বাচন করুন</option>
                            <option value="tajweed">কুরআন তিলাওয়াত ও তাজবিদ</option>
                            <option value="arabic">আরবি ব্যাকরণ</option>
                            <option value="hifz">কুরআন হিফজ</option>
                            <option value="tafsir">তাফসির ও অনুবাদ</option>
                            <option value="hadith">হাদিস ও ফিকহ</option>
                            <option value="maktab">শিশু মক্তব</option>
                        </select></div>
                    <div class="field"><label for="muallim-branch">পছন্দের শাখা <span
                                class="required">*</span></label><select class="select" id="muallim-branch"
                            name="muallim-branch" required>
                            <option value="">শাখা নির্বাচন করুন</option>
                            <option value="dhaka">ঢাকা</option>
                            <option value="chattogram">চট্টগ্রাম</option>
                            <option value="sylhet">সিলেট</option>
                            <option value="rajshahi">রাজশাহী</option>
                            <option value="khulna">খুলনা</option>
                            <option value="online">অনলাইন</option>
                        </select></div>
                    <div class="field field-full"><label for="muallim-notes">সংক্ষিপ্ত পরিচিতি</label>
                        <textarea class="textarea" id="muallim-notes" name="muallim-notes"
                            placeholder="আপনার দক্ষতা, পাঠদানের অভিজ্ঞতা বা আগ্রহের বিষয় সংক্ষেপে লিখুন"></textarea>
                    </div>
                </div>
                <div id="muallim-message" class="message" role="status"></div>
                <button class="btn btn-primary" type="submit" style="margin-top:16px"><svg class="icon"
                        aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-send"></use>
                    </svg><span>মুয়াল্লিম আবেদন জমা দিন</span></button>
            </form>
        </section>

        <section id="gallery-section" class="section-panel reveal">
            <div class="section-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-image"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">ফটো গ্যালারি</h2>
                        <p class="section-subtitle">আমাদের কার্যক্রম ও অনুষ্ঠানের মুহূর্তগুলি</p>
                    </div>
                </div>
            </div>
            <div class="gallery-grid gallery-premium">
                <button class="card gallery-card" type="button" data-gallery="মসজিদ কমপ্লেক্স"
                    aria-label="মসজিদ কমপ্লেক্স দেখুন"><span class="gallery-visual"></span><span
                        class="gallery-label">মসজিদ
                        কমপ্লেক্স <span>↗</span></span></button>
                <button class="card gallery-card" type="button" data-gallery="কুরআন ক্লাস"
                    aria-label="কুরআন ক্লাস দেখুন"><span class="gallery-visual teal"></span><span
                        class="gallery-label">কুরআন
                        ক্লাস <span>↗</span></span></button>
                <button class="card gallery-card" type="button" data-gallery="সমাপনী অনুষ্ঠান"
                    aria-label="সমাপনী অনুষ্ঠান দেখুন"><span class="gallery-visual amber"></span><span
                        class="gallery-label">সমাপনী অনুষ্ঠান <span>↗</span></span></button>
                <button class="card gallery-card" type="button" data-gallery="শিক্ষক প্রশিক্ষণ"
                    aria-label="শিক্ষক প্রশিক্ষণ দেখুন"><span class="gallery-visual navy"></span><span
                        class="gallery-label">শিক্ষক প্রশিক্ষণ <span>↗</span></span></button>
                <button class="card gallery-card" type="button" data-gallery="আরবি ক্লাস"
                    aria-label="আরবি ক্লাস দেখুন"><span class="gallery-visual teal"></span><span
                        class="gallery-label">আরবি ক্লাস <span>↗</span></span></button>
                <button class="card gallery-card" type="button" data-gallery="পুরস্কার বিতরণ"
                    aria-label="পুরস্কার বিতরণ দেখুন"><span class="gallery-visual amber"></span><span
                        class="gallery-label">পুরস্কার বিতরণ <span>↗</span></span></button>
            </div>
        </section>

        <section id="video-gallery-section" class="section-panel reveal">
            <div class="section-head split-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">ভিডিও গ্যালারি</h2>
                        <p class="section-subtitle">ক্লাস, প্রশিক্ষণ ও অনুষ্ঠানের নির্বাচিত ভিডিওসমূহ</p>
                    </div>
                </div>
                <a class="btn btn-soft" href="#digital-courses-section"><svg class="icon" aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                    </svg><span>ডিজিটাল কোর্স দেখুন</span></a>
            </div>
            <div class="video-grid">
                <button class="card video-card" type="button" data-video="কুরআন তিলাওয়াত ক্লাস"
                    aria-label="কুরআন তিলাওয়াত ক্লাস ভিডিও দেখুন"><span class="video-thumb"></span><span
                        class="video-play" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                        </svg></span><span class="video-info"><span class="video-title">কুরআন তিলাওয়াত
                            ক্লাস</span><span class="video-meta"><span>১২
                                মিনিট</span><span>ক্লাসরুম</span></span></span></button>
                <button class="card video-card" type="button" data-video="তাজবিদ পাঠের নমুনা"
                    aria-label="তাজবিদ পাঠের নমুনা ভিডিও দেখুন"><span class="video-thumb video-teal"></span><span
                        class="video-play" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                        </svg></span><span class="video-info"><span class="video-title">তাজবিদ পাঠের
                            নমুনা</span><span class="video-meta"><span>৮
                                মিনিট</span><span>শিক্ষা</span></span></span></button>
                <button class="card video-card" type="button" data-video="আরবি ব্যাকরণ পরিচিতি"
                    aria-label="আরবি ব্যাকরণ পরিচিতি ভিডিও দেখুন"><span class="video-thumb video-amber"></span><span
                        class="video-play" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                        </svg></span><span class="video-info"><span class="video-title">আরবি ব্যাকরণ
                            পরিচিতি</span><span class="video-meta"><span>১০
                                মিনিট</span><span>অনলাইন</span></span></span></button>
                <button class="card video-card" type="button" data-video="শিক্ষক প্রশিক্ষণ হাইলাইটস"
                    aria-label="শিক্ষক প্রশিক্ষণ হাইলাইটস ভিডিও দেখুন"><span class="video-thumb video-navy"></span><span
                        class="video-play" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                        </svg></span><span class="video-info"><span class="video-title">শিক্ষক প্রশিক্ষণ
                            হাইলাইটস</span><span class="video-meta"><span>১৫
                                মিনিট</span><span>ওয়ার্কশপ</span></span></span></button>
                <button class="card video-card" type="button" data-video="হিফজ শিক্ষার্থীর অগ্রগতি"
                    aria-label="হিফজ শিক্ষার্থীর অগ্রগতি ভিডিও দেখুন"><span class="video-thumb video-teal"></span><span
                        class="video-play" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                        </svg></span><span class="video-info"><span class="video-title">হিফজ শিক্ষার্থীর
                            অগ্রগতি</span><span class="video-meta"><span>৬
                                মিনিট</span><span>হিফজ</span></span></span></button>
                <button class="card video-card" type="button" data-video="সমাপনী অনুষ্ঠান"
                    aria-label="সমাপনী অনুষ্ঠান ভিডিও দেখুন"><span class="video-thumb video-amber"></span><span
                        class="video-play" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-play-circle"></use>
                        </svg></span><span class="video-info"><span class="video-title">সমাপনী
                            অনুষ্ঠান</span><span class="video-meta"><span>১৮
                                মিনিট</span><span>অনুষ্ঠান</span></span></span></button>
            </div>
        </section>

        <section id="publications-section" class="section-panel reveal">
            <div class="section-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-book"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">প্রকাশনা</h2>
                        <p class="section-subtitle">আমাদের তৈরি শিক্ষামূলক বই ও উপকরণ</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-3">
                <article class="card">
                    <div class="book-cover">
                        <div class="book-title-art">
                            <div class="book-arabic">تعليم القرآن</div>
                            <div class="book-bangla">কুরআন শিক্ষা</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">কুরআন শিক্ষা সহায়িকা</h3>
                        <p class="card-text">লেখক: মাওলানা আবদুল্লাহ</p>
                        <div class="book-footer"><span class="price">৳ ৩০০</span><button class="btn btn-soft"
                                type="button"
                                data-demo="কুরআন শিক্ষা সহায়িকা ক্রয়ের ডেমো অনুরোধ গ্রহণ করা হয়েছে।"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-book"></use>
                                </svg><span>কিনুন</span></button></div>
                    </div>
                </article>
                <article class="card">
                    <div class="book-cover navy">
                        <div class="book-title-art">
                            <div class="book-arabic">النحو والصرف</div>
                            <div class="book-bangla">নাহু-সরফ</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">আরবি ব্যাকরণ গাইড</h3>
                        <p class="card-text">লেখক: ড. মোহাম্মদ আলী</p>
                        <div class="book-footer"><span class="price">৳ ২৫০</span><button class="btn btn-soft"
                                type="button" data-demo="আরবি ব্যাকরণ গাইড ক্রয়ের ডেমো অনুরোধ গ্রহণ করা হয়েছে।"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-book"></use>
                                </svg><span>কিনুন</span></button></div>
                    </div>
                </article>
                <article class="card">
                    <div class="book-cover gold">
                        <div class="book-title-art">
                            <div class="book-arabic">التجويد</div>
                            <div class="book-bangla">তাজবিদ</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">তাজবিদ পূর্ণাঙ্গ গাইড</h3>
                        <p class="card-text">লেখক: হাফেজ করিম</p>
                        <div class="book-footer"><span class="price">৳ ৪০০</span><button class="btn btn-soft"
                                type="button"
                                data-demo="তাজবিদ পূর্ণাঙ্গ গাইড ক্রয়ের ডেমো অনুরোধ গ্রহণ করা হয়েছে।"><svg
                                    class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-book"></use>
                                </svg><span>কিনুন</span></button></div>
                    </div>
                </article>
            </div>
        </section>

        <section id="achievements-section" class="section-panel reveal">
            <div class="section-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-award"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">আমাদের অর্জন</h2>
                        <p class="section-subtitle">গত এক দশকের যাত্রায় আমাদের মাইলফলক</p>
                    </div>
                </div>
            </div>
            <div class="timeline timeline-modern">
                <article class="timeline-item"><span class="timeline-year">২০২৪</span>
                    <h3 class="timeline-title">২৫টি নতুন শাখা খোলা</h3>
                    <p>সারা বাংলাদেশে ২৫টি নতুন শাখা খোলা হয়েছে - ঢাকা, চট্টগ্রাম, সিলেট, রাজশাহীসহ বিভিন্ন বিভাগে।
                    </p>
                </article>
                <article class="timeline-item"><span class="timeline-year">২০২৩</span>
                    <h3 class="timeline-title">৫০০০+ শিক্ষার্থী</h3>
                    <p>আমাদের প্রতিষ্ঠানে ৫০০০-এর বেশি শিক্ষার্থী কুরআন ও আরবি শিক্ষা গ্রহণ করছেন।</p>
                </article>
                <article class="timeline-item"><span class="timeline-year">২০২২</span>
                    <h3 class="timeline-title">অনলাইন প্ল্যাটফর্ম চালু</h3>
                    <p>ডিজিটাল শিক্ষার প্রসারে অনলাইন কোর্স প্ল্যাটফর্ম চালু করা হয়েছে।</p>
                </article>
                <article class="timeline-item"><span class="timeline-year">২০২০</span>
                    <h3 class="timeline-title">২০০+ শিক্ষক প্রশিক্ষণ</h3>
                    <p>সারা দেশের ২০০-এর বেশি শিক্ষককে পেশাদার প্রশিক্ষণ প্রদান করা হয়েছে।</p>
                </article>
            </div>
        </section>

        <section id="reviews-section" class="section-panel reveal">
            <div class="section-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-message"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">শিক্ষার্থীদের মতামত</h2>
                        <p class="section-subtitle">আমাদের শিক্ষার্থীরা যা বলেন</p>
                    </div>
                </div>
            </div>
            <div class="grid grid-4">
                <article class="card review-card">
                    <div class="stars">★★★★★</div>
                    <p class="review-text">"এই প্রতিষ্ঠান থেকে তাজবিদ শিখেছি। শিক্ষকরা অত্যন্ত ধৈর্যশীল এবং পাঠদান
                        পদ্ধতি সত্যিই
                        কার্যকর। এখন আমি নিজেই ছেলেমেয়েদের শেখাচ্ছি।"</p>
                    <div class="review-person"><span class="review-avatar">ম</span><span><strong>মোহাম্মদ
                                রহমান</strong><br><span class="card-text">কুরআন তিলাওয়াত কোর্স</span></span>
                    </div>
                </article>
                <article class="card review-card">
                    <div class="stars">★★★★★</div>
                    <p class="review-text">"আরবি ব্যাকরণ শেখার পর কুরআনের অর্থ বুঝতে পারছি। এই অনুভূতি অতুলনীয়।
                        সত্যিই দারুণ
                        একটি প্রতিষ্ঠান।"</p>
                    <div class="review-person"><span class="review-avatar">ফা</span><span><strong>ফাতেমা
                                খাতুন</strong><br><span class="card-text">আরবি ব্যাকরণ কোর্স</span></span></div>
                </article>
                <article class="card review-card">
                    <div class="stars">★★★★★</div>
                    <p class="review-text">"আমার ছেলে এখানে হিফজ করছে। শিক্ষকরা খুবই যত্নশীল। পরিবেশটাও ইসলামি।
                        আলহামদুলিল্লাহ।"
                    </p>
                    <div class="review-person"><span class="review-avatar">আ</span><span><strong>আবদুল্লাহ আল
                                মামুন</strong><br><span class="card-text">অভিভাবক</span></span></div>
                </article>
                <article class="card review-card">
                    <div class="stars">★★★★★</div>
                    <p class="review-text">"শিক্ষক প্রশিক্ষণ কোর্সটি আমার ক্যারিয়ার বদলে দিয়েছে। এখন নিজেই
                        মাদ্রাসায় শিক্ষকতা
                        করছি।"</p>
                    <div class="review-person"><span class="review-avatar">আয়</span><span><strong>আয়েশা
                                বেগম</strong><br><span class="card-text">শিক্ষক প্রশিক্ষণ কোর্স</span></span>
                    </div>
                </article>
            </div>
        </section>

        <section id="representatives-section" class="section-panel representatives-panel reveal">
            <div class="section-head split-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-users"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">আমাদের প্রতিনিধি</h2>
                        <p class="section-subtitle">আপনার এলাকার প্রতিনিধি খুঁজে নিন</p>
                    </div>
                </div>
                <span id="rep-location-count" class="rep-count"><svg class="icon" aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin"></use>
                    </svg>৬৪টি জেলা</span>
            </div>
            <div class="rep-finder-shell">
                <div class="rep-filter-panel">
                    <div class="rep-filter-copy">
                        <span class="rep-chip"><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-user-check"></use>
                            </svg>প্রতিনিধি নেটওয়ার্ক</span>
                        <strong>এলাকা অনুযায়ী সঠিক প্রতিনিধির সঙ্গে যোগাযোগ করুন</strong>
                        <p>জেলা ও উপজেলা/থানা নির্বাচন করলে নিচের তালিকায় প্রাসঙ্গিক প্রতিনিধি দেখাবে।</p>
                    </div>
                    <div class="filter-bar rep-filter-bar">
                        <label class="rep-filter-field" for="district-filter">
                            <span class="filter-label">জেলা</span>
                            <span class="select-shell"><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin"></use>
                                </svg><select id="district-filter" class="select">
                                    <option value="">সব জেলা</option>
                                </select></span>
                        </label>
                        <label class="rep-filter-field" for="upozila-filter">
                            <span class="filter-label">উপজেলা/থানা</span>
                            <span class="select-shell"><svg class="icon" aria-hidden="true">
                                    <use href="/assets/frontend/images/icons-sprite.svg#icon-building"></use>
                                </svg><select id="upozila-filter" class="select" disabled>
                                    <option value="">আগে জেলা নির্বাচন করুন</option>
                                </select></span>
                        </label>
                    </div>
                </div>
                <div class="table-wrap rep-table-wrap">
                    <table class="rep-table" aria-label="প্রতিনিধি তালিকা">
                        <thead>
                            <tr>
                                <th>নাম</th>
                                <th>ফোন</th>
                                <th>পদবি</th>
                                <th>এলাকা</th>
                            </tr>
                        </thead>
                        <tbody id="representatives-table-body">
                            <tr data-district="dhaka" data-upozila="dhanmondi">
                                <td data-label="নাম"><span class="rep-name"><span
                                            class="rep-avatar">ম</span><span><strong>মোহাম্মদ
                                                আলী</strong><small>স্থানীয় প্রতিনিধি</small></span></span></td>
                                <td data-label="ফোন"><a class="rep-phone" href="tel:01712345678"><svg class="icon"
                                            aria-hidden="true">
                                            <use href="/assets/frontend/images/icons-sprite.svg#icon-phone"></use>
                                        </svg><span>০১৭১২৩৪৫৬৭৮</span></a></td>
                                <td data-label="পদবি"><span class="rep-badge">প্রতিনিধি</span></td>
                                <td data-label="এলাকা"><span class="rep-area"><svg class="icon"
                                            aria-hidden="true">
                                            <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin">
                                            </use>
                                        </svg>ধানমন্ডি, ঢাকা</span></td>
                            </tr>
                            <tr data-district="dhaka" data-upozila="gulshan">
                                <td data-label="নাম"><span class="rep-name"><span
                                            class="rep-avatar">আ</span><span><strong>আবদুল
                                                করিম</strong><small>ভর্তি সহায়তা</small></span></span></td>
                                <td data-label="ফোন"><a class="rep-phone" href="tel:01812345678"><svg class="icon"
                                            aria-hidden="true">
                                            <use href="/assets/frontend/images/icons-sprite.svg#icon-phone"></use>
                                        </svg><span>০১৮১২৩৪৫৬৭৮</span></a></td>
                                <td data-label="পদবি"><span class="rep-badge">প্রতিনিধি</span></td>
                                <td data-label="এলাকা"><span class="rep-area"><svg class="icon"
                                            aria-hidden="true">
                                            <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin">
                                            </use>
                                        </svg>গুলশান, ঢাকা</span></td>
                            </tr>
                            <tr data-district="chittagong" data-upozila="banani">
                                <td data-label="নাম"><span class="rep-name"><span
                                            class="rep-avatar">হা</span><span><strong>হাসান
                                                আহমেদ</strong><small>শাখা সমন্বয়কারী</small></span></span></td>
                                <td data-label="ফোন"><a class="rep-phone" href="tel:01912345678"><svg class="icon"
                                            aria-hidden="true">
                                            <use href="/assets/frontend/images/icons-sprite.svg#icon-phone"></use>
                                        </svg><span>০১৯১২৩৪৫৬৭৮</span></a></td>
                                <td data-label="পদবি"><span class="rep-badge gold">সিনিয়র প্রতিনিধি</span></td>
                                <td data-label="এলাকা"><span class="rep-area"><svg class="icon"
                                            aria-hidden="true">
                                            <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin">
                                            </use>
                                        </svg>বনানী, চট্টগ্রাম</span></td>
                            </tr>
                            <tr data-district="sylhet" data-upozila="amberkhana">
                                <td data-label="নাম"><span class="rep-name"><span
                                            class="rep-avatar">র</span><span><strong>রহমান
                                                মিয়া</strong><small>শিক্ষার্থী সহায়তা</small></span></span></td>
                                <td data-label="ফোন"><a class="rep-phone" href="tel:01512345678"><svg class="icon"
                                            aria-hidden="true">
                                            <use href="/assets/frontend/images/icons-sprite.svg#icon-phone"></use>
                                        </svg><span>০১৫১২৩৪৫৬৭৮</span></a></td>
                                <td data-label="পদবি"><span class="rep-badge">প্রতিনিধি</span></td>
                                <td data-label="এলাকা"><span class="rep-area"><svg class="icon"
                                            aria-hidden="true">
                                            <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin">
                                            </use>
                                        </svg>আম্বরখানা, সিলেট</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p id="rep-empty" class="message error" role="status">এই ফিল্টারে কোনো প্রতিনিধি পাওয়া
                    যায়নি।</p>
            </div>
        </section>

        <section id="prayer-time-section" class="section-panel prayer-panel reveal">
            <div class="section-head split-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">নামাজের সময়সূচি</h2>
                        <p class="section-subtitle">দৈনিক নামাজের সময় দ্রুত দেখুন</p>
                    </div>
                </div>
                <span class="prayer-location"><svg class="icon" aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin"></use>
                    </svg>ঢাকা, বাংলাদেশ</span>
            </div>
            <div class="prayer-grid">
                <div class="prayer-tile active"><span class="prayer-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                        </svg></span><span class="prayer-name">ফজর</span><strong>৫:১৫ AM</strong><small>ভোরের
                        নামাজ</small></div>
                <div class="prayer-tile"><span class="prayer-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                        </svg></span><span class="prayer-name">যোহর</span><strong>১২:৩০ PM</strong><small>দুপুরের
                        নামাজ</small>
                </div>
                <div class="prayer-tile"><span class="prayer-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                        </svg></span><span class="prayer-name">আসর</span><strong>৪:০০ PM</strong><small>বিকেলের
                        নামাজ</small>
                </div>
                <div class="prayer-tile"><span class="prayer-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                        </svg></span><span class="prayer-name">মাগরিব</span><strong>৬:২০
                        PM</strong><small>সন্ধ্যার
                        নামাজ</small>
                </div>
                <div class="prayer-tile"><span class="prayer-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                        </svg></span><span class="prayer-name">ইশা</span><strong>৭:৪৫ PM</strong><small>রাতের
                        নামাজ</small></div>
            </div>
        </section>

        <section id="quick-downloads-section" class="section-panel download-page reveal">
            <div class="section-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-download"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">ডাউনলোড</h2>
                        <p class="section-subtitle">প্রয়োজনীয় ফর্ম, সিলেবাস ও তথ্য এক জায়গা থেকে ডাউনলোড করুন
                        </p>
                    </div>
                </div>
            </div>
            <div class="downloads-grid"><a class="download-item" href="#"
                    data-demo="ভর্তি ফর্ম ডাউনলোডের ডেমো চালু হয়েছে।"><span class="download-icon"
                        aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-file-text"></use>
                        </svg></span><span><span class="download-title">ভর্তি ফর্ম</span><span class="download-size">PDF
                            • ৫০০
                            KB</span></span><span class="download-arrow" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-download"></use>
                        </svg></span></a><a class="download-item" href="#"
                    data-demo="কোর্স ব্রোশার ডাউনলোডের ডেমো চালু হয়েছে।"><span class="download-icon"
                        aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-file-text"></use>
                        </svg></span><span><span class="download-title">কোর্স ব্রোশার</span><span
                            class="download-size">PDF • ১.২
                            MB</span></span><span class="download-arrow" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-download"></use>
                        </svg></span></a><a class="download-item" href="#"
                    data-demo="ফি কাঠামো ২০২৪ ডাউনলোডের ডেমো চালু হয়েছে।"><span class="download-icon"
                        aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-file-text"></use>
                        </svg></span><span><span class="download-title">ফি কাঠামো ২০২৪</span><span
                            class="download-size">PDF • ২০০
                            KB</span></span><span class="download-arrow" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-download"></use>
                        </svg></span></a><a class="download-item" href="#"
                    data-demo="শিক্ষা ক্যালেন্ডার ডাউনলোডের ডেমো চালু হয়েছে।"><span class="download-icon"
                        aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-file-text"></use>
                        </svg></span><span><span class="download-title">শিক্ষা ক্যালেন্ডার</span><span
                            class="download-size">PDF •
                            ৩০০ KB</span></span><span class="download-arrow" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-download"></use>
                        </svg></span></a><a class="download-item" href="#"
                    data-demo="প্রতিষ্ঠানের নিয়মাবলি ডাউনলোডের ডেমো চালু হয়েছে।"><span class="download-icon"
                        aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-file-text"></use>
                        </svg></span><span><span class="download-title">প্রতিষ্ঠানের নিয়মাবলি</span><span
                            class="download-size">PDF • ৪০০ KB</span></span><span class="download-arrow"
                        aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-download"></use>
                        </svg></span></a><a class="download-item" href="#"
                    data-demo="সিলেবাস ডাউনলোডের ডেমো চালু হয়েছে।"><span class="download-icon"
                        aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-file-text"></use>
                        </svg></span><span><span class="download-title">সিলেবাস</span><span class="download-size">PDF •
                            ৮০০
                            KB</span></span><span class="download-arrow" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-download"></use>
                        </svg></span></a></div>
        </section>

        <section id="results-section" class="service-page results-page reveal" aria-labelledby="results-title">
            <div class="section-head split-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-award"></use>
                        </svg></div>
                    <div>
                        <h2 id="results-title" class="section-title">ফলাফল</h2>
                        <p class="section-subtitle">শিক্ষার্থী আইডি দিয়ে পরীক্ষার ফলাফল দেখুন</p>
                    </div>
                </div>
            </div>
            <div class="result-layout">
                <form id="result-lookup-form" class="form-panel result-lookup-card" novalidate>
                    <div class="form-grid">
                        <div class="field"><label for="result-student-id">শিক্ষার্থী আইডি <span
                                    class="required">*</span></label><input class="input" id="result-student-id"
                                type="text" required placeholder="যেমন: IE-2024-102"></div>
                        <div class="field"><label for="result-course">কোর্স <span
                                    class="required">*</span></label><select class="select" id="result-course"
                                required>
                                <option value="">কোর্স নির্বাচন করুন</option>
                                <option value="tajweed">তাজবিদ</option>
                                <option value="hifz">হিফজ</option>
                                <option value="arabic">আরবি ব্যাকরণ</option>
                                <option value="maktab">মক্তব</option>
                            </select></div>
                        <div class="field field-full"><label for="result-session">সেশন <span
                                    class="required">*</span></label><select class="select" id="result-session"
                                required>
                                <option value="">সেশন নির্বাচন করুন</option>
                                <option value="2024-final">২০২৪ বার্ষিক পরীক্ষা</option>
                                <option value="2024-mid">২০২৪ মধ্যবর্তী মূল্যায়ন</option>
                                <option value="2023-final">২০২৩ বার্ষিক পরীক্ষা</option>
                            </select></div>
                    </div>
                    <div id="result-message" class="message" role="status"></div>
                    <button class="btn btn-primary" type="submit" style="margin-top:16px"><svg class="icon"
                            aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-search"></use>
                        </svg><span>ফলাফল দেখুন</span></button>
                </form>
                <aside class="result-preview-card" aria-label="ডেমো ফলাফল">
                    <span class="badge">ডেমো ফলাফল</span>
                    <h3>তাজবিদ পরীক্ষা</h3>
                    <div class="result-score"><strong>৯২%</strong><span>মুমতাজ</span></div>
                    <ul class="result-list">
                        <li><span>তিলাওয়াত</span><strong>৪৬/৫০</strong></li>
                        <li><span>তাজবিদ নিয়ম</span><strong>২৮/৩০</strong></li>
                        <li><span>উপস্থিতি</span><strong>১৮/২০</strong></li>
                    </ul>
                    <button class="btn btn-soft" type="button" data-demo="ডেমো ফলাফল PDF ডাউনলোড প্রস্তুত।"><svg
                            class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-download"></use>
                        </svg><span>মার্কশিট ডাউনলোড</span></button>
                </aside>
            </div>
        </section>

        <section id="exam-section" class="service-page exam-page reveal" aria-labelledby="exam-title">
            <div class="section-head split-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-calendar"></use>
                        </svg></div>
                    <div>
                        <h2 id="exam-title" class="section-title">পরীক্ষা</h2>
                        <p class="section-subtitle">পরীক্ষার সময়সূচি, প্রবেশপত্র ও প্রস্তুতি নির্দেশনা</p>
                    </div>
                </div>
                <button class="btn btn-soft" type="button" data-demo="প্রবেশপত্র ডাউনলোডের ডেমো চালু হয়েছে।"><svg
                        class="icon" aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-file-text"></use>
                    </svg><span>প্রবেশপত্র</span></button>
            </div>
            <div class="exam-grid">
                <article class="card exam-card active"><span
                        class="exam-date"><strong>১৫</strong><small>মার্চ</small></span>
                    <div>
                        <h3>তাজবিদ ব্যবহারিক পরীক্ষা</h3>
                        <p>সকাল ৯টা • ঢাকা শাখা • মোট নম্বর ৫০</p>
                    </div><button class="btn btn-soft" type="button"
                        data-demo="তাজবিদ পরীক্ষার রুটিন ডেমো দেখা হচ্ছে।">রুটিন</button>
                </article>
                <article class="card exam-card"><span class="exam-date"><strong>২২</strong><small>মার্চ</small></span>
                    <div>
                        <h3>আরবি ব্যাকরণ লিখিত পরীক্ষা</h3>
                        <p>দুপুর ২টা • সব শাখা • মোট নম্বর ১০০</p>
                    </div><button class="btn btn-soft" type="button"
                        data-demo="আরবি পরীক্ষার নির্দেশনা ডেমো দেখা হচ্ছে।">নির্দেশনা</button>
                </article>
                <article class="card exam-card"><span class="exam-date"><strong>২৯</strong><small>মার্চ</small></span>
                    <div>
                        <h3>মক্তব মূল্যায়ন</h3>
                        <p>সকাল / বিকেল ব্যাচ • অভিভাবক আপডেটসহ</p>
                    </div><button class="btn btn-soft" type="button"
                        data-demo="মক্তব মূল্যায়ন সিলেবাস ডেমো দেখা হচ্ছে।">সিলেবাস</button>
                </article>
            </div>
            <div class="exam-note"><svg class="icon" aria-hidden="true">
                    <use href="/assets/frontend/images/icons-sprite.svg#icon-badge-check"></use>
                </svg><span>পরীক্ষার আগে বকেয়া ফি, উপস্থিতি ও প্রবেশপত্র যাচাই করে নিন।</span></div>
        </section>

        <section id="calendar-section" class="section-panel calendar-page reveal" aria-labelledby="calendar-title">
            <div class="section-head split-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-calendar"></use>
                        </svg></div>
                    <div>
                        <h2 id="calendar-title" class="section-title">শিক্ষা ক্যালেন্ডার</h2>
                        <p class="section-subtitle">ক্লাস, ভর্তি, পরীক্ষা ও বিশেষ আয়োজনের মাসভিত্তিক পরিকল্পনা</p>
                    </div>
                </div>
                <button class="btn btn-soft" type="button"
                    data-demo="শিক্ষা ক্যালেন্ডার ডাউনলোডের ডেমো চালু হয়েছে।"><svg class="icon" aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-download"></use>
                    </svg><span>ডাউনলোড</span></button>
            </div>
            <div class="calendar-page-layout">
                <article class="calendar-main-card" aria-label="মাসিক শিক্ষা ক্যালেন্ডার">
                    <div class="calendar-main-top">
                        <div>
                            <span class="badge">চলতি মাস</span>
                            <h3>মাসিক ক্লাস ও ইভেন্ট পরিকল্পনা</h3>
                        </div>
                        <div class="calendar-summary">
                            <span><strong>৪</strong><small>ইভেন্ট</small></span>
                            <span><strong>৩</strong><small>পরীক্ষা/মূল্যায়ন</small></span>
                            <span><strong>২</strong><small>নতুন ব্যাচ</small></span>
                        </div>
                    </div>
                    <div id="main-calendar-container" class="calendar calendar-large js-calendar"
                        aria-label="শিক্ষা ক্যালেন্ডার"></div>
                    <div class="calendar-legend" aria-label="ক্যালেন্ডার লেজেন্ড">
                        <span><i class="legend-dot today-dot"></i>আজ</span>
                        <span><i class="legend-dot event-dot"></i>গুরুত্বপূর্ণ ইভেন্ট</span>
                        <span><i class="legend-dot class-dot"></i>ক্লাস কার্যক্রম</span>
                    </div>
                </article>
                <aside class="calendar-events-card" aria-label="এই মাসের গুরুত্বপূর্ণ তারিখ">
                    <span class="badge gold">গুরুত্বপূর্ণ তারিখ</span>
                    <h3>এই মাসের গুরুত্বপূর্ণ তারিখ</h3>
                    <div class="calendar-event-list">
                        <article><time>১৫ মার্চ</time>
                            <div>
                                <h4>তাজবিদ ব্যবহারিক পরীক্ষা</h4>
                                <p>ঢাকা শাখা • সকাল ৯টা</p>
                            </div>
                        </article>
                        <article><time>২০ মার্চ</time>
                            <div>
                                <h4>ইসলামি সেমিনার</h4>
                                <p>চট্টগ্রাম শাখা • বিকেল ৪টা</p>
                            </div>
                        </article>
                        <article><time>২২ মার্চ</time>
                            <div>
                                <h4>আরবি ব্যাকরণ লিখিত পরীক্ষা</h4>
                                <p>সব শাখা • দুপুর ২টা</p>
                            </div>
                        </article>
                        <article><time>১ রমজান</time>
                            <div>
                                <h4>হিফজ নতুন ব্যাচ শুরু</h4>
                                <p>ন্যূনতম ৩০ জন শিক্ষার্থী নিশ্চিত হলে ব্যাচ চালু</p>
                            </div>
                        </article>
                    </div>
                    <div class="calendar-event-actions">
                        <a class="btn btn-gold" href="#exam-section"><svg class="icon" aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-calendar"></use>
                            </svg><span>পরীক্ষা দেখুন</span></a>
                        <a class="btn btn-outline" href="#admission-form-section"><svg class="icon"
                                aria-hidden="true">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-clipboard-check"></use>
                            </svg><span>ভর্তি আবেদন</span></a>
                    </div>
                </aside>
            </div>
        </section>

        <section id="student-login-section" class="student-access-section reveal"
            aria-labelledby="student-login-title">
            <div class="student-login-copy">
                <span class="student-access-icon" aria-hidden="true"><svg class="icon">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-user-check"></use>
                    </svg></span>
                <div>
                    <h2 id="student-login-title">শিক্ষার্থী লগইন</h2>
                    <p>ভর্তি তথ্য, ফলাফল, ডাউনলোড ও ক্লাস আপডেট দেখতে শিক্ষার্থী আইডি দিয়ে লগইন করুন।</p>
                </div>
            </div>
            <form id="sidebar-login-form" class="student-login-card" novalidate>
                <label class="visually-hidden" for="student-id">আইডি বা ব্যবহারকারীর নাম</label>
                <input class="input" id="student-id" type="text" required placeholder="আইডি / ব্যবহারকারীর নাম">
                <label class="visually-hidden" for="student-password">পাসওয়ার্ড</label>
                <input class="input" id="student-password" type="password" required placeholder="পাসওয়ার্ড">
                <button class="btn btn-primary" type="submit"><svg class="icon" aria-hidden="true">
                        <use href="/assets/frontend/images/icons-sprite.svg#icon-user-check"></use>
                    </svg><span>লগইন করুন</span></button>
                <a href="#" data-demo="পাসওয়ার্ড রিসেট ডেমো লিংক চালু হয়েছে।">পাসওয়ার্ড ভুলে গেছেন?</a>
            </form>
        </section>

        <section id="donation-section" class="donation-band donation-premium reveal" aria-labelledby="donation-title">
            <div class="donation-copy">
                <h2 id="donation-title">একটি দান, একটি আলোকিত ভবিষ্যৎ</h2>
                <p>আপনার আন্তরিক সহায়তা একজন শিক্ষার্থীর কুরআন শিক্ষা, বই, ফি ও নিয়মিত পাঠচর্চার পথ সহজ করে দিতে
                    পারে। ছোট
                    একটি দানও কারও জীবনে বড় পরিবর্তন আনতে পারে।</p>
                <div class="donation-impact-list" aria-label="দান ব্যবহারের ক্ষেত্র">
                    <span><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-book-open"></use>
                        </svg>কুরআন শিক্ষা সহায়তা</span>
                    <span><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-graduation"></use>
                        </svg>দরিদ্র শিক্ষার্থীর ফি</span>
                    <span><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-heart"></use>
                        </svg>বই ও শিক্ষা উপকরণ</span>
                </div>
                <div class="donation-actions">
                    <button class="btn btn-gold donation-primary-cta" type="button"
                        data-demo="দান করার ডেমো অনুরোধ গ্রহণ করা হয়েছে।"><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-heart"></use>
                        </svg><span>এখনই দান করুন</span></button>
                    <a class="btn btn-outline" href="#contact-us-section"><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-phone"></use>
                        </svg><span>যোগাযোগ করুন</span></a>
                </div>
            </div>
            <aside class="donation-story" aria-label="দান সহায়তার প্রভাব">
                <div class="donation-photo">
                    <span class="donation-heart" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-heart"></use>
                        </svg></span>
                    <div class="donation-photo-caption"><span>আপনার সহায়তায়</span><strong>আরও শিক্ষার্থী আলো
                            পাবে</strong>
                    </div>
                </div>
                <div class="donation-mini-grid">
                    <span><strong>৳ ৫০০</strong><small>বই সহায়তা</small></span>
                    <span><strong>৳ ১০০০</strong><small>মাসিক শিক্ষা সহায়তা</small></span>
                </div>
            </aside>
        </section>

        <section id="contact-us-section" class="section-panel reveal">
            <div class="section-head">
                <div class="section-title-group">
                    <div class="section-icon" aria-hidden="true"><svg class="icon">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin"></use>
                        </svg></div>
                    <div>
                        <h2 class="section-title">যোগাযোগ করুন</h2>
                        <p class="section-subtitle">আমরা সবসময় আপনার সেবায় প্রস্তুত</p>
                    </div>
                </div>
            </div>
            <div class="contact-grid contact-premium">
                <div class="map-card" role="img" aria-label="ঢাকা অফিস লোকেশন ম্যাপের ডেমো দৃশ্য">
                    <div class="map-glow" aria-hidden="true"></div>
                    <div class="map-pin"><svg class="icon" aria-hidden="true">
                            <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin"></use>
                        </svg><span>ঢাকা</span></div>
                    <div class="map-caption">
                        <span>প্রধান কার্যালয়</span>
                        <strong>ঢাকা-১০০০, বাংলাদেশ</strong>
                    </div>
                </div>
                <ul class="contact-list">
                    <li class="contact-item">
                        <span class="contact-icon" aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-map-pin"></use>
                            </svg></span>
                        <span class="contact-content"><span class="contact-label">ঠিকানা</span><span>১২৩ ইসলামিক
                                সেন্টার
                                রোড<br>ঢাকা-১০০০, বাংলাদেশ</span></span>
                    </li>
                    <li class="contact-item">
                        <span class="contact-icon" aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-phone"></use>
                            </svg></span>
                        <span class="contact-content"><span class="contact-label">ফোন</span><a
                                href="tel:+8801712345678">০১৭১২৩৪৫৬৭৮</a></span>
                    </li>
                    <li class="contact-item">
                        <span class="contact-icon" aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-mail"></use>
                            </svg></span>
                        <span class="contact-content"><span class="contact-label">ইমেইল</span><a
                                href="mailto:info@islamiceducation.com">info@islamiceducation.com</a></span>
                    </li>
                    <li class="contact-item">
                        <span class="contact-icon" aria-hidden="true"><svg class="icon">
                                <use href="/assets/frontend/images/icons-sprite.svg#icon-clock"></use>
                            </svg></span>
                        <span class="contact-content"><span class="contact-label">অফিস
                                সময়</span><span>শনি-বৃহস্পতিবার: সকাল ৮টা
                                - রাত ৮টা</span></span>
                    </li>
                </ul>
            </div>
        </section>
    </div>
@endsection
