export function createHomepageScrollGate(onFallback) {
    const gate = window.schoolaiHomeOpening;
    if (!gate) throw new Error('Critical homepage opening bootstrap unavailable');
    gate.adopt(onFallback);
    return gate;
}
