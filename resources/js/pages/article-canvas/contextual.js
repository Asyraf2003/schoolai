import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function createContextualActions(context, state, actions) {
    function syncContextualControls() {
        const blocks = actions.selectedBlocks();
        const first = blocks[0];
        const stateMap = {
            paragraph: first?.tagName === 'P',
            h2: first?.tagName === 'H2',
            h3: first?.tagName === 'H3',
            quote: first?.tagName === 'BLOCKQUOTE',
            small: blocks.length > 0 && blocks.every((block) => block.classList.contains('article-text-small')),
            large: blocks.length > 0 && blocks.every((block) => block.classList.contains('article-text-large')),
            'align-left': blocks.length > 0 && blocks.every((block) => !block.classList.contains('article-align-center') && !block.classList.contains('article-align-right') && !block.classList.contains('article-align-justify')),
            'align-center': blocks.length > 0 && blocks.every((block) => block.classList.contains('article-align-center')),
            'align-right': blocks.length > 0 && blocks.every((block) => block.classList.contains('article-align-right')),
            'align-justify': blocks.length > 0 && blocks.every((block) => block.classList.contains('article-align-justify')),
        };

        try {
            stateMap.bold = document.queryCommandState('bold');
            stateMap.italic = document.queryCommandState('italic');
            stateMap.strike = document.queryCommandState('strikeThrough');
        } catch {
            // queryCommandState is not available in a few embedded browsers.
        }

        context.app.querySelectorAll('[data-format]').forEach((button) => {
            const active = Boolean(stateMap[button.dataset.format]);
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });

        context.app.querySelectorAll('[data-block-color]').forEach((button) => {
            const color = button.dataset.blockColor;
            const active = color === 'default'
                ? blocks.length > 0 && blocks.every((block) => !context.textColorClasses.some((name) => block.classList.contains(`article-color--${name}`)))
                : blocks.length > 0 && blocks.every((block) => block.classList.contains(`article-color--${color}`));
            button.classList.toggle('is-active', active);
        });

        context.app.querySelectorAll('[data-block-background]').forEach((button) => {
            const color = button.dataset.blockBackground;
            const active = color === 'default'
                ? blocks.length > 0 && blocks.every((block) => !context.backgroundClasses.some((name) => block.classList.contains(`article-bg--${name}`)))
                : blocks.length > 0 && blocks.every((block) => block.classList.contains(`article-bg--${color}`));
            button.classList.toggle('is-active', active);
        });
    }

    function updateBlockMenu() {
        const editor = context.activeEditor();
        const selection = window.getSelection();
        if (!editor || !selection || selection.rangeCount === 0 || !editor.contains(selection.anchorNode)) {
            if (context.blockMenu) context.blockMenu.hidden = true;
            return;
        }

        const block = closestBlock(selection.anchorNode, editor);
        if (!block || block === editor || ['FIGURE', 'PRE'].includes(block.tagName) || block.querySelector('img, iframe')) {
            if (context.blockMenu) context.blockMenu.hidden = true;
            return;
        }

        const workspaceRect = context.app.querySelector('.canvas-workspace')?.getBoundingClientRect();
        const blockRect = block.getBoundingClientRect();
        if (!workspaceRect || !context.blockMenu) return;
        context.blockMenu.hidden = false;
        context.blockMenu.classList.toggle('is-on-filled-block', (block.textContent || '').trim() !== '');
        context.blockMenu.style.top = `${blockRect.top - workspaceRect.top + Math.max(0, (blockRect.height - 34) / 2)}px`;
        syncContextualControls();
    }

    function closeBlockActions() {
        if (context.blockActions) context.blockActions.hidden = true;
        context.blockToggle?.setAttribute('aria-expanded', 'false');
    }

    return { syncContextualControls, updateBlockMenu, closeBlockActions };
}
