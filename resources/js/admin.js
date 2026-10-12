for (const editor of document.querySelectorAll('[data-editor]')) {
    const source = editor.querySelector('textarea');
    const surface = editor.querySelector('[data-editor-surface]');
    const toolbar = editor.querySelector('[data-editor-toolbar]');
    surface.innerHTML = source.value;
    surface.hidden = false;
    source.hidden = true;
    toolbar.hidden = false;
    const sync = () => { source.value = surface.innerHTML; };
    surface.addEventListener('input', sync);
    surface.addEventListener('paste', event => {
        event.preventDefault();
        document.execCommand('insertText', false, event.clipboardData.getData('text/plain'));
        sync();
    });
    surface.addEventListener('drop', event => event.preventDefault());
    let selection;
    surface.addEventListener('keyup', () => { selection = getSelection().rangeCount ? getSelection().getRangeAt(0).cloneRange() : null; });
    surface.addEventListener('mouseup', () => { selection = getSelection().rangeCount ? getSelection().getRangeAt(0).cloneRange() : null; });
    toolbar.addEventListener('click', event => {
        const button = event.target.closest('[data-command]');
        if (!button) return;
        surface.focus();
        if (selection) { getSelection().removeAllRanges(); getSelection().addRange(selection); }
        const command = button.dataset.command;
        let value = null;
        if (command === 'createLink') {
            value = prompt('URL tautan (https://...)');
            if (!value || !/^https?:\/\//i.test(value.trim())) return;
        }
        document.execCommand(command, false, value);
        sync();
    });
    editor.closest('form').addEventListener('submit', sync);
}
