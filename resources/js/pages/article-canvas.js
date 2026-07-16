const root = document.querySelector('[data-article-canvas]');

if (root) {
    mountCanvas(root);
}

function mountCanvas(app) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const documents = Array.from(app.querySelectorAll('[data-document-language]'));
    const languageButtons = Array.from(app.querySelectorAll('[data-language]'));
    const saveState = app.querySelector('[data-save-state]');
    const countToggle = app.querySelector('[data-count-toggle]');
    const countPopover = app.querySelector('[data-count-popover]');
    const wordCount = app.querySelector('[data-word-count]');
    const characterCount = app.querySelector('[data-character-count]');
    const inlineToolbar = app.querySelector('[data-inline-toolbar]');
    const inlineColors = app.querySelector('[data-inline-colors]');
    const linkInput = app.querySelector('[data-link-input]');
    const blockMenu = app.querySelector('[data-block-menu]');
    const blockToggle = app.querySelector('[data-block-toggle]');
    const blockActions = app.querySelector('[data-block-actions]');
    const imageInput = app.querySelector('[data-image-file]');
    const thumbnailInput = app.querySelector('[data-thumbnail-file]');
    const imageToolbar = app.querySelector('[data-image-toolbar]');
    const codeToolbar = app.querySelector('[data-code-toolbar]');
    const publishDrawer = app.querySelector('[data-publish-drawer]');
    const textColorClasses = ['muted', 'green', 'blue', 'red', 'amber'];
    const backgroundClasses = ['gray', 'yellow', 'green', 'blue', 'rose'];
    const imageLayoutClasses = ['compact', 'inline', 'outset', 'screen'];
    const imageAlignClasses = ['left', 'center', 'right'];
    let activeLanguage = 'id';
    let savedRange = null;
    let selectedFigure = null;
    let selectedCodeBlock = null;
    let imageUploadIntent = 'insert';
    let autosaveTimer = null;
    let dirty = false;
    let saving = false;
    let currentWords = 0;
    let currentCharacters = 0;
    let thumbnailUrl = app.dataset.thumbnailUrl || '';

    const activeDocument = () => documents.find((document) => document.dataset.documentLanguage === activeLanguage);
    const activeEditor = () => activeDocument()?.querySelector('[data-editor]');

    documents.forEach((document) => {
        const title = document.querySelector('[data-title]');
        const subtitle = document.querySelector('[data-subtitle]');
        const editor = document.querySelector('[data-editor]');

        [title, subtitle].forEach((field) => {
            if (!field) return;
            resizeTextarea(field);
            field.addEventListener('input', () => {
                resizeTextarea(field);
                changed();
            });
        });

        if (!editor) return;
        normalizeEmptyEditor(editor);
        editor.addEventListener('input', () => {
            changed();
            updateMetrics();
            updateBlockMenu();
            positionCodeToolbar();
        });
        editor.addEventListener('focus', () => {
            rememberSelection();
            updateBlockMenu();
        });
        editor.addEventListener('click', handleEditorClick);
        editor.addEventListener('keyup', (event) => {
            handleMarkdownShortcut(event, editor);
            rememberSelection();
            updateInlineToolbar();
            updateBlockMenu();
            positionCodeToolbar();
        });
        editor.addEventListener('mouseup', () => {
            rememberSelection();
            updateInlineToolbar();
            updateBlockMenu();
        });
        editor.addEventListener('paste', (event) => {
            event.preventDefault();
            const text = event.clipboardData?.getData('text/plain') || '';
            document.execCommand('insertText', false, text);
        });
        editor.addEventListener('keydown', handleEditorShortcut);
    });

    languageButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activeLanguage = button.dataset.language || 'id';
            languageButtons.forEach((candidate) => {
                const active = candidate === button;
                candidate.classList.toggle('is-active', active);
                candidate.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            documents.forEach((document) => {
                const active = document.dataset.documentLanguage === activeLanguage;
                document.hidden = !active;
                document.classList.toggle('is-active', active);
            });
            hideInlineToolbar();
            hideImageToolbar();
            hideCodeToolbar();
            updateMetrics();
            updateBlockMenu();
        });
    });

    countToggle?.addEventListener('click', () => {
        const open = countPopover?.hidden ?? true;
        if (countPopover) countPopover.hidden = !open;
        countToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    blockToggle?.addEventListener('mousedown', (event) => event.preventDefault());
    blockToggle?.addEventListener('click', () => {
        rememberSelection();
        const open = blockActions?.hidden ?? true;
        if (blockActions) blockActions.hidden = !open;
        blockToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        syncContextualControls();
    });

    app.querySelectorAll('[data-format]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => applyFormat(button.dataset.format));
    });

    app.querySelectorAll('[data-insert]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => insertBlock(button.dataset.insert));
    });

    app.querySelectorAll('[data-block-color]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => applyBlockPalette('article-color--', button.dataset.blockColor, textColorClasses));
    });

    app.querySelectorAll('[data-block-background]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => applyBlockPalette('article-bg--', button.dataset.blockBackground, backgroundClasses));
    });

    app.querySelector('[data-color-toggle]')?.addEventListener('mousedown', (event) => event.preventDefault());
    app.querySelector('[data-color-toggle]')?.addEventListener('click', () => {
        if (inlineColors) inlineColors.hidden = !inlineColors.hidden;
    });

    app.querySelectorAll('[data-text-color]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => applyTextColor(button.dataset.textColor));
    });

    imageInput?.addEventListener('change', async () => {
        const file = imageInput.files?.[0];
        if (!file) return;
        await uploadImage(file, 'content', imageUploadIntent);
        imageUploadIntent = 'insert';
        imageInput.value = '';
    });

    thumbnailInput?.addEventListener('change', async () => {
        const file = thumbnailInput.files?.[0];
        if (!file) return;
        await uploadImage(file, 'thumbnail');
        thumbnailInput.value = '';
    });

    app.querySelectorAll('[data-image-layout]').forEach((button) => {
        button.addEventListener('click', () => setImageLayout(button.dataset.imageLayout));
    });

    app.querySelectorAll('[data-image-align]').forEach((button) => {
        button.addEventListener('click', () => setImageAlignment(button.dataset.imageAlign));
    });

    app.querySelector('[data-image-alt]')?.addEventListener('click', () => {
        const image = selectedFigure?.querySelector('img');
        if (!image) return;
        const value = window.prompt('Alt text untuk aksesibilitas dan SEO:', image.alt || '');
        if (value === null) return;
        image.alt = value.slice(0, 300);
        changed();
    });

    app.querySelector('[data-image-thumbnail]')?.addEventListener('click', () => {
        const image = selectedFigure?.querySelector('img');
        if (!image) return;
        thumbnailUrl = image.getAttribute('src') || image.src;
        app.dataset.thumbnailUrl = thumbnailUrl;
        changed();
        refreshPreview();
        showTransientSaveState('Thumbnail dipilih · Menyimpan…');
    });

    app.querySelector('[data-image-replace]')?.addEventListener('click', () => {
        if (!selectedFigure) return;
        imageUploadIntent = 'replace';
        imageInput?.click();
    });

    app.querySelector('[data-image-continue]')?.addEventListener('click', () => {
        if (!selectedFigure) return;
        ensureEditableBlockAfter(selectedFigure, true);
        hideImageToolbar();
    });

    app.querySelector('[data-image-delete]')?.addEventListener('click', () => {
        if (!selectedFigure) return;
        const figure = selectedFigure;
        const next = ensureEditableBlockAfter(figure, false);
        figure.remove();
        hideImageToolbar();
        if (next) placeCaret(next);
        changed();
    });

    app.querySelector('[data-code-exit]')?.addEventListener('click', () => exitCodeBlock());
    app.querySelector('[data-thumbnail-change]')?.addEventListener('click', () => thumbnailInput?.click());

    setupUrlDialog();
    setupUnsplash();
    const categoryManager = setupCategories();
    setupPublish(categoryManager);

    document.addEventListener('selectionchange', () => {
        const selection = window.getSelection();
        if (!selection || selection.rangeCount === 0) return;
        const editor = activeEditor();
        if (editor?.contains(selection.anchorNode)) rememberSelection();
    });

    document.addEventListener('click', (event) => {
        if (!inlineToolbar?.contains(event.target) && !activeEditor()?.contains(event.target)) {
            hideInlineToolbar();
        }
        if (!imageToolbar?.contains(event.target) && !(event.target instanceof HTMLImageElement) && !event.target?.closest?.('figure')) {
            hideImageToolbar();
        }
        if (!codeToolbar?.contains(event.target) && !event.target?.closest?.('pre')) {
            hideCodeToolbar();
        }
        if (!blockMenu?.contains(event.target) && !activeEditor()?.contains(event.target)) {
            closeBlockActions();
        }
    });

    window.addEventListener('resize', () => {
        if (selectedFigure) positionImageToolbar(selectedFigure);
        positionCodeToolbar();
    });

    window.addEventListener('beforeunload', (event) => {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });

    updateMetrics();

    function changed() {
        dirty = true;
        showTransientSaveState('Draft · Menyimpan…', 'is-saving');
        window.clearTimeout(autosaveTimer);
        autosaveTimer = window.setTimeout(saveNow, 900);
    }

    function showTransientSaveState(label, modifier = 'is-saving') {
        if (!saveState) return;
        saveState.textContent = label;
        saveState.className = `canvas-save-state ${modifier}`.trim();
    }

    async function saveNow() {
        if (saving) return false;
        saving = true;
        window.clearTimeout(autosaveTimer);

        const payload = { thumbnail_url: thumbnailUrl };
        documents.forEach((document) => {
            const language = document.dataset.documentLanguage;
            payload[`title_${language}`] = document.querySelector('[data-title]')?.value || '';
            payload[`subtitle_${language}`] = document.querySelector('[data-subtitle]')?.value || '';
            payload[`content_${language}`] = document.querySelector('[data-editor]')?.innerHTML || '';
        });

        try {
            const response = await jsonRequest(app.dataset.autosaveUrl, 'PATCH', payload, csrf);
            dirty = false;
            thumbnailUrl = response.thumbnail_url || thumbnailUrl;
            app.dataset.thumbnailUrl = thumbnailUrl;
            if (saveState) {
                saveState.textContent = response.saved_label || 'Draft · Tersimpan';
                saveState.className = 'canvas-save-state';
                saveState.removeAttribute('title');
            }
            updateMetrics(response.word_count, response.character_count);
            refreshPreview();
            return true;
        } catch (error) {
            if (saveState) {
                saveState.textContent = 'Draft · Gagal menyimpan';
                saveState.className = 'canvas-save-state is-error';
                saveState.title = error.message;
            }
            return false;
        } finally {
            saving = false;
        }
    }

    function updateMetrics(serverWords = null, serverCharacters = null) {
        const editor = activeEditor();
        const text = (editor?.textContent || '').trim().replace(/\s+/g, ' ');
        currentWords = serverWords ?? (text ? (text.match(/[\p{L}\p{N}]+(?:[’'\-][\p{L}\p{N}]+)*/gu) || []).length : 0);
        currentCharacters = serverCharacters ?? text.length;
        if (countToggle) countToggle.textContent = `${currentWords} kata`;
        if (wordCount) wordCount.textContent = `${currentWords} kata`;
        if (characterCount) characterCount.textContent = `${currentCharacters} karakter`;
        refreshPreview();
    }

    function rememberSelection() {
        const selection = window.getSelection();
        const editor = activeEditor();
        if (!selection || selection.rangeCount === 0 || !editor?.contains(selection.anchorNode)) return;
        savedRange = selection.getRangeAt(0).cloneRange();
    }

    function restoreSelection() {
        const editor = activeEditor();
        if (!savedRange || !editor?.contains(savedRange.startContainer)) return false;
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(savedRange);
        return true;
    }

    function selectedBlocks(includeFigures = false) {
        const editor = activeEditor();
        if (!editor) return [];
        const selection = window.getSelection();
        const range = selection?.rangeCount ? selection.getRangeAt(0) : savedRange;
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
        const editor = activeEditor();
        if (!inlineToolbar || !selection || selection.rangeCount === 0 || selection.isCollapsed || !editor?.contains(selection.anchorNode)) {
            hideInlineToolbar();
            return;
        }

        const rect = selection.getRangeAt(0).getBoundingClientRect();
        if (!rect.width && !rect.height) return;
        inlineToolbar.hidden = false;
        if (inlineColors) inlineColors.hidden = true;
        const width = Math.min(inlineToolbar.scrollWidth, window.innerWidth - 16);
        inlineToolbar.style.width = `${width}px`;
        inlineToolbar.style.left = `${Math.max(8, Math.min(window.innerWidth - width - 8, rect.left + rect.width / 2 - width / 2))}px`;
        inlineToolbar.style.top = `${Math.max(8, rect.top - inlineToolbar.offsetHeight - 10)}px`;
        syncContextualControls();
    }

    function hideInlineToolbar() {
        if (inlineToolbar) {
            inlineToolbar.hidden = true;
            inlineToolbar.style.removeProperty('width');
        }
        if (inlineColors) inlineColors.hidden = true;
        if (linkInput) linkInput.hidden = true;
    }

    function applyFormat(format) {
        restoreSelection();
        activeEditor()?.focus();

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

        changed();
        rememberSelection();
        syncContextualControls();
        if (!window.getSelection()?.isCollapsed) updateInlineToolbar();
    }

    function setBlockType(tagName) {
        const blocks = selectedBlocks().filter((block) => ['P', 'H2', 'H3', 'BLOCKQUOTE'].includes(block.tagName));
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
            rememberSelection();
        }
    }

    function toggleQuoteBlocks() {
        const blocks = selectedBlocks().filter((block) => ['P', 'H2', 'H3', 'BLOCKQUOTE'].includes(block.tagName));
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
        rememberSelection();
        return replacement;
    }

    function toggleBlockClass(className, mutuallyExclusive = []) {
        const blocks = selectedBlocks().filter((block) => !['PRE'].includes(block.tagName));
        if (blocks.length === 0) return;
        const removeOnly = className !== '' && blocks.every((block) => block.classList.contains(className));

        blocks.forEach((block) => {
            mutuallyExclusive.forEach((candidate) => block.classList.remove(candidate));
            if (className && !removeOnly) block.classList.add(className);
        });
    }

    function applyBlockPalette(prefix, color, palette) {
        restoreSelection();
        const blocks = selectedBlocks().filter((block) => !['PRE'].includes(block.tagName));
        if (blocks.length === 0) return;
        blocks.forEach((block) => {
            palette.forEach((name) => block.classList.remove(`${prefix}${name}`));
            if (color && color !== 'default') block.classList.add(`${prefix}${color}`);
        });
        changed();
        syncContextualControls();
    }

    function applyTextColor(color) {
        restoreSelection();
        const selection = window.getSelection();
        const editor = activeEditor();
        if (!selection || selection.rangeCount === 0 || !editor?.contains(selection.anchorNode)) return;
        const range = selection.getRangeAt(0);
        const blocks = selectedBlocks();
        const startBlock = closestBlock(range.startContainer, editor);
        const endBlock = closestBlock(range.endContainer, editor);

        if (range.collapsed || blocks.length > 1 || startBlock !== endBlock) {
            applyBlockPalette('article-color--', color, textColorClasses);
            return;
        }

        const wrapper = document.createElement('span');
        wrapper.className = `article-color--${color || 'default'}`;
        wrapper.append(range.extractContents());
        range.insertNode(wrapper);
        selection.removeAllRanges();
        const nextRange = document.createRange();
        nextRange.selectNodeContents(wrapper);
        selection.addRange(nextRange);
        rememberSelection();
        if (inlineColors) inlineColors.hidden = true;
        changed();
        updateInlineToolbar();
    }

    function toggleInlineElement(tagName) {
        const selection = window.getSelection();
        const editor = activeEditor();
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
        range.insertNode(wrapper);
        selection.removeAllRanges();
        const nextRange = document.createRange();
        nextRange.selectNodeContents(wrapper);
        selection.addRange(nextRange);
    }

    function openLinkInput() {
        if (!linkInput || !inlineToolbar) return;
        const input = linkInput.querySelector('input');
        linkInput.hidden = false;
        linkInput.style.left = inlineToolbar.style.left || '12px';
        linkInput.style.top = inlineToolbar.style.top || '68px';
        inlineToolbar.hidden = true;
        input.value = '';
        input.focus();
        input.onkeydown = (event) => {
            if (event.key === 'Escape') {
                linkInput.hidden = true;
                return;
            }
            if (event.key !== 'Enter') return;
            event.preventDefault();
            restoreSelection();
            if (isSafeHttpUrl(input.value)) document.execCommand('createLink', false, input.value.trim());
            linkInput.hidden = true;
            changed();
        };
    }

    function syncContextualControls() {
        const blocks = selectedBlocks();
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

        app.querySelectorAll('[data-format]').forEach((button) => {
            const active = Boolean(stateMap[button.dataset.format]);
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });

        app.querySelectorAll('[data-block-color]').forEach((button) => {
            const color = button.dataset.blockColor;
            const active = color === 'default'
                ? blocks.length > 0 && blocks.every((block) => !textColorClasses.some((name) => block.classList.contains(`article-color--${name}`)))
                : blocks.length > 0 && blocks.every((block) => block.classList.contains(`article-color--${color}`));
            button.classList.toggle('is-active', active);
        });

        app.querySelectorAll('[data-block-background]').forEach((button) => {
            const color = button.dataset.blockBackground;
            const active = color === 'default'
                ? blocks.length > 0 && blocks.every((block) => !backgroundClasses.some((name) => block.classList.contains(`article-bg--${name}`)))
                : blocks.length > 0 && blocks.every((block) => block.classList.contains(`article-bg--${color}`));
            button.classList.toggle('is-active', active);
        });
    }

    function updateBlockMenu() {
        const editor = activeEditor();
        const selection = window.getSelection();
        if (!editor || !selection || selection.rangeCount === 0 || !editor.contains(selection.anchorNode)) {
            if (blockMenu) blockMenu.hidden = true;
            return;
        }

        const block = closestBlock(selection.anchorNode, editor);
        if (!block || block === editor || ['FIGURE', 'PRE'].includes(block.tagName) || block.querySelector('img, iframe')) {
            if (blockMenu) blockMenu.hidden = true;
            return;
        }

        const workspaceRect = app.querySelector('.canvas-workspace')?.getBoundingClientRect();
        const blockRect = block.getBoundingClientRect();
        if (!workspaceRect || !blockMenu) return;
        blockMenu.hidden = false;
        blockMenu.classList.toggle('is-on-filled-block', (block.textContent || '').trim() !== '');
        blockMenu.style.top = `${blockRect.top - workspaceRect.top + Math.max(0, (blockRect.height - 34) / 2)}px`;
        syncContextualControls();
    }

    function closeBlockActions() {
        if (blockActions) blockActions.hidden = true;
        blockToggle?.setAttribute('aria-expanded', 'false');
    }

    function insertBlock(type) {
        rememberSelection();
        closeBlockActions();

        if (type === 'image') {
            imageUploadIntent = 'insert';
            imageInput?.click();
            return;
        }
        if (type === 'unsplash') {
            openUnsplash();
            return;
        }
        if (type === 'video' || type === 'embed') {
            openUrlDialog(type);
            return;
        }
        if (type === 'code') {
            const pre = document.createElement('pre');
            const code = document.createElement('code');
            code.append(document.createElement('br'));
            pre.append(code);
            insertNode(pre);
            ensureEditableBlockAfter(pre, false);
            placeCaret(code);
            showCodeToolbar(pre);
        }
        if (type === 'divider') {
            const divider = document.createElement('hr');
            insertNode(divider);
            ensureEditableBlockAfter(divider, true);
        }
        if (type === 'dropcap') {
            const block = selectedBlocks()[0];
            if (block?.tagName === 'P') block.classList.toggle('has-drop-cap');
        }
        changed();
    }

    async function uploadImage(file, purpose = 'content', intent = 'insert') {
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 10 * 1024 * 1024) {
            window.alert('Gambar harus JPG, PNG, atau WebP dan maksimal 10MB.');
            return;
        }
        showTransientSaveState(purpose === 'thumbnail' ? 'Mengupload thumbnail…' : 'Mengupload gambar…');
        const formData = new FormData();
        formData.append('image', file);
        formData.append('purpose', purpose);
        try {
            const response = await fetch(app.dataset.uploadUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: formData,
            });
            const payload = await response.json();
            if (!response.ok) throw new Error(errorMessage(payload));

            if (purpose === 'thumbnail') {
                thumbnailUrl = payload.url;
                app.dataset.thumbnailUrl = thumbnailUrl;
                refreshPreview();
                showTransientSaveState('Thumbnail · Tersimpan', '');
                return;
            }

            if (intent === 'replace' && selectedFigure) {
                const image = selectedFigure.querySelector('img');
                if (image) {
                    image.src = payload.url;
                    image.alt = file.name || image.alt;
                    changed();
                    positionImageToolbar(selectedFigure);
                }
                return;
            }

            insertImage(payload.url, file.name || 'Gambar artikel');
            changed();
        } catch (error) {
            if (saveState) {
                saveState.textContent = 'Upload gagal';
                saveState.className = 'canvas-save-state is-error';
                saveState.title = error.message;
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
        insertNode(figure);
        ensureEditableBlockAfter(figure, true);
    }

    function handleEditorClick(event) {
        const figure = event.target?.closest?.('figure');
        const pre = event.target?.closest?.('pre');
        if (figure?.querySelector('img')) {
            showImageToolbar(figure);
            hideCodeToolbar();
        } else if (pre) {
            showCodeToolbar(pre);
            hideImageToolbar();
        } else {
            hideImageToolbar();
            hideCodeToolbar();
        }
        window.setTimeout(() => {
            rememberSelection();
            updateBlockMenu();
        }, 0);
    }

    function showImageToolbar(figure) {
        selectedFigure = figure;
        if (!imageToolbar) return;
        imageToolbar.hidden = false;
        imageLayoutClasses.forEach((layout) => {
            const button = imageToolbar.querySelector(`[data-image-layout="${layout}"]`);
            button?.classList.toggle('is-active', figure.classList.contains(`article-image--${layout}`));
        });
        imageAlignClasses.forEach((alignment) => {
            const button = imageToolbar.querySelector(`[data-image-align="${alignment}"]`);
            button?.classList.toggle('is-active', figure.classList.contains(`article-image-align--${alignment}`));
        });
        positionImageToolbar(figure);
    }

    function hideImageToolbar() {
        if (imageToolbar) imageToolbar.hidden = true;
        selectedFigure = null;
    }

    function setImageLayout(layout) {
        if (!selectedFigure || !imageLayoutClasses.includes(layout)) return;
        imageLayoutClasses.forEach((candidate) => selectedFigure.classList.remove(`article-image--${candidate}`));
        selectedFigure.classList.add(`article-image--${layout}`);
        if (['outset', 'screen'].includes(layout)) {
            imageAlignClasses.forEach((candidate) => selectedFigure.classList.remove(`article-image-align--${candidate}`));
            selectedFigure.classList.add('article-image-align--center');
        }
        changed();
        showImageToolbar(selectedFigure);
    }

    function setImageAlignment(alignment) {
        if (!selectedFigure || !imageAlignClasses.includes(alignment)) return;
        if (!selectedFigure.classList.contains('article-image--compact')) {
            imageLayoutClasses.forEach((candidate) => selectedFigure.classList.remove(`article-image--${candidate}`));
            selectedFigure.classList.add('article-image--compact');
        }
        imageAlignClasses.forEach((candidate) => selectedFigure.classList.remove(`article-image-align--${candidate}`));
        selectedFigure.classList.add(`article-image-align--${alignment}`);
        changed();
        showImageToolbar(selectedFigure);
    }

    function positionImageToolbar(figure) {
        const image = figure?.querySelector('img');
        if (!image || !imageToolbar || imageToolbar.hidden) return;
        positionFloatingElement(imageToolbar, image.getBoundingClientRect());
    }

    function showCodeToolbar(pre) {
        selectedCodeBlock = pre;
        if (!codeToolbar) return;
        codeToolbar.hidden = false;
        positionCodeToolbar();
    }

    function hideCodeToolbar() {
        if (codeToolbar) codeToolbar.hidden = true;
        selectedCodeBlock = null;
    }

    function positionCodeToolbar() {
        if (!selectedCodeBlock || !codeToolbar || codeToolbar.hidden || !selectedCodeBlock.isConnected) return;
        positionFloatingElement(codeToolbar, selectedCodeBlock.getBoundingClientRect());
    }

    function exitCodeBlock() {
        if (!selectedCodeBlock) return;
        const next = ensureEditableBlockAfter(selectedCodeBlock, true);
        hideCodeToolbar();
        if (next) {
            rememberSelection();
            updateBlockMenu();
        }
    }

    function positionFloatingElement(element, rect) {
        const width = Math.min(element.scrollWidth, window.innerWidth - 16);
        element.style.maxWidth = `${window.innerWidth - 16}px`;
        element.style.left = `${Math.max(8, Math.min(window.innerWidth - width - 8, rect.left + rect.width / 2 - width / 2))}px`;
        element.style.top = `${Math.max(8, rect.top - element.offsetHeight - 10)}px`;
    }

    function setupUrlDialog() {
        const dialog = app.querySelector('[data-url-dialog]');
        const input = dialog?.querySelector('[data-url-value]');
        const error = dialog?.querySelector('[data-url-error]');
        dialog?.querySelectorAll('[data-dialog-close]').forEach((button) => button.addEventListener('click', () => { dialog.hidden = true; }));
        dialog?.querySelector('[data-url-apply]')?.addEventListener('click', () => {
            const type = dialog.dataset.type || 'embed';
            const result = embedFromUrl(input?.value || '', type);
            if (!result) {
                if (error) { error.textContent = 'URL belum valid atau providernya belum didukung.'; error.hidden = false; }
                return;
            }
            if (error) error.hidden = true;
            insertNode(result);
            ensureEditableBlockAfter(result, true);
            dialog.hidden = true;
            changed();
        });
    }

    function openUrlDialog(type) {
        const dialog = app.querySelector('[data-url-dialog]');
        if (!dialog) return;
        dialog.dataset.type = type;
        dialog.querySelector('[data-url-eyebrow]').textContent = type === 'video' ? 'Video' : 'Embed';
        dialog.querySelector('[data-url-title]').textContent = type === 'video' ? 'Tempel URL video' : 'Tempel URL media';
        dialog.querySelector('[data-url-value]').value = '';
        dialog.querySelector('[data-url-error]').hidden = true;
        dialog.hidden = false;
        window.setTimeout(() => dialog.querySelector('[data-url-value]').focus(), 0);
    }

    function embedFromUrl(raw, type) {
        let url;
        try { url = new URL(raw.trim()); } catch { return null; }
        if (url.protocol !== 'https:') return null;
        let embedUrl = null;
        const className = type === 'video' ? 'article-video' : 'article-embed';

        if (['youtube.com', 'www.youtube.com', 'youtu.be'].includes(url.hostname)) {
            const id = url.hostname === 'youtu.be' ? url.pathname.slice(1) : url.searchParams.get('v');
            if (/^[A-Za-z0-9_-]+$/.test(id || '')) embedUrl = `https://www.youtube-nocookie.com/embed/${id}`;
        } else if (['vimeo.com', 'www.vimeo.com'].includes(url.hostname)) {
            const id = url.pathname.split('/').filter(Boolean).pop();
            if (/^\d+$/.test(id || '')) embedUrl = `https://player.vimeo.com/video/${id}`;
        } else if (url.hostname === 'open.spotify.com') {
            const parts = url.pathname.split('/').filter(Boolean);
            if (['track', 'episode', 'show', 'playlist', 'album'].includes(parts[0]) && /^[A-Za-z0-9]+$/.test(parts[1] || '')) {
                embedUrl = `https://open.spotify.com/embed/${parts[0]}/${parts[1]}`;
            }
        } else if (url.hostname === 'codepen.io') {
            const parts = url.pathname.split('/').filter(Boolean);
            const penIndex = parts.indexOf('pen');
            if (penIndex === 1 && /^[A-Za-z0-9]+$/.test(parts[2] || '')) embedUrl = `https://codepen.io/${parts[0]}/embed/${parts[2]}`;
        }

        if (embedUrl) {
            const wrapper = document.createElement('div');
            wrapper.className = className;
            const iframe = document.createElement('iframe');
            iframe.src = embedUrl;
            iframe.title = type === 'video' ? 'Video artikel' : 'Media artikel';
            iframe.loading = 'lazy';
            iframe.allowFullscreen = true;
            wrapper.append(iframe);
            return wrapper;
        }

        if (type === 'embed' && isSafeHttpUrl(url.href)) {
            const wrapper = document.createElement('div');
            wrapper.className = 'article-embed';
            const paragraph = document.createElement('p');
            const anchor = document.createElement('a');
            anchor.href = url.href;
            anchor.target = '_blank';
            anchor.rel = 'noopener noreferrer';
            anchor.textContent = url.href;
            paragraph.append(anchor);
            wrapper.append(paragraph);
            return wrapper;
        }

        return null;
    }

    function setupUnsplash() {
        const dialog = app.querySelector('[data-unsplash-dialog]');
        const form = dialog?.querySelector('[data-unsplash-form]');
        const query = dialog?.querySelector('[data-unsplash-query]');
        const results = dialog?.querySelector('[data-unsplash-results]');
        const error = dialog?.querySelector('[data-unsplash-error]');
        dialog?.querySelectorAll('[data-unsplash-close]').forEach((button) => button.addEventListener('click', () => { dialog.hidden = true; }));
        form?.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (error) error.hidden = true;
            results?.replaceChildren();
            try {
                const response = await fetch(`${app.dataset.unsplashUrl}?query=${encodeURIComponent(query.value)}`, { headers: { 'Accept': 'application/json' } });
                const payload = await response.json();
                if (!response.ok) throw new Error(errorMessage(payload));
                payload.results.forEach((photo) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.title = `Foto oleh ${photo.credit_name}`;
                    const image = document.createElement('img');
                    image.src = photo.thumb;
                    image.alt = photo.alt || '';
                    image.loading = 'lazy';
                    button.append(image);
                    button.addEventListener('click', () => {
                        insertImage(photo.url, photo.alt || `Foto oleh ${photo.credit_name}`);
                        dialog.hidden = true;
                        changed();
                    });
                    results.append(button);
                });
            } catch (caught) {
                if (error) { error.textContent = caught.message; error.hidden = false; }
            }
        });
    }

    function openUnsplash() {
        const dialog = app.querySelector('[data-unsplash-dialog]');
        if (!dialog) return;
        dialog.hidden = false;
        window.setTimeout(() => dialog.querySelector('[data-unsplash-query]')?.focus(), 0);
    }

    function setupCategories() {
        const dataElement = app.querySelector('[data-category-data]');
        const chips = app.querySelector('[data-category-chips]');
        const input = app.querySelector('[data-category-input]');
        const suggestionBox = app.querySelector('[data-category-suggestions]');
        let data = { selected: [], suggestions: [] };
        try { data = JSON.parse(dataElement?.textContent || '{}'); } catch { /* Use empty defaults. */ }
        let selected = Array.isArray(data.selected) ? data.selected.filter((item) => typeof item === 'string').slice(0, 5) : [];
        const suggestions = Array.isArray(data.suggestions) ? data.suggestions.filter((item) => typeof item === 'string') : [];

        const normalized = (value) => value.trim().replace(/\s+/g, ' ').toLocaleLowerCase('id');

        function add(value) {
            const clean = value.trim().replace(/\s+/g, ' ');
            if (!clean || selected.length >= 5 || selected.some((item) => normalized(item) === normalized(clean))) return;
            const canonical = suggestions.find((item) => normalized(item) === normalized(clean)) || clean;
            selected.push(canonical.slice(0, 40));
            if (input) input.value = '';
            render();
        }

        function remove(index) {
            selected.splice(index, 1);
            render();
            input?.focus();
        }

        function renderSuggestions() {
            if (!suggestionBox || !input) return;
            const query = normalized(input.value);
            const matches = suggestions
                .filter((item) => !selected.some((current) => normalized(current) === normalized(item)))
                .filter((item) => query === '' || normalized(item).includes(query))
                .slice(0, 8);
            suggestionBox.replaceChildren();
            matches.forEach((item) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = item;
                button.addEventListener('mousedown', (event) => event.preventDefault());
                button.addEventListener('click', () => add(item));
                suggestionBox.append(button);
            });
            suggestionBox.hidden = matches.length === 0;
        }

        function render() {
            chips?.replaceChildren();
            selected.forEach((item, index) => {
                const chip = document.createElement('span');
                chip.textContent = item;
                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.setAttribute('aria-label', `Hapus kategori ${item}`);
                removeButton.textContent = '×';
                removeButton.addEventListener('click', () => remove(index));
                chip.append(removeButton);
                chips?.append(chip);
            });
            if (input) input.disabled = selected.length >= 5;
            renderSuggestions();
            refreshPreview();
        }

        input?.addEventListener('focus', renderSuggestions);
        input?.addEventListener('input', () => {
            if (input.value.includes(',')) {
                input.value.split(',').forEach(add);
            }
            renderSuggestions();
        });
        input?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                add(input.value.replace(/,$/, ''));
            }
            if (event.key === 'Backspace' && input.value === '' && selected.length > 0) {
                remove(selected.length - 1);
            }
            if (event.key === 'Escape' && suggestionBox) suggestionBox.hidden = true;
        });
        input?.addEventListener('blur', () => {
            window.setTimeout(() => {
                if (input.value.trim()) add(input.value);
                if (suggestionBox) suggestionBox.hidden = true;
            }, 120);
        });

        render();
        return { get: () => [...selected] };
    }

    function setupPublish(categoryManager) {
        const error = publishDrawer?.querySelector('[data-publish-error]');
        const submit = publishDrawer?.querySelector('[data-publish-submit]');
        const publishAt = publishDrawer?.querySelector('[data-publish-at]');
        const dateLabel = publishDrawer?.querySelector('[data-publish-date-label]');
        const author = publishDrawer?.querySelector('[data-publish-author]');

        app.querySelector('[data-publish-open]')?.addEventListener('click', async () => {
            if (dirty && !(await saveNow())) return;
            refreshPreview();
            publishDrawer.hidden = false;
        });
        publishDrawer?.querySelectorAll('[data-publish-close]').forEach((button) => button.addEventListener('click', () => { publishDrawer.hidden = true; }));
        publishDrawer?.querySelectorAll('input[name="publish_mode"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                const schedule = radio.checked && radio.value === 'schedule';
                if (dateLabel) dateLabel.textContent = schedule ? 'Jadwal publikasi' : 'Tanggal publikasi';
                if (submit) submit.textContent = schedule ? 'Schedule to publish' : 'Publish now';
                if (schedule && publishAt && new Date(publishAt.value).getTime() <= Date.now()) {
                    publishAt.value = toDateTimeLocal(new Date(Date.now() + 60 * 60 * 1000));
                }
                refreshPreview();
            });
        });
        author?.addEventListener('input', refreshPreview);
        publishAt?.addEventListener('input', refreshPreview);

        submit?.addEventListener('click', async () => {
            const mode = publishDrawer.querySelector('input[name="publish_mode"]:checked')?.value || 'now';
            if (error) error.hidden = true;
            submit.disabled = true;
            try {
                const response = await jsonRequest(app.dataset.publishUrl, 'POST', {
                    publish_mode: mode,
                    published_at: mode === 'now' ? publishAt?.value : null,
                    scheduled_at: mode === 'schedule' ? publishAt?.value : null,
                    author: author?.value || '',
                    tags: categoryManager?.get() || [],
                }, csrf);
                dirty = false;
                window.location.assign(response.redirect);
            } catch (caught) {
                if (error) { error.textContent = caught.message; error.hidden = false; }
                submit.disabled = false;
            }
        });
    }

    function refreshPreview() {
        const idDocument = documents.find((document) => document.dataset.documentLanguage === 'id');
        const title = idDocument?.querySelector('[data-title]')?.value.trim() || 'Artikel tanpa judul';
        const subtitle = idDocument?.querySelector('[data-subtitle]')?.value.trim() || '';
        const previewTitle = publishDrawer?.querySelector('[data-preview-title]');
        const previewSubtitle = publishDrawer?.querySelector('[data-preview-subtitle]');
        const previewImage = publishDrawer?.querySelector('[data-preview-image] img');
        const previewAuthor = publishDrawer?.querySelector('[data-preview-author]');
        const previewDate = publishDrawer?.querySelector('[data-preview-date]');
        const previewReading = publishDrawer?.querySelector('[data-preview-reading]');
        const author = publishDrawer?.querySelector('[data-publish-author]')?.value.trim() || 'Admin';
        const publishAt = publishDrawer?.querySelector('[data-publish-at]')?.value;
        if (previewTitle) previewTitle.textContent = title;
        if (previewSubtitle) {
            previewSubtitle.textContent = subtitle;
            previewSubtitle.hidden = subtitle === '';
        }
        if (previewImage && thumbnailUrl) previewImage.src = thumbnailUrl;
        if (previewAuthor) previewAuthor.textContent = author;
        if (previewDate && publishAt) {
            const date = new Date(publishAt);
            if (!Number.isNaN(date.getTime())) {
                previewDate.dateTime = date.toISOString();
                previewDate.textContent = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(date);
            }
        }
        if (previewReading) previewReading.textContent = `${Math.max(1, Math.ceil(Math.max(1, currentWords) / 220))} menit baca`;
    }

    function handleEditorShortcut(event) {
        const editor = activeEditor();
        const block = closestBlock(window.getSelection()?.anchorNode, editor);

        if (block?.tagName === 'PRE' && event.key === 'Enter' && !event.shiftKey) {
            const code = block.querySelector('code') || block;
            const shouldExit = event.ctrlKey || event.metaKey || (isCaretAtEnd(code) && (code.textContent || '').endsWith('\n'));
            if (shouldExit) {
                event.preventDefault();
                selectedCodeBlock = block;
                exitCodeBlock();
                return;
            }
        }

        if (!(event.ctrlKey || event.metaKey)) return;
        const key = event.key.toLowerCase();
        if (key === 'b' || key === 'i') {
            event.preventDefault();
            document.execCommand(key === 'b' ? 'bold' : 'italic', false);
            changed();
        }
        if (key === 'x' && event.shiftKey) {
            event.preventDefault();
            document.execCommand('strikeThrough', false);
            changed();
        }
        if (key === 'k') {
            event.preventDefault();
            rememberSelection();
            openLinkInput();
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
            changed();
        } else if (event.key === ' ' && text === '1. ') {
            block.textContent = '';
            document.execCommand('insertOrderedList', false);
            changed();
        } else if (text.trim() === '```') {
            const pre = document.createElement('pre');
            const code = document.createElement('code');
            code.append(document.createElement('br'));
            pre.append(code);
            block.replaceWith(pre);
            ensureEditableBlockAfter(pre, false);
            placeCaret(code);
            showCodeToolbar(pre);
            changed();
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
        changed();
    }

    function insertNode(node) {
        restoreSelection();
        const editor = activeEditor();
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
}

function resizeTextarea(field) {
    field.style.height = 'auto';
    field.style.height = `${field.scrollHeight}px`;
}

function closestBlock(node, editor) {
    const element = node instanceof Element ? node : node?.parentElement;
    const block = element?.closest('p,h2,h3,blockquote,li,pre,figure,div.article-embed,div.article-video');
    return block && editor?.contains(block) ? block : (element === editor ? editor : null);
}

function placeCaret(element, atEnd = false) {
    const range = document.createRange();
    range.selectNodeContents(element);
    range.collapse(!atEnd);
    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
    element.closest('[contenteditable="true"]')?.focus();
}

function isCaretAtEnd(element) {
    const selection = window.getSelection();
    if (!selection || selection.rangeCount === 0 || !selection.isCollapsed) return false;
    const range = selection.getRangeAt(0).cloneRange();
    const tail = document.createRange();
    tail.selectNodeContents(element);
    tail.setStart(range.endContainer, range.endOffset);
    return tail.toString() === '';
}

function toDateTimeLocal(date) {
    const pad = (value) => String(value).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

function isSafeHttpUrl(value) {
    try {
        const url = new URL(value);
        return ['http:', 'https:'].includes(url.protocol);
    } catch {
        return false;
    }
}

async function jsonRequest(url, method, payload, csrf) {
    const response = await fetch(url, {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify(payload),
    });
    const data = await response.json();
    if (!response.ok) throw new Error(errorMessage(data));
    return data;
}

function errorMessage(payload) {
    const errors = payload?.errors ? Object.values(payload.errors).flat() : [];
    return errors[0] || payload?.message || 'Terjadi kesalahan. Silakan coba lagi.';
}
