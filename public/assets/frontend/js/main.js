(function () {
  "use strict";

  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));

  const bnDigits = ["০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯"];
  const toBn = (value) => String(value).replace(/\d/g, (digit) => bnDigits[Number(digit)]);
  const ICON_SPRITE = "/assets/frontend/images/icons-sprite.svg";

  const toast = $("#toast");
  let toastTimer = null;

  function showToast(message) {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add("show");
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove("show"), 3200);
  }

  function setMessage(element, type, text) {
    if (!element) return;
    element.className = `message show ${type}`;
    element.textContent = text;
  }

  function clearFieldState(form) {
    $$(".field-error", form).forEach((field) => field.classList.remove("field-error"));
  }

  function markInvalid(input) {
    const field = input.closest(".field") || input.parentElement;
    if (field) field.classList.add("field-error");
  }

  function validEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  function validPhone(value) {
    return /^(۰۱|01)[۰-۹0-9]{9}$/.test(value) || /^(০۱|01)[۰-۹0-9]{9}$/.test(value) || /^(۰۱|01)[۰-۹0-9]{9}$/.test(value);
  }

  function validLocalPhone(value) {
    const normalized = Array.from(value).map((char) => {
      const code = char.charCodeAt(0);
      return code >= 0x09E6 && code <= 0x09EF ? String(code - 0x09E6) : char;
    }).join("");
    return /^01[0-9]{9}$/.test(normalized);
  }

  function initClock() {
    const targets = [$("#current-time-display")].filter(Boolean);
    if (!targets.length) return;

    const tick = () => {
      const now = new Date();
      const time = now.toLocaleTimeString("bn-BD", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: true
      });
      targets.forEach((target) => {
        target.textContent = time;
      });
    };

    tick();
    setInterval(tick, 30000);
  }

  function initMobileMenu() {
    const toggle = $("#mobile-menu-toggle");
    const menu = $("#mobile-menu");
    const courseToggle = $("#mobile-course-toggle");
    const courseMenu = $("#mobile-course-menu");

    if (toggle && menu) {
      toggle.addEventListener("click", () => {
        const open = menu.classList.toggle("open");
        toggle.setAttribute("aria-expanded", String(open));
      });
    }

    if (courseToggle && courseMenu) {
      courseToggle.addEventListener("click", () => {
        const open = courseMenu.classList.toggle("open");
        courseToggle.setAttribute("aria-expanded", String(open));
      });
    }

    $$("#mobile-menu a").forEach((link) => {
      link.addEventListener("click", () => {
        menu?.classList.remove("open");
        toggle?.setAttribute("aria-expanded", "false");
        courseMenu?.classList.remove("open");
        courseToggle?.setAttribute("aria-expanded", "false");
      });
    });
  }

  function initDropdownA11y() {
    $$(".dropdown").forEach((dropdown) => {
      const button = $("button.nav-link", dropdown);
      if (!button) return;
      dropdown.addEventListener("mouseenter", () => button.setAttribute("aria-expanded", "true"));
      dropdown.addEventListener("mouseleave", () => {
        if (!dropdown.classList.contains("open")) button.setAttribute("aria-expanded", "false");
      });
      dropdown.addEventListener("focusin", () => button.setAttribute("aria-expanded", "true"));
      dropdown.addEventListener("focusout", () => {
        if (!dropdown.classList.contains("open")) button.setAttribute("aria-expanded", "false");
      });
      button.addEventListener("click", () => {
        const open = dropdown.classList.toggle("open");
        button.setAttribute("aria-expanded", String(open));
      });
    });

    document.addEventListener("click", (event) => {
      $$(".dropdown.open").forEach((dropdown) => {
        if (dropdown.contains(event.target)) return;
        dropdown.classList.remove("open");
        $("button.nav-link", dropdown)?.setAttribute("aria-expanded", "false");
      });
    });
  }

  function initActiveNav() {
    const links = $$(".nav-link[href^='#']");
    const sections = links
      .map((link) => $(link.getAttribute("href")))
      .filter(Boolean);

    if (!sections.length) return;

    const observer = new IntersectionObserver((entries) => {
      const visible = entries
        .filter((entry) => entry.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

      if (!visible) return;
      const id = `#${visible.target.id}`;
      links.forEach((link) => {
        link.classList.toggle("active", link.getAttribute("href") === id);
      });
    }, {
      rootMargin: "-35% 0px -55% 0px",
      threshold: [0.08, 0.16, 0.32]
    });

    sections.forEach((section) => observer.observe(section));
  }

  function initRevealAndCounters() {
    const counters = new WeakSet();

    function animateCounter(element) {
      if (counters.has(element)) return;
      counters.add(element);
      const target = Number(element.dataset.count || 0);
      const duration = 1150;
      const start = performance.now();

      function frame(time) {
        const progress = Math.min((time - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const value = Math.round(target * eased);
        element.textContent = `${toBn(value)}+`;
        if (progress < 1) requestAnimationFrame(frame);
      }

      requestAnimationFrame(frame);
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add("visible");
        $$("[data-count]", entry.target).forEach(animateCounter);
      });
    }, {
      threshold: 0.16,
      rootMargin: "0px 0px -60px 0px"
    });

    $$(".reveal").forEach((el) => observer.observe(el));
    $$(".stat-card").forEach((el) => observer.observe(el));
  }

  function initBranchDistrictSelects() {
    const locations = Array.isArray(window.BD_DISTRICT_UPAZILAS) ? window.BD_DISTRICT_UPAZILAS : [];
    if (!locations.length) return;

    const districtNames = [...new Set(locations.map((item) => item.name).filter(Boolean))];

    const populateSelect = (selector, extras = []) => {
      const select = $(selector);
      if (!select) return;

      const placeholder = select.querySelector("option")?.textContent || "শাখা নির্বাচন করুন";
      select.replaceChildren(new Option(placeholder, ""));

      districtNames.forEach((districtName) => {
        select.add(new Option(districtName, districtName));
      });

      extras.forEach((item) => {
        select.add(new Option(item.label, item.value));
      });
    };

    populateSelect("#branch");
    populateSelect("#muallim-branch", [{ label: "অনলাইন", value: "online" }]);
  }

  function initAdmissionForm() {
    const form = $("#admission-form");
    const message = $("#form-message");
    if (!form) return;

    form.addEventListener("submit", (event) => {
      event.preventDefault();
      clearFieldState(form);

      const required = ["name", "phone", "branch", "course"].map((id) => $(`#${id}`));
      let ok = true;

      required.forEach((input) => {
        if (!input || !input.value.trim()) {
          ok = false;
          if (input) markInvalid(input);
        }
      });

      const phone = $("#phone");
      if (phone && phone.value.trim() && !/^(০১|01)[০-৯0-9]{9}$/.test(phone.value.trim())) {
        ok = false;
        markInvalid(phone);
      }

      const email = $("#email");
      if (email && email.value.trim() && !validEmail(email.value.trim())) {
        ok = false;
        markInvalid(email);
      }

      if (!ok) {
        setMessage(message, "error", "অনুগ্রহ করে প্রয়োজনীয় তথ্য সঠিকভাবে পূরণ করুন।");
        return;
      }

      setMessage(message, "success", "ধন্যবাদ। আপনার আবেদন গ্রহণ করা হয়েছে। আমাদের প্রতিনিধি শীঘ্রই যোগাযোগ করবেন।");
      form.reset();
    });
  }

  function initMuallimForm() {
    const form = $("#muallim-form");
    const message = $("#muallim-message");
    if (!form) return;

    form.addEventListener("submit", (event) => {
      event.preventDefault();
      clearFieldState(form);

      const required = [
        "muallim-name",
        "muallim-phone",
        "muallim-district",
        "muallim-qualification",
        "muallim-experience",
        "muallim-speciality",
        "muallim-branch"
      ].map((id) => $(`#${id}`));
      let ok = true;

      required.forEach((input) => {
        if (!input || !input.value.trim()) {
          ok = false;
          if (input) markInvalid(input);
        }
      });

      const phone = $("#muallim-phone");
      if (phone && phone.value.trim() && !validLocalPhone(phone.value.trim())) {
        ok = false;
        markInvalid(phone);
      }

      const email = $("#muallim-email");
      if (email && email.value.trim() && !validEmail(email.value.trim())) {
        ok = false;
        markInvalid(email);
      }

      if (!ok) {
        setMessage(message, "error", "অনুগ্রহ করে মুয়াল্লিম আবেদন ফর্মের প্রয়োজনীয় তথ্য সঠিকভাবে পূরণ করুন।");
        return;
      }

      setMessage(message, "success", "ধন্যবাদ। আপনার মুয়াল্লিম আবেদন গ্রহণ করা হয়েছে। যাচাই শেষে সাক্ষাৎকারের সময় জানানো হবে।");
      form.reset();
    });
  }

  function initResultLookup() {
    const form = $("#result-lookup-form");
    const message = $("#result-message");
    if (!form) return;

    form.addEventListener("submit", (event) => {
      event.preventDefault();
      clearFieldState(form);

      const required = ["result-student-id", "result-course", "result-session"].map((id) => $(`#${id}`));
      let ok = true;

      required.forEach((input) => {
        if (!input || !input.value.trim()) {
          ok = false;
          if (input) markInvalid(input);
        }
      });

      if (!ok) {
        setMessage(message, "error", "ফলাফল দেখতে শিক্ষার্থী আইডি, কোর্স ও সেশন নির্বাচন করুন।");
        return;
      }

      setMessage(message, "success", "ডেমো ফলাফল পাওয়া গেছে। ডান পাশে ফলাফলের সারাংশ দেখুন।");
    });
  }

  function initCourseSelectionCtas() {
    const courseSelect = $("#course");
    if (!courseSelect) return;

    $$("[data-course-select]").forEach((cta) => {
      cta.addEventListener("click", () => {
        const value = cta.dataset.courseSelect;
        if (!value) return;
        courseSelect.value = value;
      });
    });
  }

  function initSimpleForms() {
    const setups = [
      {
        form: "#subscribe-form",
        input: "#subscribe-email",
        message: "#subscribe-message",
        success: "সাবস্ক্রিপশন সফল হয়েছে। নতুন আপডেট আপনার ইমেইলে পাঠানো হবে।",
        error: "সঠিক ইমেইল ঠিকানা লিখুন।"
      }
    ];

    setups.forEach((setup) => {
      const form = $(setup.form);
      const input = $(setup.input);
      const message = $(setup.message);
      if (!form || !input) return;

      form.addEventListener("submit", (event) => {
        event.preventDefault();
        const value = input.value.trim();
        const valid = input.type === "email" ? validEmail(value) : value.length > 2;
        if (!valid) {
          setMessage(message, "error", setup.error);
          return;
        }
        setMessage(message, "success", setup.success);
        form.reset();
      });
    });

    const footerForm = $("#footer-newsletter-form");
    const footerEmail = $("#footer-email");
    if (footerForm && footerEmail) {
      footerForm.addEventListener("submit", (event) => {
        event.preventDefault();
        if (!validEmail(footerEmail.value.trim())) {
          showToast("সঠিক ইমেইল ঠিকানা লিখুন।");
          return;
        }
        footerForm.reset();
        showToast("নিউজলেটার সাবস্ক্রিপশন সফল হয়েছে।");
      });
    }

    const loginForm = $("#sidebar-login-form");
    if (loginForm) {
      loginForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const missing = $$("input", loginForm).some((input) => !input.value.trim());
        if (missing) {
          showToast("লগইন করতে আইডি ও পাসওয়ার্ড লিখুন।");
          return;
        }
        loginForm.reset();
        showToast("ডেমো লগইন সম্পন্ন হয়েছে।");
      });
    }
  }

  function initRepresentativeFilter() {
    const district = $("#district-filter");
    const upozila = $("#upozila-filter");
    const tableBody = $("#representatives-table-body");
    const empty = $("#rep-empty");
    const count = $("#rep-location-count");
    const locations = Array.isArray(window.BD_DISTRICT_UPAZILAS) ? window.BD_DISTRICT_UPAZILAS : [];
    if (!district || !upozila || !tableBody || !locations.length) return;

    const locationAddons = {
      "ঢাকা": [
        "ঢাকা উত্তর সিটি কর্পোরেশন", "ঢাকা দক্ষিণ সিটি কর্পোরেশন",
        "আদাবর থানা", "এয়ারপোর্ট থানা", "বাড্ডা থানা", "বনানী থানা", "বংশাল থানা", "ভাষানটেক থানা", "ক্যান্টনমেন্ট থানা", "চকবাজার থানা", "দারুস সালাম থানা", "দক্ষিণখান থানা", "ডেমরা থানা", "ধানমন্ডি থানা", "গেণ্ডারিয়া থানা", "গুলশান থানা", "হাজারীবাগ থানা", "যাত্রাবাড়ী থানা", "কদমতলী থানা", "কাফরুল থানা", "কলাবাগান থানা", "কামরাঙ্গীরচর থানা", "খিলগাঁও থানা", "খিলক্ষেত থানা", "কোতোয়ালি থানা", "লালবাগ থানা", "মিরপুর মডেল থানা", "মোহাম্মদপুর থানা", "মতিঝিল থানা", "মুগদা থানা", "নিউ মার্কেট থানা", "পল্লবী থানা", "পল্টন মডেল থানা", "রমনা মডেল থানা", "রামপুরা থানা", "রূপনগর থানা", "সবুজবাগ থানা", "শাহ আলী থানা", "শাহবাগ থানা", "শেরেবাংলা নগর থানা", "শ্যামপুর থানা", "সূত্রাপুর থানা", "শাহজাহানপুর থানা", "তেজগাঁও থানা", "তেজগাঁও শিল্পাঞ্চল থানা", "তুরাগ থানা", "উত্তরা মডেল থানা", "উত্তরখান থানা", "উত্তরা পশ্চিম থানা", "ভাটারা থানা", "ওয়ারী থানা"
      ],
      "চট্টগ্রাম": [
        "চট্টগ্রাম সিটি কর্পোরেশন",
        "কোতোয়ালি থানা", "ডবলমুরিং থানা", "পাহাড়তলী থানা", "পাঁচলাইশ থানা", "বায়েজিদ বোস্তামী থানা", "চান্দগাঁও থানা", "খুলশী থানা", "হালিশহর থানা", "বন্দর থানা", "পতেঙ্গা থানা", "ইপিজেড থানা", "আকবরশাহ থানা", "বাকলিয়া থানা", "চকবাজার থানা", "কর্ণফুলী থানা", "সদরঘাট থানা"
      ],
      "খুলনা": [
        "খুলনা সিটি কর্পোরেশন",
        "খুলনা সদর থানা", "সোনাডাঙ্গা মডেল থানা", "খালিশপুর থানা", "দৌলতপুর থানা", "খানজাহান আলী থানা", "হরিণটানা থানা", "লবণচরা থানা", "আড়ংঘাটা থানা"
      ],
      "রাজশাহী": [
        "রাজশাহী সিটি কর্পোরেশন",
        "বোয়ালিয়া মডেল থানা", "রাজপাড়া থানা", "মতিহার থানা", "শাহ মখদুম থানা", "চন্দ্রিমা থানা", "কাশিয়াডাঙ্গা থানা", "কাটাখালী থানা", "কর্ণহার থানা", "এয়ারপোর্ট থানা", "পবা থানা", "বেলপুকুর থানা"
      ],
      "সিলেট": [
        "সিলেট সিটি কর্পোরেশন",
        "কোতোয়ালি মডেল থানা", "জালালাবাদ থানা", "শাহপরান থানা", "মোগলাবাজার থানা", "দক্ষিণ সুরমা থানা", "এয়ারপোর্ট থানা"
      ],
      "বরিশাল": [
        "বরিশাল সিটি কর্পোরেশন",
        "কোতোয়ালি মডেল থানা", "বন্দর থানা", "কাউনিয়া থানা", "এয়ারপোর্ট থানা"
      ],
      "নারায়ণগঞ্জ": [
        "নারায়ণগঞ্জ সিটি কর্পোরেশন",
        "নারায়ণগঞ্জ সদর মডেল থানা", "ফতুল্লা মডেল থানা", "সিদ্ধিরগঞ্জ থানা", "বন্দর থানা", "সোনারগাঁও থানা", "রূপগঞ্জ থানা"
      ],
      "কুমিল্লা": [
        "কুমিল্লা সিটি কর্পোরেশন",
        "কুমিল্লা কোতোয়ালি মডেল থানা", "সদর দক্ষিণ মডেল থানা", "চান্দিনা থানা", "দাউদকান্দি থানা", "লাকসাম থানা", "চৌদ্দগ্রাম থানা", "দেবিদ্বার থানা", "মুরাদনগর থানা"
      ],
      "রংপুর": [
        "রংপুর সিটি কর্পোরেশন",
        "কোতোয়ালি থানা", "তাজহাট থানা", "পরশুরাম থানা", "হাজীরহাট থানা", "হারাগাছ থানা", "মাহিগঞ্জ থানা"
      ],
      "গাজীপুর": [
        "গাজীপুর সিটি কর্পোরেশন",
        "গাজীপুর সদর থানা", "টঙ্গী পূর্ব থানা", "টঙ্গী পশ্চিম থানা", "বাসন থানা", "কোনাবাড়ী থানা", "কাশিমপুর থানা", "গাছা থানা", "পূবাইল থানা"
      ],
      "বগুড়া": [
        "বগুড়া সিটি কর্পোরেশন",
        "বগুড়া সদর থানা", "শাজাহানপুর থানা", "শেরপুর থানা", "গাবতলী থানা"
      ],
      "ময়মনসিংহ": [
        "ময়মনসিংহ সিটি কর্পোরেশন",
        "কোতোয়ালি মডেল থানা", "তারাকান্দা থানা", "ফুলবাড়িয়া থানা", "মুক্তাগাছা থানা", "ত্রিশাল থানা"
      ]
    };
    const normalizeLocationName = (value) => String(value).replace(/\s+/g, " ").trim();
    const enrichLocation = (location) => {
      const merged = [...(locationAddons[location.name] || []), ...location.upazilas].map(normalizeLocationName);
      return { ...location, upazilas: Array.from(new Set(merged)) };
    };
    const enrichedLocations = locations.map(enrichLocation);
    const demoNames = ["মোহাম্মদ আলী", "আবদুল করিম", "হাসান আহমেদ", "রহমান মিয়া", "সাইফুল ইসলাম", "মাহমুদ হাসান", "নুরুল আমিন", "মিজানুর রহমান", "ফারুক হোসেন", "ইমরান হাবিব"];
    const demoRoles = ["স্থানীয় প্রতিনিধি", "ভর্তি সহায়তা", "শাখা সমন্বয়কারী", "শিক্ষার্থী সহায়তা", "কোর্স পরামর্শক"];
    const featuredDistricts = ["ঢাকা", "চট্টগ্রাম", "সিলেট", "রাজশাহী", "খুলনা", "বরিশাল"];
    const visibleRowsLimit = 3;
    const selectedLocation = () => enrichedLocations.find((item) => item.name === district.value);
    const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      "\"": "&quot;",
      "'": "&#039;"
    }[char]));

    const makePhone = (index) => {
      const raw = `01${7 + (index % 3)}${String(12345678 + index * 137).slice(0, 8)}`;
      return { raw, display: toBn(raw) };
    };

    const countLabel = (text) => {
      if (!count) return;
      count.innerHTML = `<svg class="icon" aria-hidden="true"><use href="${ICON_SPRITE}#icon-map-pin"></use></svg>${text}`;
    };

    const rowTemplate = (item, index) => {
      const name = demoNames[index % demoNames.length];
      const role = demoRoles[index % demoRoles.length];
      const senior = index % 5 === 2;
      const phone = makePhone(index + item.district.length + item.upazila.length);
      const avatarIcon = senior ? `${ICON_SPRITE}#icon-award` : `${ICON_SPRITE}#icon-user-check`;
      return `<tr data-district="${escapeHtml(item.district)}" data-upozila="${escapeHtml(item.upazila)}">
        <td data-label="নাম"><span class="rep-name"><span class="rep-avatar${senior ? " senior" : ""}" aria-hidden="true"><svg class="icon"><use href="${avatarIcon}"></use></svg></span><span><strong>${escapeHtml(name)}</strong><small>${escapeHtml(role)}</small></span></span></td>
        <td data-label="ফোন"><a class="rep-phone" href="tel:${phone.raw}"><svg class="icon" aria-hidden="true"><use href="${ICON_SPRITE}#icon-phone"></use></svg><span>${phone.display}</span></a></td>
        <td data-label="পদবি"><span class="rep-badge${senior ? " gold" : ""}">${senior ? "সিনিয়র প্রতিনিধি" : "প্রতিনিধি"}</span></td>
        <td data-label="এলাকা"><span class="rep-area"><svg class="icon" aria-hidden="true"><use href="${ICON_SPRITE}#icon-map-pin"></use></svg>${escapeHtml(item.upazila)}, ${escapeHtml(item.district)}</span></td>
      </tr>`;
    };

    const populateDistricts = () => {
      district.innerHTML = `<option value="">সব জেলা</option>${enrichedLocations.map((item) => `<option value="${escapeHtml(item.name)}">${escapeHtml(item.name)}</option>`).join("")}`;
    };

    const populateUpozilas = () => {
      const location = selectedLocation();
      if (!location) {
        upozila.disabled = true;
        upozila.innerHTML = `<option value="">আগে জেলা নির্বাচন করুন</option>`;
        return;
      }
      upozila.disabled = false;
      upozila.innerHTML = `<option value="">সব উপজেলা/থানা</option>${location.upazilas.map((name) => `<option value="${escapeHtml(name)}">${escapeHtml(name)}</option>`).join("")}`;
    };

    const renderRows = () => {
      const location = selectedLocation();
      let rows = [];

      if (location) {
        const upazilas = upozila.value ? [upozila.value] : location.upazilas;
        rows = upazilas.map((name) => ({ district: location.name, upazila: name }));
        countLabel(upozila.value ? "১টি উপজেলা/থানা নির্বাচিত" : `${location.name}: ${toBn(rows.length)}টি উপজেলা/থানা`);
      } else {
        rows = featuredDistricts
          .map((name) => enrichedLocations.find((item) => item.name === name))
          .filter(Boolean)
          .map((item) => ({ district: item.name, upazila: item.upazilas[0] }));
        countLabel(`${toBn(enrichedLocations.length)}টি জেলা • ${toBn(enrichedLocations.reduce((sum, item) => sum + item.upazilas.length, 0))} উপজেলা/থানা`);
      }

      const visibleRows = rows.slice(0, visibleRowsLimit);
      tableBody.innerHTML = visibleRows.map(rowTemplate).join("");
      empty?.classList.toggle("show", visibleRows.length === 0);
    };

    district.addEventListener("change", () => {
      populateUpozilas();
      renderRows();
    });
    upozila.addEventListener("change", renderRows);

    populateDistricts();
    populateUpozilas();
    renderRows();
  }

  function initSettings() {
    const toggle = $("#settings-gear");
    const panel = $("#settings-toolbar");

    const applyTheme = (theme) => {
      document.documentElement.dataset.theme = theme;
      localStorage.setItem("ie_theme", theme);
      $$("[data-theme-choice]").forEach((button) => {
        button.classList.toggle("active", button.dataset.themeChoice === theme);
      });
    };

    const applyFont = (size) => {
      document.body.classList.remove("font-small", "font-large");
      if (size === "small") document.body.classList.add("font-small");
      if (size === "large") document.body.classList.add("font-large");
      localStorage.setItem("ie_font", size);
      $$("[data-font-choice]").forEach((button) => {
        button.classList.toggle("active", button.dataset.fontChoice === size);
      });
    };

    applyTheme(localStorage.getItem("ie_theme") || "light");
    applyFont(localStorage.getItem("ie_font") || "base");

    toggle?.addEventListener("click", () => {
      const open = panel?.classList.toggle("open");
      toggle.setAttribute("aria-expanded", String(Boolean(open)));
    });

    $$("[data-theme-choice]").forEach((button) => {
      button.addEventListener("click", () => applyTheme(button.dataset.themeChoice));
    });

    $$("[data-font-choice]").forEach((button) => {
      button.addEventListener("click", () => applyFont(button.dataset.fontChoice));
    });

    window.IslamicEducation = {
      toggleTheme: applyTheme,
      adjustFontSize: applyFont
    };
  }

  function initCalendar() {
    const targets = Array.from(new Set($$(".js-calendar, #sidebar-calendar-container")));
    if (!targets.length) return;

    const now = new Date();
    const monthNames = ["জানুয়ারি", "ফেব্রুয়ারি", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগস্ট", "সেপ্টেম্বর", "অক্টোবর", "নভেম্বর", "ডিসেম্বর"];
    const dayNames = ["রবি", "সোম", "মঙ্গল", "বুধ", "বৃহ", "শুক্র", "শনি"];
    const year = now.getFullYear();
    const month = now.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const days = new Date(year, month + 1, 0).getDate();
    const today = now.getDate();
    const eventDays = new Set([15, 20, 22, Math.min(days, 28)]);

    let html = `<div class="calendar-head"><span>${monthNames[month]}</span><span>${toBn(year)}</span></div><div class="calendar-grid">`;
    dayNames.forEach((day) => {
      html += `<span class="day-name">${day}</span>`;
    });
    for (let i = 0; i < firstDay; i += 1) html += "<span></span>";
    for (let day = 1; day <= days; day += 1) {
      const classes = [day === today ? "today" : "", eventDays.has(day) ? "event-day" : ""].filter(Boolean).join(" ");
      html += `<span class="${classes}">${toBn(day)}</span>`;
    }
    html += "</div>";
    targets.forEach((target) => {
      target.innerHTML = html;
    });
  }

  function initLightbox() {
    const lightbox = $("#gallery-lightbox");
    const title = $("#lightbox-title");
    const visual = $("#lightbox-visual");
    const close = $("#lightbox-close");
    let lastFocus = null;

    if (!lightbox || !title || !visual || !close) return;

    function closeLightbox() {
      lightbox.classList.remove("open");
      visual.classList.remove("video-preview");
      lastFocus?.focus();
    }

    function openPreview(item, options) {
      const source = $(options.sourceSelector, item);
      lastFocus = item;
      title.textContent = item.dataset[options.dataKey] || options.fallbackTitle;
      visual.style.background = source ? getComputedStyle(source).background : "";
      visual.classList.toggle("video-preview", options.isVideo);
      lightbox.classList.add("open");
      close.focus();
    }

    $$(".gallery-card").forEach((item) => {
      item.addEventListener("click", () => {
        openPreview(item, {
          dataKey: "gallery",
          fallbackTitle: "গ্যালারি",
          sourceSelector: ".gallery-visual",
          isVideo: false
        });
      });
    });

    $$(".video-card").forEach((item) => {
      item.addEventListener("click", () => {
        openPreview(item, {
          dataKey: "video",
          fallbackTitle: "ভিডিও গ্যালারি",
          sourceSelector: ".video-thumb",
          isVideo: true
        });
      });
    });

    close.addEventListener("click", closeLightbox);
    lightbox.addEventListener("click", (event) => {
      if (event.target === lightbox) closeLightbox();
    });
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && lightbox.classList.contains("open")) closeLightbox();
    });
  }

  function initDemoActions() {
    $$("[data-demo]").forEach((element) => {
      element.addEventListener("click", (event) => {
        event.preventDefault();
        showToast(element.dataset.demo || "ডেমো অ্যাকশন সম্পন্ন হয়েছে।");
      });
    });
  }

  function initSearch() {
    const form = $("#site-search");
    const input = $("#search-input");
    if (!form || !input) return;

    const routes = [
      { terms: ["ভর্তি", "admission"], target: "#admission-form-section" },
      { terms: ["মুয়াল্লিম", "মুয়াল্লিম", "muallim", "teacher", "শিক্ষক আবেদন"], target: "#muallim-application-section" },
      { terms: ["মক্তব", "maktab", "শিশু", "নৈতিক শিক্ষা"], target: "#maktab-section" },
      { terms: ["প্রশিক্ষণ", "training", "teacher training"], target: "#training-section" },
      { terms: ["ক্যালেন্ডার", "calendar", "শিক্ষা ক্যালেন্ডার", "academic calendar"], target: "#calendar-section" },
      { terms: ["ফলাফল", "result", "results", "মার্কশিট"], target: "#results-section" },
      { terms: ["পরীক্ষা", "exam", "routine", "প্রবেশপত্র"], target: "#exam-section" },
      { terms: ["ডোনেশন", "donation", "দান", "support"], target: "#donation-section" },
      { terms: ["লগইন", "login", "student login"], target: "#student-login-section" },
      { terms: ["কোর্স", "course", "quran", "arabic", "তাজবিদ", "আরবি"], target: "#courses-section" },
      { terms: ["শাখা", "branch", "প্রতিনিধি"], target: "#representatives-section" },
      { terms: ["যোগাযোগ", "contact", "phone"], target: "#contact-us-section" },
      { terms: ["ডাউনলোড", "download"], target: "#quick-downloads-section" }
    ];

    form.addEventListener("submit", (event) => {
      event.preventDefault();
      const query = input.value.trim().toLowerCase();
      if (!query) {
        showToast("অনুসন্ধান করতে একটি শব্দ লিখুন।");
        return;
      }
      const match = routes.find((route) => route.terms.some((term) => query.includes(term.toLowerCase())));
      const target = match ? $(match.target) : null;
      if (!target) {
        showToast("এই ডেমোতে মিল পাওয়া যায়নি। মেনু থেকে বিভাগ নির্বাচন করুন।");
        return;
      }
      target.scrollIntoView({ behavior: "smooth", block: "start" });
      input.value = "";
    });
  }

  function initBackToTop() {
    const button = $("#back-to-top");
    if (!button) return;

    const toggle = () => {
      button.classList.toggle("visible", window.scrollY > 700);
    };

    window.addEventListener("scroll", toggle, { passive: true });
    button.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
    toggle();
  }

  document.addEventListener("DOMContentLoaded", () => {
    initClock();
    initMobileMenu();
    initDropdownA11y();
    initActiveNav();
    initRevealAndCounters();
    initBranchDistrictSelects();
    initAdmissionForm();
    initMuallimForm();
    initResultLookup();
    initCourseSelectionCtas();
    initSimpleForms();
    initRepresentativeFilter();
    initSettings();
    initCalendar();
    initLightbox();
    initDemoActions();
    initSearch();
    initBackToTop();
  });
}());
