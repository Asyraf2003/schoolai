// Main-navigation highlight is one projection of hover, open panel and route state.
export function highlightedIndex({ hover = null, panel = null, current = null }) {
    return hover ?? panel ?? current;
}

export function mountHeaderHighlight(root) {
    const controls = [...root.querySelectorAll('.site-header__items > li > a, .site-header__items > li > details > summary')];
    const current = controls.findIndex(control => control.getAttribute('aria-current') === 'page');
    let panel = null;
    let hover = null;
    function paint() {
        const open = controls.findIndex(control => control.parentElement.dataset.panel === panel);
        const winner = highlightedIndex({ hover: root.dataset.languageOpen === 'true' ? null : hover,
            panel: open < 0 ? null : open, current: current < 0 ? null : current });
        controls.forEach((control, index) => control.toggleAttribute('data-highlighted', index === winner));
    }
    function over(event) {
        if (event.type === 'pointerover' && !window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
        const control = event.target.closest('a, summary');
        if (event.type === 'focusin' && controls.includes(control) && !control.matches(':focus-visible')) return;
        hover = controls.includes(control) ? controls.indexOf(control) : null;
        paint();
    }
    function out(event) {
        const control = event.relatedTarget?.closest?.('a, summary');
        hover = controls.includes(control) ? controls.indexOf(control) : null;
        paint();
    }
    root.addEventListener('pointerover', over);
    root.addEventListener('pointerout', out);
    root.addEventListener('focusin', over);
    root.addEventListener('focusout', out);
    paint();
    return {
        update(id) { panel = id; paint(); },
        dispose() {
            root.removeEventListener('pointerover', over);
            root.removeEventListener('pointerout', out);
            root.removeEventListener('focusin', over);
            root.removeEventListener('focusout', out);
            controls.forEach(control => control.removeAttribute('data-highlighted'));
        },
    };
}
