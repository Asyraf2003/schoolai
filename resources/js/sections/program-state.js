// Pure interaction policy. Browser, focus, media and animation live in adapters.
export function createProgramState(count) {
    let phase = 'idle';
    let index = -1;
    let version = 0;
    return {
        get phase() { return phase; },
        get index() { return index; },
        begin(next) {
            if (phase !== 'idle' || !Number.isInteger(next) || next < 0 || next >= count) return null;
            index = next;
            phase = 'preparing';
            return ++version;
        },
        accepts(token) { return token === version && phase === 'preparing'; },
        opened(token, animated) {
            if (!this.accepts(token)) return false;
            phase = animated ? 'opening' : 'detail';
            return true;
        },
        revealed() { if (phase === 'opening') phase = 'detail'; },
        close() {
            if (phase === 'idle' || phase === 'closing' || phase === 'disposed') return false;
            ++version;
            phase = 'closing';
            return true;
        },
        reset() { ++version; phase = 'idle'; index = -1; },
        dispose() { ++version; phase = 'disposed'; index = -1; },
    };
}
