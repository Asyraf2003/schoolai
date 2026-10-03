import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function wireImages(context, state, actions) {
    context.imageInput?.addEventListener('change', async () => {
        const file = context.imageInput.files?.[0];
        if (!file) return;
        await actions.uploadImage(file, 'content', state.imageUploadIntent);
        state.imageUploadIntent = 'insert';
        context.imageInput.value = '';
    });

    context.thumbnailInput?.addEventListener('change', async () => {
        const file = context.thumbnailInput.files?.[0];
        if (!file) return;
        await actions.uploadImage(file, 'thumbnail');
        context.thumbnailInput.value = '';
    });

    context.app.querySelectorAll('[data-image-layout]').forEach((button) => {
        button.addEventListener('click', () => actions.setImageLayout(button.dataset.imageLayout));
    });

    context.app.querySelectorAll('[data-image-align]').forEach((button) => {
        button.addEventListener('click', () => actions.setImageAlignment(button.dataset.imageAlign));
    });

    context.app.querySelector('[data-image-alt]')?.addEventListener('click', () => {
        const image = state.selectedFigure?.querySelector('img');
        if (!image) return;
        const value = window.prompt('Alt text untuk aksesibilitas dan SEO:', image.alt || '');
        if (value === null) return;
        image.alt = value.slice(0, 300);
        actions.changed();
    });

    context.app.querySelector('[data-image-thumbnail]')?.addEventListener('click', () => {
        const image = state.selectedFigure?.querySelector('img');
        if (!image) return;
        state.thumbnailUrl = image.getAttribute('src') || image.src;
        context.app.dataset.thumbnailUrl = state.thumbnailUrl;
        actions.changed();
        actions.refreshPreview();
        actions.showTransientSaveState('Thumbnail dipilih · Menyimpan…');
    });

    context.app.querySelector('[data-image-replace]')?.addEventListener('click', () => {
        if (!state.selectedFigure) return;
        state.imageUploadIntent = 'replace';
        context.imageInput?.click();
    });

    context.app.querySelector('[data-image-continue]')?.addEventListener('click', () => {
        if (!state.selectedFigure) return;
        actions.ensureEditableBlockAfter(state.selectedFigure, true);
        actions.hideImageToolbar();
    });

    context.app.querySelector('[data-image-delete]')?.addEventListener('click', () => {
        if (!state.selectedFigure) return;
        const figure = state.selectedFigure;
        const next = actions.ensureEditableBlockAfter(figure, false);
        figure.remove();
        actions.hideImageToolbar();
        if (next) placeCaret(next);
        actions.changed();
    });

    context.app.querySelector('[data-code-exit]')?.addEventListener('click', () => actions.exitCodeBlock());
    context.app.querySelector('[data-thumbnail-change]')?.addEventListener('click', () => context.thumbnailInput?.click());
}
