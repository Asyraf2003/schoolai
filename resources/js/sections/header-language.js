// Native dialog owns top-layer positioning, modal focus and background inertness.
export function mountHeaderLanguage(root, changed) {
    const group = root.querySelector('[data-language]');
    const dialog = group?.querySelector('dialog');
    if (!dialog?.showModal) return { close() {}, isOpen: () => false, dispose() {} };
    const trigger = group.querySelector('summary');
    let opened = false;
    let submitting = false;
    dialog.removeAttribute('open');
    function dismiss(event) {
        event.preventDefault();
        sync(false);
    }
    function backdrop(event) {
        if (event.target === dialog) dismiss(event);
    }
    function submit(event) {
        if (submitting) { event.preventDefault(); return; }
        submitting = true;
        dialog.setAttribute('aria-busy', 'true');
        dialog.close();
        sync(false);
    }
    function resume() {
        submitting = false;
        dialog.removeAttribute('aria-busy');
        dialog.querySelectorAll('[data-locale]').forEach(button => { button.disabled = false; });
    }
    function sync(show) {
        if (show === opened) return;
        opened = show;
        root.dataset.languageOpen = String(show);
        group.open = show;
        trigger.setAttribute('aria-expanded', String(show));
        changed(show);
        if (show) {
            dialog.showModal();
            dialog.querySelector('[aria-current]')?.focus();
        } else {
            dialog.close();
            if (trigger.getClientRects().length) trigger.focus();
        }
    }
    const toggle = event => { event.preventDefault(); sync(!opened); };
    trigger.addEventListener('click', toggle);
    dialog.addEventListener('cancel', dismiss);
    dialog.addEventListener('click', backdrop);
    dialog.addEventListener('submit', submit);
    window.addEventListener('pageshow', resume);
    return {
        close() { sync(false); },
        isOpen() { return opened; },
        dispose() {
            sync(false);
            trigger.removeEventListener('click', toggle);
            dialog.removeEventListener('cancel', dismiss);
            dialog.removeEventListener('click', backdrop);
            dialog.removeEventListener('submit', submit);
            window.removeEventListener('pageshow', resume);
            dialog.setAttribute('open', '');
        },
    };
}
