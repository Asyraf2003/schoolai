import '../../css/pages/welcome-about-flow.css';

(function () {
    'use strict';

    var copyByLocale = {
        id: {
            eyebrow: 'Tentang Al Mustaqbal',
            titleLead: 'Pendidikan yang bergerak bersama ',
            titleAccent: 'rasa ingin tahu.',
            description: 'Al Mustaqbal menumbuhkan generasi Muslim yang Qurani, inovatif, inspiratif, dan berintegritas melalui pengalaman belajar yang holistik, hidup, dan bermakna.',
            values: ['Qurani', 'Innovation', 'Inspiration', 'Integrity'],
            scrollCue: 'Scroll untuk mengikuti alirannya'
        },
        en: {
            eyebrow: 'About Al Mustaqbal',
            titleLead: 'Education that moves with ',
            titleAccent: 'curiosity.',
            description: 'Al Mustaqbal nurtures a Qur’anic, innovative, inspiring, and principled Muslim generation through learning experiences that are holistic, alive, and meaningful.',
            values: ['Qur’anic', 'Innovation', 'Inspiration', 'Integrity'],
            scrollCue: 'Scroll to follow the flow'
        },
        ar: {
            eyebrow: 'عن مدرسة المستقبل',
            titleLead: 'تعليم يتحرّك مع ',
            titleAccent: 'الفضول.',
            description: 'تنمّي مدرسة المستقبل جيلاً مسلماً قرآنياً مبتكراً ملهماً ومتصفاً بالنزاهة، من خلال خبرات تعليمية شمولية وحيوية وذات معنى.',
            values: ['قرآني', 'ابتكار', 'إلهام', 'نزاهة'],
            scrollCue: 'مرّر لمتابعة التدفق'
        }
    };

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function getLocale() {
        var lang = (document.documentElement.getAttribute('lang') || 'id').toLowerCase();
        var key = lang.split('-')[0];
        return copyByLocale[key] ? key : 'id';
    }

    function createSection(copy) {
        var section = document.createElement('section');
        section.className = 'about-flow';
        section.id = 'tentang';
        section.setAttribute('data-about-flow', '');
        section.setAttribute('aria-labelledby', 'about-flow-title');

        section.innerHTML = [
            '<div class="about-flow__sticky">',
            '  <div class="about-flow__backdrop" aria-hidden="true"></div>',
            '  <div class="about-flow__stage" aria-hidden="true">',
            '    <svg class="about-flow__svg" viewBox="0 0 1600 1050" preserveAspectRatio="xMidYMid slice" focusable="false">',
            '      <defs>',
            '        <linearGradient id="about-flow-primary" x1="0%" y1="0%" x2="100%" y2="100%">',
            '          <stop offset="0%" stop-color="#8bb8ff"/>',
            '          <stop offset="28%" stop-color="#7258ff"/>',
            '          <stop offset="57%" stop-color="#ff4eb8"/>',
            '          <stop offset="78%" stop-color="#ff8b42"/>',
            '          <stop offset="100%" stop-color="#ffd04a"/>',
            '        </linearGradient>',
            '        <linearGradient id="about-flow-secondary" x1="10%" y1="100%" x2="90%" y2="0%">',
            '          <stop offset="0%" stop-color="#4f7cff"/>',
            '          <stop offset="26%" stop-color="#7b4eff"/>',
            '          <stop offset="52%" stop-color="#ff53bd"/>',
            '          <stop offset="76%" stop-color="#ff8250"/>',
            '          <stop offset="100%" stop-color="#ffbf2e"/>',
            '        </linearGradient>',
            '        <linearGradient id="about-flow-highlight" x1="0%" y1="0%" x2="100%" y2="0%">',
            '          <stop offset="0%" stop-color="#ffffff" stop-opacity="0"/>',
            '          <stop offset="45%" stop-color="#ffffff" stop-opacity="0.72"/>',
            '          <stop offset="68%" stop-color="#fff3c7" stop-opacity="0.5"/>',
            '          <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>',
            '        </linearGradient>',
            '        <filter id="about-flow-glow" x="-30%" y="-30%" width="160%" height="160%">',
            '          <feGaussianBlur stdDeviation="24"/>',
            '        </filter>',
            '      </defs>',
            '',
            '      <g class="about-flow__ribbon-group">',
            '        <path class="about-flow__ribbon-glow" d="M1508 -130 C1118 118 1290 397 1120 580 C956 758 1006 893 1498 1185" stroke="#ff58bd" stroke-width="286" filter="url(#about-flow-glow)"/>',
            '        <path class="about-flow__ribbon about-flow__ribbon--soft" d="M1508 -130 C1118 118 1290 397 1120 580 C956 758 1006 893 1498 1185" stroke="url(#about-flow-primary)" stroke-width="220"/>',
            '        <path class="about-flow__ribbon about-flow__ribbon--core" d="M1473 -112 C1188 128 1316 394 1132 586 C1005 718 1037 884 1456 1150" stroke="url(#about-flow-secondary)" stroke-width="105"/>',
            '        <path class="about-flow__ribbon-highlight" d="M1447 -95 C1236 137 1340 399 1150 592 C1056 690 1071 856 1416 1117" stroke="url(#about-flow-highlight)" stroke-width="23"/>',
            '      </g>',
            '',
            '      <g class="about-flow__ribbon-group about-flow__ribbon-group--cross">',
            '        <path class="about-flow__ribbon-glow" d="M-214 -25 C250 102 571 404 900 520 C1218 633 1468 785 1770 1114" stroke="#7558ff" stroke-width="246" filter="url(#about-flow-glow)"/>',
            '        <path class="about-flow__ribbon about-flow__ribbon--soft" d="M-214 -25 C250 102 571 404 900 520 C1218 633 1468 785 1770 1114" stroke="url(#about-flow-secondary)" stroke-width="182"/>',
            '        <path class="about-flow__ribbon about-flow__ribbon--core" d="M-180 8 C268 128 583 414 916 531 C1227 640 1454 792 1734 1081" stroke="url(#about-flow-primary)" stroke-width="82"/>',
            '        <path class="about-flow__ribbon-highlight" d="M-150 35 C288 151 603 424 932 540 C1238 649 1448 798 1700 1047" stroke="url(#about-flow-highlight)" stroke-width="18"/>',
            '      </g>',
            '    </svg>',
            '  </div>',
            '',
            '  <span class="about-flow__orb about-flow__orb--one" aria-hidden="true"></span>',
            '  <span class="about-flow__orb about-flow__orb--two" aria-hidden="true"></span>',
            '',
            '  <div class="about-flow__content container">',
            '    <div class="about-flow__copy">',
            '      <p class="about-flow__eyebrow" data-about-flow-eyebrow></p>',
            '      <h2 class="about-flow__title" id="about-flow-title">',
            '        <span data-about-flow-title-lead></span><span class="about-flow__title-accent" data-about-flow-title-accent></span>',
            '      </h2>',
            '      <p class="about-flow__description" data-about-flow-description></p>',
            '      <div class="about-flow__values" data-about-flow-values aria-label="QIII"></div>',
            '    </div>',
            '  </div>',
            '',
            '  <div class="about-flow__scroll-cue" aria-hidden="true">',
            '    <span class="about-flow__scroll-line"></span>',
            '    <span data-about-flow-scroll-cue></span>',
            '  </div>',
            '</div>'
        ].join('\n');

        section.querySelector('[data-about-flow-eyebrow]').textContent = copy.eyebrow;
        section.querySelector('[data-about-flow-title-lead]').textContent = copy.titleLead;
        section.querySelector('[data-about-flow-title-accent]').textContent = copy.titleAccent;
        section.querySelector('[data-about-flow-description]').textContent = copy.description;
        section.querySelector('[data-about-flow-scroll-cue]').textContent = copy.scrollCue;

        var values = section.querySelector('[data-about-flow-values]');
        copy.values.forEach(function (value) {
            var chip = document.createElement('span');
            chip.className = 'about-flow__value';
            chip.textContent = value;
            values.appendChild(chip);
        });

        return section;
    }

    function mountAboutFlow() {
        if (document.querySelector('[data-about-flow]')) return;

        var hero = document.querySelector('.hero-cinema');
        var stats = document.querySelector('.stats-ribbon');

        if (!hero || !stats || !stats.parentNode) return;

        var locale = getLocale();
        var section = createSection(copyByLocale[locale]);
        stats.parentNode.insertBefore(section, stats);

        var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        function setProgress(progress) {
            var x = -5.5 + (progress * 11.5);
            var y = -5 + (progress * 24);
            var rotation = -4.5 + (progress * 9);
            var copyY = -0.8 - (progress * 6.5);
            var copyOpacity = 1 - (Math.max(0, progress - 0.56) / 0.44) * 0.3;

            section.style.setProperty('--about-flow-x', x.toFixed(3) + 'vw');
            section.style.setProperty('--about-flow-y', y.toFixed(3) + 'vh');
            section.style.setProperty('--about-flow-rotate', rotation.toFixed(3) + 'deg');
            section.style.setProperty('--about-flow-copy-y', copyY.toFixed(3) + 'vh');
            section.style.setProperty('--about-flow-copy-opacity', clamp(copyOpacity, 0.7, 1).toFixed(3));
        }

        if (reducedMotion) {
            setProgress(0.42);
            return;
        }

        var ticking = false;

        function updateFromScroll() {
            ticking = false;

            var rect = section.getBoundingClientRect();
            var travel = Math.max(section.offsetHeight - window.innerHeight, 1);
            var progress = clamp((-rect.top) / travel, 0, 1);
            setProgress(progress);
        }

        function requestScrollUpdate() {
            if (ticking) return;
            ticking = true;
            window.requestAnimationFrame(updateFromScroll);
        }

        window.addEventListener('scroll', requestScrollUpdate, { passive: true });
        window.addEventListener('resize', requestScrollUpdate, { passive: true });
        updateFromScroll();

        var finePointer = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;

        if (finePointer) {
            section.addEventListener('pointermove', function (event) {
                var rect = section.getBoundingClientRect();
                var nx = clamp((event.clientX - rect.left) / Math.max(rect.width, 1), 0, 1) - 0.5;
                var ny = clamp((event.clientY - rect.top) / Math.max(rect.height, 1), 0, 1) - 0.5;
                var pointerX = nx * 13;
                var pointerY = ny * 9;

                section.style.setProperty('--about-flow-pointer-x', pointerX.toFixed(2) + 'px');
                section.style.setProperty('--about-flow-pointer-y', pointerY.toFixed(2) + 'px');
                section.style.setProperty('--about-flow-orb-one-x', (-pointerX * 0.55).toFixed(2) + 'px');
                section.style.setProperty('--about-flow-orb-one-y', (-pointerY * 0.55).toFixed(2) + 'px');
                section.style.setProperty('--about-flow-orb-two-x', (pointerX * 0.35).toFixed(2) + 'px');
                section.style.setProperty('--about-flow-orb-two-y', (pointerY * 0.35).toFixed(2) + 'px');
            }, { passive: true });

            section.addEventListener('pointerleave', function () {
                section.style.setProperty('--about-flow-pointer-x', '0px');
                section.style.setProperty('--about-flow-pointer-y', '0px');
                section.style.setProperty('--about-flow-orb-one-x', '0px');
                section.style.setProperty('--about-flow-orb-one-y', '0px');
                section.style.setProperty('--about-flow-orb-two-x', '0px');
                section.style.setProperty('--about-flow-orb-two-y', '0px');
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountAboutFlow, { once: true });
    } else {
        mountAboutFlow();
    }
})();
