export function createSelectionContext(root, editableSelector, state) {
    const activeDocument = () => root.querySelector('[data-document-language].is-active:not([hidden])')
        || root.querySelector('[data-document-language]:not([hidden])');

    const activeEditor = () => activeDocument()?.querySelector('[data-editor]');

    const closestBlock = (node, editor) => {
        const element = node instanceof Element ? node : node?.parentElement;
        const block = element?.closest(editableSelector);
        return block && editor?.contains(block) ? block : null;
    };

    const firstEditableBlock = (editor) => {
        if (!editor) return null;

        let block = editor.querySelector(editableSelector);

        if (!block) {
            block = document.createElement('p');
            block.append(document.createElement('br'));
            editor.append(block);
        }

        return block;
    };

    const currentRange = (editor, requireText = false) => {
        const selection = window.getSelection();

        if (!editor || !selection || selection.rangeCount === 0) return null;

        const range = selection.getRangeAt(0);
        const startInside = editor.contains(range.startContainer);
        const endInside = editor.contains(range.endContainer);

        if (!startInside || !endInside) return null;
        if (requireText && (range.collapsed || selection.toString().trim() === '')) return null;

        return range;
    };

    const rangeRect = (range) => {
        if (!range) return null;

        const rects = Array.from(range.getClientRects()).filter((rect) => rect.width > 0 || rect.height > 0);

        if (rects.length > 0) {
            const first = rects[0];
            const last = rects[rects.length - 1];

            return {
                left: Math.min(...rects.map((rect) => rect.left)),
                right: Math.max(...rects.map((rect) => rect.right)),
                top: Math.min(...rects.map((rect) => rect.top)),
                bottom: Math.max(...rects.map((rect) => rect.bottom)),
                width: Math.max(first.width, last.width),
                height: Math.max(1, Math.max(...rects.map((rect) => rect.height))),
            };
        }

        const rect = range.getBoundingClientRect();
        return rect.width || rect.height ? rect : null;
    };

    const placeCaret = (block) => {
        const editor = activeEditor();
        if (!block || !editor || !editor.contains(block)) return;

        const selection = window.getSelection();
        const range = document.createRange();
        range.selectNodeContents(block);
        range.collapse(false);
        selection?.removeAllRanges();
        selection?.addRange(range);
        editor.focus({ preventScroll: true });
        state.activeBlock = block;
    };

    const resolveActiveBlock = () => {
        const editor = activeEditor();
        if (!editor) return null;

        const range = currentRange(editor);
        const selectedBlock = range ? closestBlock(range.startContainer, editor) : null;

        if (selectedBlock) {
            state.activeBlock = selectedBlock;
            return selectedBlock;
        }

        if (state.activeBlock?.isConnected && editor.contains(state.activeBlock)) {
            return state.activeBlock;
        }

        state.activeBlock = firstEditableBlock(editor);
        return state.activeBlock;
    };

    return {
        activeDocument,
        activeEditor,
        closestBlock,
        currentRange,
        firstEditableBlock,
        placeCaret,
        rangeRect,
        resolveActiveBlock,
    };
}

export function initializeSelectionContext(state, activeEditor, firstEditableBlock, update) {
    requestAnimationFrame(() => {
        state.activeBlock = firstEditableBlock(activeEditor());
        update();
    });

    window.setTimeout(() => {
        state.activeBlock = state.activeBlock || firstEditableBlock(activeEditor());
        update();
    }, 120);
}
