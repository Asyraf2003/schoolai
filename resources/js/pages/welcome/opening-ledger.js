export const REQUIRED_OPENING_UNITS = Object.freeze([
    'hero', 'navigation', 'runtimes', 'styles', 'fonts', 'images', 'media',
    'vision', 'program', 'values', 'gallery', 'testimonials', 'articles', 'footer', 'geometry',
]);
const SETTLED = new Set(['PREPARED', 'STATIC_FALLBACK']);

export function createOpeningLedger() {
    const units = new Map(REQUIRED_OPENING_UNITS.map(unit => [unit, 'PENDING']));
    function settle(unit, state, usable) {
        if (!units.has(unit) || SETTLED.has(units.get(unit))) return false;
        if (!SETTLED.has(state) || usable !== true) {
            units.set(unit, state === 'ABORTED' ? 'ABORTED' : 'FAILED');
            return false;
        }
        units.set(unit, state);
        return true;
    }
    return {
        settle,
        get progress() { return 100 * [...units.values()].filter(state => SETTLED.has(state)).length / REQUIRED_OPENING_UNITS.length; },
        get complete() { return [...units.values()].every(state => SETTLED.has(state)); },
        snapshot() { return Object.fromEntries(units); },
    };
}
