export var ROOT_SELECTOR = '[data-testimonial-network-story]';
export var DESKTOP_QUERY = '(min-width: 961px)';
export var REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';

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

export function setNumberProperty(element, property, value, precision) {
        if (!element) return;

        element.style.setProperty(
            property,
            Number(value).toFixed(typeof precision === 'number' ? precision : 4)
        );
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

export function copyForLocale() {
        var copy = {
            id: {
                title: 'Apa Kata Mereka Tentang Al Mustaqbal?',
                openMedia: 'Buka media testimoni',
                openVideo: 'Buka video testimoni',
                close: 'Tutup'
            },
            en: {
                title: 'What Do People Say About Al Mustaqbal?',
                openMedia: 'Open testimonial media',
                openVideo: 'Open testimonial video',
                close: 'Close'
            },
            ar: {
                title: 'ماذا يقول الناس عن مدرسة المستقبل؟',
                openMedia: 'افتح وسائط الشهادة',
                openVideo: 'افتح فيديو الشهادة',
                close: 'إغلاق'
            }
        };

        return copy[localeKey()] || copy.id;
    }

export function escapeAttribute(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
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
