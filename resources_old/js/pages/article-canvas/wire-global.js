import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function wireGlobalEvents(context, state, actions) {
    document.addEventListener('selectionchange', () => {
        const selection = window.getSelection();
        if (!selection || selection.rangeCount === 0) return;
        const editor = context.activeEditor();
        if (editor?.contains(selection.anchorNode)) actions.rememberSelection();
    });

    document.addEventListener('click', (event) => {
        if (!context.inlineToolbar?.contains(event.target) && !context.activeEditor()?.contains(event.target)) {
            actions.hideInlineToolbar();
        }
        if (!context.imageToolbar?.contains(event.target) && !(event.target instanceof HTMLImageElement) && !event.target?.closest?.('figure')) {
            actions.hideImageToolbar();
        }
        if (!context.codeToolbar?.contains(event.target) && !event.target?.closest?.('pre')) {
            actions.hideCodeToolbar();
        }
        if (!context.blockMenu?.contains(event.target) && !context.activeEditor()?.contains(event.target)) {
            actions.closeBlockActions();
        }
    });

    window.addEventListener('resize', () => {
        if (state.selectedFigure) actions.positionImageToolbar(state.selectedFigure);
        actions.positionCodeToolbar();
    });

    window.addEventListener('beforeunload', (event) => {
        if (!state.dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });
}
