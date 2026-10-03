import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function createImageCodeActions(context, state, actions) {
    function setImageLayout(layout) {
        if (!state.selectedFigure || !context.imageLayoutClasses.includes(layout)) return;
        context.imageLayoutClasses.forEach((candidate) => state.selectedFigure.classList.remove(`article-image--${candidate}`));
        state.selectedFigure.classList.add(`article-image--${layout}`);
        if (['outset', 'screen'].includes(layout)) {
            context.imageAlignClasses.forEach((candidate) => state.selectedFigure.classList.remove(`article-image-align--${candidate}`));
            state.selectedFigure.classList.add('article-image-align--center');
        }
        actions.changed();
        actions.showImageToolbar(state.selectedFigure);
    }

    function setImageAlignment(alignment) {
        if (!state.selectedFigure || !context.imageAlignClasses.includes(alignment)) return;
        if (!state.selectedFigure.classList.contains('article-image--compact')) {
            context.imageLayoutClasses.forEach((candidate) => state.selectedFigure.classList.remove(`article-image--${candidate}`));
            state.selectedFigure.classList.add('article-image--compact');
        }
        context.imageAlignClasses.forEach((candidate) => state.selectedFigure.classList.remove(`article-image-align--${candidate}`));
        state.selectedFigure.classList.add(`article-image-align--${alignment}`);
        actions.changed();
        actions.showImageToolbar(state.selectedFigure);
    }

    function positionImageToolbar(figure) {
        const image = figure?.querySelector('img');
        if (!image || !context.imageToolbar || context.imageToolbar.hidden) return;
        positionFloatingElement(context.imageToolbar, image.getBoundingClientRect());
    }

    function showCodeToolbar(pre) {
        state.selectedCodeBlock = pre;
        if (!context.codeToolbar) return;
        context.codeToolbar.hidden = false;
        positionCodeToolbar();
    }

    function hideCodeToolbar() {
        if (context.codeToolbar) context.codeToolbar.hidden = true;
        state.selectedCodeBlock = null;
    }

    function positionCodeToolbar() {
        if (!state.selectedCodeBlock || !context.codeToolbar || context.codeToolbar.hidden || !state.selectedCodeBlock.isConnected) return;
        positionFloatingElement(context.codeToolbar, state.selectedCodeBlock.getBoundingClientRect());
    }

    function exitCodeBlock() {
        if (!state.selectedCodeBlock) return;
        const next = actions.ensureEditableBlockAfter(state.selectedCodeBlock, true);
        hideCodeToolbar();
        if (next) {
            actions.rememberSelection();
            actions.updateBlockMenu();
        }
    }

    function positionFloatingElement(element, rect) {
        const width = Math.min(element.scrollWidth, window.innerWidth - 16);
        element.style.maxWidth = `${window.innerWidth - 16}px`;
        element.style.left = `${Math.max(8, Math.min(window.innerWidth - width - 8, rect.left + rect.width / 2 - width / 2))}px`;
        element.style.top = `${Math.max(8, rect.top - element.offsetHeight - 10)}px`;
    }

    function setupUrlDialog() {
        const dialog = context.app.querySelector('[data-url-dialog]');
        const input = dialog?.querySelector('[data-url-value]');
        const error = dialog?.querySelector('[data-url-error]');
        dialog?.querySelectorAll('[data-dialog-close]').forEach((button) => button.addEventListener('click', () => { dialog.hidden = true; }));
        dialog?.querySelector('[data-url-apply]')?.addEventListener('click', () => {
            const type = dialog.dataset.type || 'embed';
            const result = actions.embedFromUrl(input?.value || '', type);
            if (!result) {
                if (error) { error.textContent = 'URL belum valid atau providernya belum didukung.'; error.hidden = false; }
                return;
            }
            if (error) error.hidden = true;
            actions.insertNode(result);
            actions.ensureEditableBlockAfter(result, true);
            dialog.hidden = true;
            actions.changed();
        });
    }

    function openUrlDialog(type) {
        const dialog = context.app.querySelector('[data-url-dialog]');
        if (!dialog) return;
        dialog.dataset.type = type;
        dialog.querySelector('[data-url-eyebrow]').textContent = type === 'video' ? 'Video' : 'Embed';
        dialog.querySelector('[data-url-title]').textContent = type === 'video' ? 'Tempel URL video' : 'Tempel URL media';
        dialog.querySelector('[data-url-value]').value = '';
        dialog.querySelector('[data-url-error]').hidden = true;
        dialog.hidden = false;
        window.setTimeout(() => dialog.querySelector('[data-url-value]').focus(), 0);
    }

    return { setImageLayout, setImageAlignment, positionImageToolbar, showCodeToolbar, hideCodeToolbar, positionCodeToolbar, exitCodeBlock, positionFloatingElement, setupUrlDialog, openUrlDialog };
}
