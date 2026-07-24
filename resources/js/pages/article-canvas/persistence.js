import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function createPersistenceActions(context, state, actions) {
    function changed() {
        state.dirty = true;
        showTransientSaveState('Draft · Menyimpan…', 'is-saving');
        window.clearTimeout(state.autosaveTimer);
        state.autosaveTimer = window.setTimeout(saveNow, 900);
    }

    function showTransientSaveState(label, modifier = 'is-saving') {
        if (!context.saveState) return;
        context.saveState.textContent = label;
        context.saveState.className = `canvas-save-state ${modifier}`.trim();
    }

    async function saveNow() {
        if (state.saving) return false;
        state.saving = true;
        window.clearTimeout(state.autosaveTimer);

        const payload = { thumbnail_url: state.thumbnailUrl };
        context.documents.forEach((document) => {
            const language = document.dataset.documentLanguage;
            payload[`title_${language}`] = document.querySelector('[data-title]')?.value || '';
            payload[`subtitle_${language}`] = document.querySelector('[data-subtitle]')?.value || '';
            payload[`content_${language}`] = document.querySelector('[data-editor]')?.innerHTML || '';
        });

        try {
            const response = await jsonRequest(context.app.dataset.autosaveUrl, 'PATCH', payload, context.csrf);
            state.dirty = false;
            state.thumbnailUrl = response.thumbnail_url || state.thumbnailUrl;
            context.app.dataset.thumbnailUrl = state.thumbnailUrl;
            if (context.saveState) {
                context.saveState.textContent = response.saved_label || 'Draft · Tersimpan';
                context.saveState.className = 'canvas-save-state';
                context.saveState.removeAttribute('title');
            }
            updateMetrics(response.word_count, response.character_count);
            actions.refreshPreview();
            return true;
        } catch (error) {
            if (context.saveState) {
                context.saveState.textContent = 'Draft · Gagal menyimpan';
                context.saveState.className = 'canvas-save-state is-error';
                context.saveState.title = error.message;
            }
            return false;
        } finally {
            state.saving = false;
        }
    }

    function updateMetrics(serverWords = null, serverCharacters = null) {
        const editor = context.activeEditor();
        const text = (editor?.textContent || '').trim().replace(/\s+/g, ' ');
        state.currentWords = serverWords ?? (text ? (text.match(/[\p{L}\p{N}]+(?:[’'\-][\p{L}\p{N}]+)*/gu) || []).length : 0);
        state.currentCharacters = serverCharacters ?? text.length;
        if (context.countToggle) context.countToggle.textContent = `${state.currentWords} kata`;
        if (context.wordCount) context.wordCount.textContent = `${state.currentWords} kata`;
        if (context.characterCount) context.characterCount.textContent = `${state.currentCharacters} karakter`;
        actions.refreshPreview();
    }

    function rememberSelection() {
        const selection = window.getSelection();
        const editor = context.activeEditor();
        if (!selection || selection.rangeCount === 0 || !editor?.contains(selection.anchorNode)) return;
        state.savedRange = selection.getRangeAt(0).cloneRange();
    }

    function restoreSelection() {
        const editor = context.activeEditor();
        if (!state.savedRange || !editor?.contains(state.savedRange.startContainer)) return false;
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(state.savedRange);
        return true;
    }

    function selectedBlocks(includeFigures = false) {
        const editor = context.activeEditor();
        if (!editor) return [];
        const selection = window.getSelection();
        const range = selection?.rangeCount ? selection.getRangeAt(0) : state.savedRange;
        if (!range || !editor.contains(range.startContainer)) return [];

        const selector = includeFigures
            ? 'p,h2,h3,blockquote,li,pre,figure'
            : 'p,h2,h3,blockquote,li,pre';
        const candidates = Array.from(editor.querySelectorAll(selector));

        if (range.collapsed) {
            const block = closestBlock(range.startContainer, editor);
            return block && block !== editor && (includeFigures || block.tagName !== 'FIGURE') ? [block] : [];
        }

        return candidates.filter((block) => {
            try {
                return range.intersectsNode(block);
            } catch {
                return false;
            }
        });
    }

    function updateInlineToolbar() {
        const selection = window.getSelection();
        const editor = context.activeEditor();
        if (!context.inlineToolbar || !selection || selection.rangeCount === 0 || selection.isCollapsed || !editor?.contains(selection.anchorNode)) {
            hideInlineToolbar();
            return;
        }

        const rect = selection.getRangeAt(0).getBoundingClientRect();
        if (!rect.width && !rect.height) return;
        context.inlineToolbar.hidden = false;
        if (context.inlineColors) context.inlineColors.hidden = true;
        const width = Math.min(context.inlineToolbar.scrollWidth, window.innerWidth - 16);
        context.inlineToolbar.style.width = `${width}px`;
        context.inlineToolbar.style.left = `${Math.max(8, Math.min(window.innerWidth - width - 8, rect.left + rect.width / 2 - width / 2))}px`;
        context.inlineToolbar.style.top = `${Math.max(8, rect.top - context.inlineToolbar.offsetHeight - 10)}px`;
        actions.syncContextualControls();
    }

    function hideInlineToolbar() {
        if (context.inlineToolbar) {
            context.inlineToolbar.hidden = true;
            context.inlineToolbar.style.removeProperty('width');
        }
        if (context.inlineColors) context.inlineColors.hidden = true;
        if (context.linkInput) context.linkInput.hidden = true;
    }

    return { changed, showTransientSaveState, saveNow, updateMetrics, rememberSelection, restoreSelection, selectedBlocks, updateInlineToolbar, hideInlineToolbar };
}
