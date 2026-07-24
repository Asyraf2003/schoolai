export var ROOT_SELECTOR = '[data-about-stats-story]';
export var TRACK_SELECTOR = '[data-about-stats-track]';
export var STAT_SELECTOR = '[data-about-stats-item]';
export var DESKTOP_QUERY = '(min-width: 961px)';
export var REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';

var STAT_DESCRIPTIONS = {
        id: [
            'Ruang belajar yang terus bertumbuh bersama anak, keluarga, dan komunitas sekolah.',
            'Pencapaian yang lahir dari proses belajar bermakna, konsisten, dan berani mencoba.',
            'Waktu belajar yang diisi dengan eksplorasi, refleksi, kolaborasi, dan pengalaman nyata.',
            'Program yang dirancang untuk menguatkan iman, ilmu, karakter, kreativitas, dan kemandirian.'
        ],
        en: [
            'A learning community that keeps growing together with children, families, and the school community.',
            'Achievements shaped by meaningful learning, consistency, curiosity, and the courage to try.',
            'Learning time filled with exploration, reflection, collaboration, and real-world experiences.',
            'Programs designed to strengthen faith, knowledge, character, creativity, and independence.'
        ],
        ar: [
            'بيئة تعليمية تنمو باستمرار مع الأطفال والأسر ومجتمع المدرسة.',
            'إنجازات تنطلق من تعلم هادف واستمرارية وفضول وشجاعة في التجربة.',
            'ساعات تعلم مليئة بالاستكشاف والتأمل والتعاون والخبرات الواقعية.',
            'برامج صممت لتعزيز الإيمان والعلم والشخصية والإبداع والاستقلالية.'
        ]
    };

export function clamp(value, minimum, maximum) {
        return Math.min(Math.max(value, minimum), maximum);
    }

export function lerp(from, to, progress) {
        return from + (to - from) * progress;
    }

export function smootherstep(edgeStart, edgeEnd, value) {
        if (edgeStart === edgeEnd) return value < edgeStart ? 0 : 1;

        var progress = clamp(
            (value - edgeStart) / (edgeEnd - edgeStart),
            0,
            1
        );

        return progress * progress * progress * (
            progress * (progress * 6 - 15) + 10
        );
    }

export function easeOutCubic(value) {
        var inverse = 1 - clamp(value, 0, 1);
        return 1 - inverse * inverse * inverse;
    }

export function setNumberProperty(element, property, value, precision) {
        if (!element) return;

        var digits = typeof precision === 'number' ? precision : 4;
        element.style.setProperty(property, Number(value).toFixed(digits));
    }

export function setPixelProperty(element, property, value) {
        if (!element) return;
        element.style.setProperty(property, Number(value).toFixed(2) + 'px');
    }

export function localeKey() {
        var language = (document.documentElement.lang || '').toLowerCase();

        if (language.indexOf('ar') === 0) return 'ar';
        if (language.indexOf('en') === 0) return 'en';
        return 'id';
    }

export function isRtlDocument() {
        return (
            (document.documentElement.dir || '').toLowerCase() === 'rtl' ||
            localeKey() === 'ar'
        );
    }

export function ensureStatDescription(statElement, index) {
        if (!statElement) return;

        var callout = statElement.querySelector(
            '.about-stats-story__stat-callout'
        );
        if (!callout) return;

        if (callout.querySelector('.about-stats-story__stat-description')) {
            return;
        }

        var descriptions = STAT_DESCRIPTIONS[localeKey()] || STAT_DESCRIPTIONS.id;
        var description = document.createElement('p');
        var line = callout.querySelector('.about-stats-story__stat-line');

        description.className = 'about-stats-story__stat-description';
        description.textContent = descriptions[index % descriptions.length];

        if (line) {
            callout.insertBefore(description, line);
        } else {
            callout.appendChild(description);
        }
    }

export function addMediaListener(mediaQuery, listener) {
        if (typeof mediaQuery.addEventListener === 'function') {
            mediaQuery.addEventListener('change', listener);
            return;
        }

        if (typeof mediaQuery.addListener === 'function') {
            mediaQuery.addListener(listener);
        }
    }

export function removeMediaListener(mediaQuery, listener) {
        if (typeof mediaQuery.removeEventListener === 'function') {
            mediaQuery.removeEventListener('change', listener);
            return;
        }

        if (typeof mediaQuery.removeListener === 'function') {
            mediaQuery.removeListener(listener);
        }
    }
