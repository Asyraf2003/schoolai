import {
    createSelectionContext,
    initializeSelectionContext,
} from './article-canvas-context-ui/selection-context.js';

const root = document.querySelector('[data-article-canvas]');

if (root) {
    const blockMenu = root.querySelector('[data-block-menu]');
    const blockToggle = root.querySelector('[data-block-toggle]');
    const blockActions = root.querySelector('[data-block-actions]');
    const inlineToolbar = root.querySelector('[data-inline-toolbar]');
    const editableSelector = 'p,h2,h3,blockquote,li,pre,figure,div.article-embed,div.article-video';
    const state = { activeBlock: null };
    let frame = null;
    const {
        activeDocument,
        activeEditor,
        closestBlock,
        currentRange,
        firstEditableBlock,
        placeCaret,
        rangeRect,
        resolveActiveBlock,
    } = createSelectionContext(root, editableSelector, state);

    const hideInlineToolbar = () => {
        if (!inlineToolbar) return;
        inlineToolbar.hidden = true;
        inlineToolbar.style.removeProperty('width');
        inlineToolbar.style.removeProperty('max-width');
    };

    const updateInlineToolbar = () => {
        const editor = activeEditor();
        const range = currentRange(editor, true);

        if (!inlineToolbar || !editor || !range) {
            hideInlineToolbar();
            return;
        }

        const rect = rangeRect(range);

        if (!rect) {
            hideInlineToolbar();
            return;
        }

        inlineToolbar.hidden = false;
        inlineToolbar.style.pointerEvents = 'auto';
        inlineToolbar.style.maxWidth = `${Math.max(240, window.innerWidth - 16)}px`;

        const width = Math.min(Math.max(inlineToolbar.scrollWidth, 240), window.innerWidth - 16);
        const height = Math.max(inlineToolbar.offsetHeight, 44);
        const center = (rect.left + rect.right) / 2;
        const left = Math.max(8, Math.min(window.innerWidth - width - 8, center - width / 2));
        const above = rect.top - height - 10;
        const top = above >= 8 ? above : Math.min(window.innerHeight - height - 8, rect.bottom + 10);

        inlineToolbar.style.width = `${width}px`;
        inlineToolbar.style.left = `${left}px`;
        inlineToolbar.style.top = `${Math.max(8, top)}px`;
    };

    const updateBlockMenu = () => {
        const documentSection = activeDocument();
        const editor = activeEditor();
        const block = resolveActiveBlock();

        if (!blockMenu || !documentSection || !editor || !block) {
            if (blockMenu) blockMenu.hidden = true;
            return;
        }

        if (['FIGURE', 'PRE'].includes(block.tagName) || block.querySelector('img, iframe')) {
            blockMenu.hidden = true;
            return;
        }

        const workspace = root.querySelector('.canvas-workspace');
        const workspaceRect = workspace?.getBoundingClientRect();
        const documentRect = documentSection.getBoundingClientRect();
        const blockRect = block.getBoundingClientRect();

        if (!workspaceRect) return;

        blockMenu.hidden = false;
        blockMenu.style.pointerEvents = 'auto';
        blockMenu.classList.toggle('is-on-filled-block', (block.textContent || '').trim() !== '');
        blockMenu.style.top = `${blockRect.top - workspaceRect.top + Math.max(0, (blockRect.height - 34) / 2)}px`;

        if (window.innerWidth > 850) {
            const rtl = documentSection.getAttribute('dir') === 'rtl';
            const desiredLeft = rtl
                ? documentRect.right - workspaceRect.left + 16
                : documentRect.left - workspaceRect.left - 50;

            blockMenu.style.left = `${Math.max(8, Math.min(workspaceRect.width - 42, desiredLeft))}px`;
        } else {
            blockMenu.style.removeProperty('left');
        }
    };

    const ensureCaretForBlockMenu = () => {
        const editor = activeEditor();
        if (!editor) return;

        if (!currentRange(editor)) {
            placeCaret(resolveActiveBlock());
        }
    };

    const update = () => {
        frame = null;
        updateInlineToolbar();
        updateBlockMenu();
    };

    const scheduleUpdate = (delay = 0) => {
        window.clearTimeout(scheduleUpdate.timer);
        scheduleUpdate.timer = window.setTimeout(() => {
            if (frame !== null) cancelAnimationFrame(frame);
            frame = requestAnimationFrame(update);
        }, delay);
    };

    root.addEventListener('pointerdown', (event) => {
        const editor = event.target?.closest?.('[data-editor]');
        if (!editor || editor !== activeEditor()) return;

        state.activeBlock = closestBlock(event.target, editor) || state.activeBlock || firstEditableBlock(editor);
    }, true);

    blockToggle?.addEventListener('mousedown', (event) => {
        ensureCaretForBlockMenu();
        event.preventDefault();
    }, true);

    blockToggle?.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopImmediatePropagation();
        ensureCaretForBlockMenu();

        const open = blockActions?.hidden ?? true;
        if (blockActions) blockActions.hidden = !open;
        blockToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        updateBlockMenu();
    }, true);

    document.addEventListener('selectionchange', () => {
        const editor = activeEditor();
        const range = currentRange(editor);

        if (range) {
            state.activeBlock = closestBlock(range.startContainer, editor) || state.activeBlock;
        }

        scheduleUpdate();
        scheduleUpdate(32);
    });

    document.addEventListener('pointerup', () => {
        scheduleUpdate();
        scheduleUpdate(40);
    });

    root.addEventListener('keyup', () => scheduleUpdate());
    root.addEventListener('focusin', (event) => {
        const editor = event.target?.closest?.('[data-editor]');
        if (editor && editor === activeEditor()) {
            state.activeBlock = closestBlock(window.getSelection()?.anchorNode, editor) || state.activeBlock || firstEditableBlock(editor);
        }
        scheduleUpdate();
    });

    root.addEventListener('click', (event) => {
        if (event.target?.closest?.('[data-language]')) {
            state.activeBlock = null;
            scheduleUpdate(0);
            scheduleUpdate(60);
        }
    });

    document.addEventListener('pointerdown', (event) => {
        if (!inlineToolbar?.contains(event.target) && !activeEditor()?.contains(event.target)) {
            hideInlineToolbar();
        }

        if (!blockMenu?.contains(event.target) && !activeEditor()?.contains(event.target) && blockActions) {
            blockActions.hidden = true;
            blockToggle?.setAttribute('aria-expanded', 'false');
        }
    });

    window.addEventListener('resize', () => scheduleUpdate());
    window.addEventListener('scroll', () => scheduleUpdate(), { passive: true });

    initializeSelectionContext(state, activeEditor, firstEditableBlock, update);
}
