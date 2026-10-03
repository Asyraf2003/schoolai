import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function createShortcutActions(context, state, actions) {
    function handleEditorShortcut(event) {
        const editor = context.activeEditor();
        const block = closestBlock(window.getSelection()?.anchorNode, editor);

        if (block?.tagName === 'PRE' && event.key === 'Enter' && !event.shiftKey) {
            const code = block.querySelector('code') || block;
            const shouldExit = event.ctrlKey || event.metaKey || (isCaretAtEnd(code) && (code.textContent || '').endsWith('\n'));
            if (shouldExit) {
                event.preventDefault();
                state.selectedCodeBlock = block;
                actions.exitCodeBlock();
                return;
            }
        }

        if (!(event.ctrlKey || event.metaKey)) return;
        const key = event.key.toLowerCase();
        if (key === 'b' || key === 'i') {
            event.preventDefault();
            document.execCommand(key === 'b' ? 'bold' : 'italic', false);
            actions.changed();
        }
        if (key === 'x' && event.shiftKey) {
            event.preventDefault();
            document.execCommand('strikeThrough', false);
            actions.changed();
        }
        if (key === 'k') {
            event.preventDefault();
            actions.rememberSelection();
            actions.openLinkInput();
        }
    }

    function handleMarkdownShortcut(event, editor) {
        if (event.key !== ' ' && event.key !== 'Enter') return;
        const block = closestBlock(window.getSelection()?.anchorNode, editor);
        if (!block || block.tagName === 'PRE') return;
        const text = (block.textContent || '').replace(/\u00a0/g, ' ');
        if (event.key === ' ' && ['* ', '- '].includes(text)) {
            block.textContent = '';
            document.execCommand('insertUnorderedList', false);
            actions.changed();
        } else if (event.key === ' ' && text === '1. ') {
            block.textContent = '';
            document.execCommand('insertOrderedList', false);
            actions.changed();
        } else if (text.trim() === '```') {
            const pre = document.createElement('pre');
            const code = document.createElement('code');
            code.append(document.createElement('br'));
            pre.append(code);
            block.replaceWith(pre);
            ensureEditableBlockAfter(pre, false);
            placeCaret(code);
            actions.showCodeToolbar(pre);
            actions.changed();
        } else if (event.key === ' ') {
            applyInlineCode(block);
        }
    }

    function applyInlineCode(block) {
        if (block.children.length > 0) return;
        const text = block.textContent || '';
        const match = text.match(/`([^`\n]+)`\s$/);
        if (!match) return;
        const before = text.slice(0, match.index);
        const after = text.slice((match.index || 0) + match[0].length);
        const code = document.createElement('code');
        code.textContent = match[1];
        block.replaceChildren(document.createTextNode(before), code, document.createTextNode(` ${after}`));
        placeCaret(block, true);
        actions.changed();
    }

    function insertNode(node) {
        actions.restoreSelection();
        const editor = context.activeEditor();
        const selection = window.getSelection();
        if (!editor) return null;
        if (!selection || selection.rangeCount === 0 || !editor.contains(selection.anchorNode)) {
            editor.append(node);
            return node;
        }
        const block = closestBlock(selection.anchorNode, editor);
        if (block && (block.textContent || '').trim() === '' && block.tagName === 'P') {
            block.replaceWith(node);
        } else if (block && block !== editor) {
            block.after(node);
        } else {
            selection.getRangeAt(0).insertNode(node);
        }
        return node;
    }

    function ensureEditableBlockAfter(node, focus = true) {
        const editableTags = ['P', 'H2', 'H3', 'BLOCKQUOTE', 'UL', 'OL'];
        let next = node.nextElementSibling;
        if (!next || !editableTags.includes(next.tagName)) {
            next = document.createElement('p');
            next.append(document.createElement('br'));
            node.after(next);
        }
        if (focus) placeCaret(next);
        return next;
    }

    function normalizeEmptyEditor(editor) {
        if ((editor.textContent || '').trim() !== '' || editor.querySelector('img, iframe, hr')) return;
        editor.replaceChildren();
        const paragraph = document.createElement('p');
        paragraph.append(document.createElement('br'));
        editor.append(paragraph);
    }

    return { handleEditorShortcut, handleMarkdownShortcut, applyInlineCode, insertNode, ensureEditableBlockAfter, normalizeEmptyEditor };
}
