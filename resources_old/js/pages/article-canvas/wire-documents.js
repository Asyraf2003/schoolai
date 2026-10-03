import { resizeTextarea } from './helpers.js';

export function wireDocuments(context, state, actions) {
    context.documents.forEach((document) => {
        const title = document.querySelector('[data-title]');
        const subtitle = document.querySelector('[data-subtitle]');
        const editor = document.querySelector('[data-editor]');

        [title, subtitle].forEach((field) => {
            if (!field) return;
            resizeTextarea(field);
            field.addEventListener('input', () => {
                resizeTextarea(field);
                actions.changed();
            });
        });

        if (!editor) return;
        actions.normalizeEmptyEditor(editor);
        editor.addEventListener('input', () => {
            actions.changed();
            actions.updateMetrics();
            actions.updateBlockMenu();
            actions.positionCodeToolbar();
        });
        editor.addEventListener('focus', () => {
            actions.rememberSelection();
            actions.updateBlockMenu();
        });
        editor.addEventListener('click', actions.handleEditorClick);
        editor.addEventListener('keyup', (event) => {
            actions.handleMarkdownShortcut(event, editor);
            actions.rememberSelection();
            actions.updateInlineToolbar();
            actions.updateBlockMenu();
            actions.positionCodeToolbar();
        });
        editor.addEventListener('mouseup', () => {
            actions.rememberSelection();
            actions.updateInlineToolbar();
            actions.updateBlockMenu();
        });
        editor.addEventListener('paste', (event) => {
            event.preventDefault();
            const text = event.clipboardData?.getData('text/plain') || '';
            document.execCommand('insertText', false, text);
        });
        editor.addEventListener('keydown', actions.handleEditorShortcut);
    });

    context.languageButtons.forEach((button) => {
        button.addEventListener('click', () => {
            state.activeLanguage = button.dataset.language || 'id';
            context.languageButtons.forEach((candidate) => {
                const active = candidate === button;
                candidate.classList.toggle('is-active', active);
                candidate.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            context.documents.forEach((document) => {
                const active = document.dataset.documentLanguage === state.activeLanguage;
                document.hidden = !active;
                document.classList.toggle('is-active', active);
            });
            actions.hideInlineToolbar();
            actions.hideImageToolbar();
            actions.hideCodeToolbar();
            actions.updateMetrics();
            actions.updateBlockMenu();
        });
    });
}
