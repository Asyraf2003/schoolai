// Native dialog owns top-layer positioning, modal focus and background inertness.
export function mountHeaderLanguage(root, requestClose) {
    const group = root.querySelector('[data-panel="language"]');
    const dialog = group?.querySelector('dialog');
    if (!dialog?.showModal) return { sync() {}, dispose() {} };
    const trigger = group.querySelector('summary');
    let opened = false;
    let submitting = false;
    dialog.removeAttribute('open');
    function dismiss(event) {
        event.preventDefault();
        requestClose();
    }
    function backdrop(event) {
        if (event.target === dialog) dismiss(event);
    }
    function submit(event) {
        if (submitting) { event.preventDefault(); return; }
        submitting = true;
        dialog.setAttribute('aria-busy', 'true');
        dialog.close();
        requestClose();
    }
    function resume() {
        submitting = false;
        dialog.removeAttribute('aria-busy');
        dialog.querySelectorAll('[data-locale]').forEach(button => { button.disabled = false; });
    }
    dialog.addEventListener('cancel', dismiss);
    dialog.addEventListener('click', backdrop);
    dialog.addEventListener('submit', submit);
    window.addEventListener('pageshow', resume);
    return {
        sync(show) {
            if (show === opened) return;
            opened = show;
            trigger.setAttribute('aria-expanded', String(show));
            if (show) {
                dialog.showModal();
                dialog.querySelector('[aria-current]')?.focus();
            } else {
                dialog.close();
                if (trigger.getClientRects().length) trigger.focus();
            }
        },
        dispose() {
            dialog.close();
            dialog.removeEventListener('cancel', dismiss);
            dialog.removeEventListener('click', backdrop);
            dialog.removeEventListener('submit', submit);
            window.removeEventListener('pageshow', resume);
            dialog.setAttribute('open', '');
        },
    };
}
