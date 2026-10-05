// Pure playback policy; effects are handled by the injected render port.
export function createHeroState({ count, reduced = false, render }) {
    let state = { index: 0, paused: reduced, audio: false, visible: true, inViewport: true, suspended: false, focused: false, reduced };
    const canPlay = () => !state.paused && state.visible && state.inViewport && !state.suspended;
    function send(event) {
        if (!count) return;
        if (event.type === 'step' || (event.type === 'advance' && canPlay() && !state.reduced && !state.focused)) {
            state.index = (state.index + (event.delta ?? 1) + count) % count;
        } else if (event.type === 'pause') state.paused = !state.paused;
        else if (event.type === 'audio') { state.audio = !state.audio; if (state.audio) state.paused = false; }
        else if (event.type === 'audio-blocked') state.audio = false;
        else if (event.type === 'environment') state = { ...state, ...event.values };
        else if (event.type === 'motion') { state.reduced = event.reduced; state.paused = event.reduced || state.paused; }
        render({ ...state, canPlay: canPlay(), canAdvance: canPlay() && !state.reduced && !state.focused && count > 1 }, event);
    }
    return { send, snapshot: () => ({ ...state, canPlay: canPlay() }) };
}
