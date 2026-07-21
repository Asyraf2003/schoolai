const root = document.querySelector('[data-article-canvas]');

if (root) {
    const blockMenu = root.querySelector('[data-block-menu]');
    const inlineToolbar = root.querySelector('[data-inline-toolbar]');
    let frame = null;

    const activeDocument = () => root.querySelector('[data-document-language].is-active:not([hidden])')
        || root.querySelector('[data-document-language]:not([hidden])');
    const activeEditor = () => activeDocument()?.querySelector('[data-editor]');

    const closestBlock = (node, editor) => {
        const element = node instanceof Element ? node : node?.parentElement;
        const block = element?.closest('p,h2,h3,blockquote,li,pre,figure,div.article-embed,div.article-video');
        return block && editor?.contains(block) ? block : null;
    };

    const hideInlineToolbar = () => {
        if (inlineToolbar) inlineToolbar.hidden = true;
    };

    const updateInlineToolbar = () => {
        const editor = activeEditor();
        const selection = window.getSelection();

        if (
            !inlineToolbar
            || !editor
            || !selection
            || selection.rangeCount === 0
            || selection.isCollapsed
            || !editor.contains(selection.anchorNode)
            || !editor.contains(selection.focusNode)
        ) {
            hideInlineToolbar();
            return;
        }

        const rect = selection.getRangeAt(0).getBoundingClientRect();
        if (!rect.width && !rect.height) {
            hideInlineToolbar();
            return;
        }

        inlineToolbar.hidden = false;
        const width = Math.min(inlineToolbar.scrollWidth, window.innerWidth - 16);
        inlineToolbar.style.width = `${width}px`;
        inlineToolbar.style.left = `${Math.max(8, Math.min(window.innerWidth - width - 8, rect.left + rect.width / 2 - width / 2))}px`;
        inlineToolbar.style.top = `${Math.max(8, rect.top - inlineToolbar.offsetHeight - 10)}px`;
    };

    const updateBlockMenu = () => {
        const documentSection = activeDocument();
        const editor = activeEditor();
        const selection = window.getSelection();

        if (!blockMenu || !documentSection || !editor || !selection || selection.rangeCount === 0 || !editor.contains(selection.anchorNode)) {
            if (blockMenu) blockMenu.hidden = true;
            return;
        }

        let block = closestBlock(selection.anchorNode, editor);

        if (!block && selection.isCollapsed) {
            const range = selection.getRangeAt(0);
            if (range.startContainer === editor) {
                const child = editor.childNodes[Math.min(range.startOffset, Math.max(0, editor.childNodes.length - 1))]
                    || editor.firstChild;
                block = closestBlock(child, editor);
            }
        }

        if (!block || ['FIGURE', 'PRE'].includes(block.tagName) || block.querySelector('img, iframe')) {
            blockMenu.hidden = true;
            return;
        }

        const workspace = root.querySelector('.canvas-workspace');
        const workspaceRect = workspace?.getBoundingClientRect();
        const documentRect = documentSection.getBoundingClientRect();
        const blockRect = block.getBoundingClientRect();

        if (!workspaceRect) return;

        blockMenu.hidden = false;
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

    const update = () => {
        frame = null;
        updateInlineToolbar();
        updateBlockMenu();
    };

    const scheduleUpdate = () => {
        if (frame !== null) cancelAnimationFrame(frame);
        frame = requestAnimationFrame(update);
    };

    document.addEventListener('selectionchange', scheduleUpdate);
    root.addEventListener('mouseup', scheduleUpdate);
    root.addEventListener('keyup', scheduleUpdate);
    root.addEventListener('focusin', scheduleUpdate);
    root.addEventListener('click', scheduleUpdate);
    window.addEventListener('resize', scheduleUpdate);
    window.addEventListener('scroll', scheduleUpdate, { passive: true });
}
