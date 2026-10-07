import { createProgramState } from './program-state.js';

export function mountProgramDialog(root, prepare, signal) {
    const cards = [...root.querySelectorAll('[data-program-card]')];
    const dialog = root.querySelector('[data-program-dialog]');
    const stage = root.querySelector('[data-program-detail-stage]');
    const typeHome = root.querySelector('[data-program-type-home]');
    const typeViewport = root.querySelector('[data-program-type-viewport]');
    const state = createProgramState(cards.length);
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    let kinetic;
    let opener;
    let detail;
    const publish = () => { root.dataset.programState = state.phase; };
    const restore = () => {
        kinetic?.reset();
        delete document.documentElement.dataset.programDetailOpen;
        if (detail) {
            detail.querySelector('[data-program-back]').hidden = true;
            cards[state.index]?.append(detail);
        }
        if (dialog.open) dialog.close();
        typeHome.append(typeViewport);
        detail = null;
        state.reset();
        root.removeAttribute('aria-busy');
        publish();
        opener?.focus({ preventScroll: true });
        opener = null;
        root.dispatchEvent(new Event('program:restored'));
    };
    const close = (immediate = false) => {
        if (immediate && state.phase === 'closing') { restore(); return; }
        if (!state.close()) return;
        publish();
        if (immediate || !detail || !kinetic || motion.matches || dialog.dataset.phase !== 'detail') restore();
        else { dialog.dataset.phase = 'closing'; kinetic.close(detail, restore); }
    };
    const open = async trigger => {
        const index = cards.indexOf(trigger.closest('[data-program-card]'));
        const token = state.begin(index);
        if (token === null) return;
        opener = trigger;
        publish();
        root.setAttribute('aria-busy', 'true');
        kinetic = motion.matches ? null : await prepare();
        if (!state.accepts(token)) return;
        if (motion.matches) kinetic = null;
        root.removeAttribute('aria-busy');
        detail = cards[index].querySelector('[data-program-detail]');
        detail.querySelector('[data-program-back]').hidden = false;
        const image = detail.querySelector('[data-program-image]');
        image.src = image.dataset.src;
        stage.append(detail);
        dialog.prepend(typeViewport);
        dialog.setAttribute('aria-labelledby', detail.getAttribute('aria-labelledby'));
        state.opened(token, !!kinetic);
        dialog.dataset.phase = kinetic ? 'opening' : 'detail';
        document.documentElement.dataset.programDetailOpen = 'true';
        try { dialog.showModal(); }
        catch { restore(); cards[index].open = true; return; }
        dialog.scrollTop = 0;
        dialog.focus({ preventScroll: true });
        publish();
        const focusBack = () => detail?.querySelector('[data-program-back]').focus({ preventScroll: true });
        if (kinetic) kinetic.open(detail, () => {}, () => { state.revealed(); publish(); focusBack(); });
        else focusBack();
    };
    publish();
    if (typeof dialog.showModal !== 'function') return { suspend() {}, resume() {}, dispose() { state.dispose(); } };
    root.querySelectorAll('[data-program-open]').forEach(trigger => { trigger.setAttribute('aria-haspopup', 'dialog'); });
    root.addEventListener('click', event => {
        const trigger = event.target.closest('[data-program-open]');
        if (trigger) { event.preventDefault(); open(trigger); }
        if (event.target.closest('[data-program-back]')) close();
    }, { signal });
    root.addEventListener('focusin', event => { if (event.target.closest('[data-program-open]') && !motion.matches) prepare(); }, { signal });
    root.addEventListener('pointerover', event => {
        if (event.pointerType === 'mouse' && event.target.closest('[data-program-open]') && !motion.matches) prepare();
    }, { signal });
    dialog.addEventListener('cancel', event => { event.preventDefault(); close(); }, { signal });
    dialog.addEventListener('close', () => { if (detail) restore(); }, { signal });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && state.phase === 'preparing') { event.preventDefault(); close(true); }
        if (event.key !== 'Tab' || !dialog.open) return;
        const back = detail?.querySelector('[data-program-back]');
        event.preventDefault();
        if (dialog.dataset.phase === 'detail') back?.focus({ preventScroll: true });
        else dialog.focus({ preventScroll: true });
    }, { signal });
    motion.addEventListener('change', () => {
        if (!motion.matches || !dialog.open) return;
        if (state.phase === 'closing') { restore(); return; }
        kinetic?.reset();
        kinetic = null;
        state.revealed();
        dialog.dataset.phase = 'detail';
        publish();
        detail.querySelector('[data-program-back]').focus({ preventScroll: true });
    }, { signal });
    return {
        suspend() { close(true); },
        resume() {},
        dispose() { close(true); state.dispose(); publish(); },
    };
}
