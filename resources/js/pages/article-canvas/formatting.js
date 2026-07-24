import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function createFormattingActions(context, state, actions) {
    function applyFormat(format) {
        actions.restoreSelection();
        context.activeEditor()?.focus();

        if (format === 'bold' || format === 'italic') {
            document.execCommand(format, false);
        } else if (format === 'strike') {
            document.execCommand('strikeThrough', false);
        } else if (format === 'highlight') {
            toggleInlineElement('mark');
        } else if (format === 'link') {
            openLinkInput();
            return;
        } else if (format === 'paragraph' || format === 'h2' || format === 'h3') {
            setBlockType(format === 'paragraph' ? 'p' : format);
        } else if (format === 'small' || format === 'large') {
            toggleBlockClass(`article-text-${format}`, ['article-text-small', 'article-text-large']);
        } else if (format === 'align-left') {
            toggleBlockClass('', ['article-align-center', 'article-align-right', 'article-align-justify']);
        } else if (['align-center', 'align-right', 'align-justify'].includes(format)) {
            toggleBlockClass(`article-${format}`, ['article-align-center', 'article-align-right', 'article-align-justify']);
        } else if (format === 'quote') {
            toggleQuoteBlocks();
        }

        actions.changed();
        actions.rememberSelection();
        actions.syncContextualControls();
        if (!window.getSelection()?.isCollapsed) actions.updateInlineToolbar();
    }

    function setBlockType(tagName) {
        const blocks = actions.selectedBlocks().filter((block) => ['P', 'H2', 'H3', 'BLOCKQUOTE'].includes(block.tagName));
        let last = null;

        blocks.forEach((block) => {
            if (block.tagName.toLowerCase() === tagName) {
                last = block;
                return;
            }
            const replacement = document.createElement(tagName);
            replacement.innerHTML = block.innerHTML;
            replacement.className = block.className;
            replacement.classList.remove('article-quote', 'article-pull-quote');
            if (tagName !== 'p') replacement.classList.remove('has-drop-cap');
            block.replaceWith(replacement);
            last = replacement;
        });

        if (last) {
            placeCaret(last, true);
            actions.rememberSelection();
        }
    }

    function toggleQuoteBlocks() {
        const blocks = actions.selectedBlocks().filter((block) => ['P', 'H2', 'H3', 'BLOCKQUOTE'].includes(block.tagName));
        if (blocks.length === 1 && blocks[0].tagName === 'BLOCKQUOTE') {
            const block = blocks[0];
            if (block.classList.contains('article-pull-quote')) {
                replaceBlock(block, 'p');
            } else {
                block.classList.remove('article-quote');
                block.classList.add('article-pull-quote');
            }
            return;
        }

        const allQuotes = blocks.length > 0 && blocks.every((block) => block.tagName === 'BLOCKQUOTE');
        blocks.forEach((block) => {
            const replacement = replaceBlock(block, allQuotes ? 'p' : 'blockquote');
            if (!allQuotes) replacement?.classList.add('article-quote');
        });
    }

    function replaceBlock(block, tagName) {
        const replacement = document.createElement(tagName);
        replacement.innerHTML = block.innerHTML;
        replacement.className = block.className;
        replacement.classList.remove('article-quote', 'article-pull-quote');
        block.replaceWith(replacement);
        placeCaret(replacement, true);
        actions.rememberSelection();
        return replacement;
    }

    function toggleBlockClass(className, mutuallyExclusive = []) {
        const blocks = actions.selectedBlocks().filter((block) => !['PRE'].includes(block.tagName));
        if (blocks.length === 0) return;
        const removeOnly = className !== '' && blocks.every((block) => block.classList.contains(className));

        blocks.forEach((block) => {
            mutuallyExclusive.forEach((candidate) => block.classList.remove(candidate));
            if (className && !removeOnly) block.classList.add(className);
        });
    }

    function applyBlockPalette(prefix, color, palette) {
        actions.restoreSelection();
        const blocks = actions.selectedBlocks().filter((block) => !['PRE'].includes(block.tagName));
        if (blocks.length === 0) return;
        blocks.forEach((block) => {
            palette.forEach((name) => block.classList.remove(`${prefix}${name}`));
            if (color && color !== 'default') block.classList.add(`${prefix}${color}`);
        });
        actions.changed();
        actions.syncContextualControls();
    }

    function applyTextColor(color) {
        actions.restoreSelection();
        const selection = window.getSelection();
        const editor = context.activeEditor();
        if (!selection || selection.rangeCount === 0 || !editor?.contains(selection.anchorNode)) return;
        const range = selection.getRangeAt(0);
        const blocks = actions.selectedBlocks();
        const startBlock = closestBlock(range.startContainer, editor);
        const endBlock = closestBlock(range.endContainer, editor);

        if (range.collapsed || blocks.length > 1 || startBlock !== endBlock) {
            applyBlockPalette('article-color--', color, context.textColorClasses);
            return;
        }

        const wrapper = document.createElement('span');
        wrapper.className = `article-color--${color || 'default'}`;
        wrapper.append(range.extractContents());
        range.actions.insertNode(wrapper);
        selection.removeAllRanges();
        const nextRange = document.createRange();
        nextRange.selectNodeContents(wrapper);
        selection.addRange(nextRange);
        actions.rememberSelection();
        if (context.inlineColors) context.inlineColors.hidden = true;
        actions.changed();
        actions.updateInlineToolbar();
    }

    function toggleInlineElement(tagName) {
        const selection = window.getSelection();
        const editor = context.activeEditor();
        if (!selection || selection.rangeCount === 0 || selection.isCollapsed || !editor?.contains(selection.anchorNode)) return;
        const range = selection.getRangeAt(0);
        const existing = (range.commonAncestorContainer instanceof Element
            ? range.commonAncestorContainer
            : range.commonAncestorContainer.parentElement)?.closest(tagName);

        if (existing && editor.contains(existing)) {
            existing.replaceWith(...existing.childNodes);
            return;
        }

        const wrapper = document.createElement(tagName);
        wrapper.append(range.extractContents());
        range.actions.insertNode(wrapper);
        selection.removeAllRanges();
        const nextRange = document.createRange();
        nextRange.selectNodeContents(wrapper);
        selection.addRange(nextRange);
    }

    function openLinkInput() {
        if (!context.linkInput || !context.inlineToolbar) return;
        const input = context.linkInput.querySelector('input');
        context.linkInput.hidden = false;
        context.linkInput.style.left = context.inlineToolbar.style.left || '12px';
        context.linkInput.style.top = context.inlineToolbar.style.top || '68px';
        context.inlineToolbar.hidden = true;
        input.value = '';
        input.focus();
        input.onkeydown = (event) => {
            if (event.key === 'Escape') {
                context.linkInput.hidden = true;
                return;
            }
            if (event.key !== 'Enter') return;
            event.preventDefault();
            actions.restoreSelection();
            if (isSafeHttpUrl(input.value)) document.execCommand('createLink', false, input.value.trim());
            context.linkInput.hidden = true;
            actions.changed();
        };
    }

    return { applyFormat, setBlockType, toggleQuoteBlocks, replaceBlock, toggleBlockClass, applyBlockPalette, applyTextColor, toggleInlineElement, openLinkInput };
}
