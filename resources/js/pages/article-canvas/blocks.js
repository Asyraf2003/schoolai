import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function createBlockActions(context, state, actions) {
    function insertBlock(type) {
        actions.rememberSelection();
        actions.closeBlockActions();

        if (type === 'image') {
            state.imageUploadIntent = 'insert';
            context.imageInput?.click();
            return;
        }
        if (type === 'unsplash') {
            actions.openUnsplash();
            return;
        }
        if (type === 'video' || type === 'embed') {
            actions.openUrlDialog(type);
            return;
        }
        if (type === 'code') {
            const pre = document.createElement('pre');
            const code = document.createElement('code');
            code.append(document.createElement('br'));
            pre.append(code);
            actions.insertNode(pre);
            actions.ensureEditableBlockAfter(pre, false);
            placeCaret(code);
            actions.showCodeToolbar(pre);
        }
        if (type === 'divider') {
            const divider = document.createElement('hr');
            actions.insertNode(divider);
            actions.ensureEditableBlockAfter(divider, true);
        }
        if (type === 'dropcap') {
            const block = actions.selectedBlocks()[0];
            if (block?.tagName === 'P') block.classList.toggle('has-drop-cap');
        }
        actions.changed();
    }

    async function uploadImage(file, purpose = 'content', intent = 'insert') {
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 10 * 1024 * 1024) {
            window.alert('Gambar harus JPG, PNG, atau WebP dan maksimal 10MB.');
            return;
        }
        actions.showTransientSaveState(purpose === 'thumbnail' ? 'Mengupload thumbnail…' : 'Mengupload gambar…');
        const formData = new FormData();
        formData.append('image', file);
        formData.append('purpose', purpose);
        try {
            const response = await fetch(context.app.dataset.uploadUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': context.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });
            const payload = await readJsonResponse(response);
            if (!response.ok) throw new Error(errorMessage(payload));

            if (purpose === 'thumbnail') {
                state.thumbnailUrl = payload.url;
                context.app.dataset.thumbnailUrl = state.thumbnailUrl;
                actions.refreshPreview();
                actions.showTransientSaveState('Thumbnail · Tersimpan', '');
                return;
            }

            if (intent === 'replace' && state.selectedFigure) {
                const image = state.selectedFigure.querySelector('img');
                if (image) {
                    image.src = payload.url;
                    image.alt = file.name || image.alt;
                    actions.changed();
                    actions.positionImageToolbar(state.selectedFigure);
                }
                return;
            }

            insertImage(payload.url, file.name || 'Gambar artikel');
            actions.changed();
        } catch (error) {
            if (context.saveState) {
                context.saveState.textContent = 'Upload gagal';
                context.saveState.className = 'canvas-save-state is-error';
                context.saveState.title = error.message;
            }
        }
    }

    function insertImage(url, alt = '') {
        const figure = document.createElement('figure');
        figure.className = 'article-image--inline article-image-align--center';
        const image = document.createElement('img');
        image.src = url;
        image.alt = alt;
        const caption = document.createElement('figcaption');
        caption.contentEditable = 'true';
        figure.append(image, caption);
        actions.insertNode(figure);
        actions.ensureEditableBlockAfter(figure, true);
    }

    function handleEditorClick(event) {
        const figure = event.target?.closest?.('figure');
        const pre = event.target?.closest?.('pre');
        if (figure?.querySelector('img')) {
            showImageToolbar(figure);
            actions.hideCodeToolbar();
        } else if (pre) {
            actions.showCodeToolbar(pre);
            hideImageToolbar();
        } else {
            hideImageToolbar();
            actions.hideCodeToolbar();
        }
        window.setTimeout(() => {
            actions.rememberSelection();
            actions.updateBlockMenu();
        }, 0);
    }

    function showImageToolbar(figure) {
        state.selectedFigure = figure;
        if (!context.imageToolbar) return;
        context.imageToolbar.hidden = false;
        context.imageLayoutClasses.forEach((layout) => {
            const button = context.imageToolbar.querySelector(`[data-image-layout="${layout}"]`);
            button?.classList.toggle('is-active', figure.classList.contains(`article-image--${layout}`));
        });
        context.imageAlignClasses.forEach((alignment) => {
            const button = context.imageToolbar.querySelector(`[data-image-align="${alignment}"]`);
            button?.classList.toggle('is-active', figure.classList.contains(`article-image-align--${alignment}`));
        });
        actions.positionImageToolbar(figure);
    }

    function hideImageToolbar() {
        if (context.imageToolbar) context.imageToolbar.hidden = true;
        state.selectedFigure = null;
    }

    return { insertBlock, uploadImage, insertImage, handleEditorClick, showImageToolbar, hideImageToolbar };
}
