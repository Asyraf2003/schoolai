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
    const formatBar = app.querySelector('[data-format-bar]');
    const linkInput = app.querySelector('[data-link-input]');
    const blockMenu = app.querySelector('[data-block-menu]');
    const blockToggle = app.querySelector('[data-block-toggle]');
    const blockActions = app.querySelector('[data-block-actions]');
    const imageInput = app.querySelector('[data-image-file]');
    const imageToolbar = app.querySelector('[data-image-toolbar]');
    const publishDrawer = app.querySelector('[data-publish-drawer]');
    let activeLanguage = 'id';
    let savedRange = null;
    let selectedFigure = null;
    let autosaveTimer = null;
    let dirty = false;
    let saving = false;
    let lastQuoteBlock = null;

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
            updateMetrics();
            updateBlockMenu();
        });
    });

    countToggle?.addEventListener('click', () => {
        const open = countPopover?.hidden ?? true;
        if (countPopover) countPopover.hidden = !open;
        countToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    blockToggle?.addEventListener('click', () => {
        const open = blockActions?.hidden ?? true;
        if (blockActions) blockActions.hidden = !open;
        blockToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    app.querySelectorAll('[data-format]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => applyFormat(button.dataset.format));
    });

    app.querySelectorAll('[data-insert]').forEach((button) => {
        button.addEventListener('click', () => insertBlock(button.dataset.insert));
    });

    imageInput?.addEventListener('change', async () => {
        const file = imageInput.files?.[0];
        if (!file) return;
        await uploadImage(file);
        imageInput.value = '';
    });

    app.querySelectorAll('[data-image-layout]').forEach((button) => {
        button.addEventListener('click', () => setImageLayout(button.dataset.imageLayout));
    });

    app.querySelector('[data-image-alt]')?.addEventListener('click', () => {
        const image = selectedFigure?.querySelector('img');
        if (!image) return;
        const value = window.prompt('Alt text untuk aksesibilitas dan SEO:', image.alt || '');
        if (value === null) return;
        image.alt = value.slice(0, 300);
        changed();
    });

    setupUrlDialog();
    setupUnsplash();
    setupPublish();

    document.addEventListener('selectionchange', () => {
        const selection = window.getSelection();
        if (!selection || selection.rangeCount === 0) return;
        const editor = activeEditor();
        if (editor?.contains(selection.anchorNode)) rememberSelection();
    });

    document.addEventListener('click', (event) => {
        if (!inlineToolbar?.contains(event.target) && !formatBar?.contains(event.target) && !activeEditor()?.contains(event.target)) {
            hideInlineToolbar();
        }
        if (!imageToolbar?.contains(event.target) && !(event.target instanceof HTMLImageElement)) {
            hideImageToolbar();
        }
    });

    window.addEventListener('beforeunload', (event) => {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });

    updateMetrics();

    function changed() {
        dirty = true;
        if (saveState) {
            saveState.textContent = 'Draft · Menyimpan…';
            saveState.className = 'canvas-save-state is-saving';
        }
        window.clearTimeout(autosaveTimer);
        autosaveTimer = window.setTimeout(saveNow, 900);
    }

    async function saveNow() {
        if (saving) return false;
        saving = true;
        window.clearTimeout(autosaveTimer);

        const payload = {};
        documents.forEach((document) => {
            const language = document.dataset.documentLanguage;
            payload[`title_${language}`] = document.querySelector('[data-title]')?.value || '';
            payload[`subtitle_${language}`] = document.querySelector('[data-subtitle]')?.value || '';
            payload[`content_${language}`] = document.querySelector('[data-editor]')?.innerHTML || '';
        });

        try {
            const response = await jsonRequest(app.dataset.autosaveUrl, 'PATCH', payload, csrf);
            dirty = false;
            if (saveState) {
                saveState.textContent = response.saved_label || 'Draft · Tersimpan';
                saveState.className = 'canvas-save-state';
            }
            updateMetrics(response.word_count, response.character_count);
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
        const words = serverWords ?? (text ? (text.match(/[\p{L}\p{N}]+(?:[’'\-][\p{L}\p{N}]+)*/gu) || []).length : 0);
        const characters = serverCharacters ?? text.length;
        if (countToggle) countToggle.textContent = `${words} kata`;
        if (wordCount) wordCount.textContent = `${words} kata`;
        if (characterCount) characterCount.textContent = `${characters} karakter`;
    }

    function rememberSelection() {
        const selection = window.getSelection();
        const editor = activeEditor();
        if (!selection || selection.rangeCount === 0 || !editor?.contains(selection.anchorNode)) return;
        savedRange = selection.getRangeAt(0).cloneRange();
    }

    function restoreSelection() {
        if (!savedRange) return false;
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(savedRange);
        return true;
    }

    function updateInlineToolbar() {
        const selection = window.getSelection();
        const editor = activeEditor();
        if (!selection || selection.rangeCount === 0 || selection.isCollapsed || !editor?.contains(selection.anchorNode)) {
            hideInlineToolbar();
            return;
        }

        const rect = selection.getRangeAt(0).getBoundingClientRect();
        if (!rect.width && !rect.height) return;
        inlineToolbar.hidden = false;
        const width = inlineToolbar.offsetWidth;
        inlineToolbar.style.left = `${Math.max(8, Math.min(window.innerWidth - width - 8, rect.left + rect.width / 2 - width / 2))}px`;
        inlineToolbar.style.top = `${Math.max(8, rect.top - inlineToolbar.offsetHeight - 10)}px`;
    }

    function hideInlineToolbar() {
        if (inlineToolbar) inlineToolbar.hidden = true;
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
        } else if (format === 'paragraph') {
            document.execCommand('formatBlock', false, 'P');
        } else if (format === 'h2' || format === 'h3') {
            document.execCommand('formatBlock', false, format.toUpperCase());
        } else if (format === 'small' || format === 'large') {
            toggleBlockClass(`article-text-${format}`, ['article-text-small', 'article-text-large']);
        } else if (format === 'align-left') {
            toggleBlockClass('', ['article-align-center', 'article-align-right']);
        } else if (format === 'align-center' || format === 'align-right') {
            toggleBlockClass(`article-${format}`, ['article-align-center', 'article-align-right']);
        } else if (format === 'quote') {
            toggleQuote();
        }

        changed();
        rememberSelection();
        updateInlineToolbar();
    }

    function openLinkInput() {
        if (!linkInput) return;
        const input = linkInput.querySelector('input');
        const anchorToolbar = inlineToolbar && !inlineToolbar.hidden ? inlineToolbar : formatBar;
        linkInput.hidden = false;
        if (anchorToolbar === inlineToolbar) {
            linkInput.style.left = inlineToolbar.style.left;
            linkInput.style.top = inlineToolbar.style.top;
            inlineToolbar.hidden = true;
        } else {
            const rect = formatBar?.getBoundingClientRect();
            linkInput.style.left = `${Math.max(8, Math.min(window.innerWidth - 286, rect?.left || 12))}px`;
            linkInput.style.top = `${(rect?.bottom || 68) + 8}px`;
        }
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

    function toggleQuote() {
        const block = closestBlock(window.getSelection()?.anchorNode, activeEditor());
        if (block?.tagName === 'BLOCKQUOTE') {
            if (block === lastQuoteBlock || block.classList.contains('article-quote')) {
                block.className = 'article-pull-quote';
            } else {
                block.className = 'article-quote';
            }
            lastQuoteBlock = block;
            return;
        }
        document.execCommand('formatBlock', false, 'BLOCKQUOTE');
        const nextBlock = closestBlock(window.getSelection()?.anchorNode, activeEditor());
        nextBlock?.classList.add('article-quote');
        lastQuoteBlock = nextBlock;
    }

    function toggleInlineElement(tagName) {
        const selection = window.getSelection();
        const editor = activeEditor();
        if (!selection || selection.rangeCount === 0 || !editor?.contains(selection.anchorNode)) return;

        const selectedText = selection.toString();
        if (!selectedText) {
            document.execCommand(tagName === 'mark' ? 'backColor' : 'insertHTML', false, tagName === 'mark' ? '#fff3a3' : '');
            return;
        }

        const range = selection.getRangeAt(0);
        const wrapper = document.createElement(tagName);
        wrapper.append(range.extractContents());
        range.insertNode(wrapper);
        selection.removeAllRanges();
        const nextRange = document.createRange();
        nextRange.selectNodeContents(wrapper);
        selection.addRange(nextRange);
    }

    function toggleBlockClass(className, mutuallyExclusive = []) {
        const block = closestBlock(window.getSelection()?.anchorNode || savedRange?.startContainer, activeEditor());
        if (!block || ['FIGURE', 'PRE'].includes(block.tagName)) return;

        mutuallyExclusive.forEach((candidate) => block.classList.remove(candidate));
        if (className) block.classList.toggle(className);
    }

    function updateBlockMenu() {
        const editor = activeEditor();
        const selection = window.getSelection();
        if (!editor || !selection || selection.rangeCount === 0 || !selection.isCollapsed || !editor.contains(selection.anchorNode)) {
            if (blockMenu) blockMenu.hidden = true;
            return;
        }

        const block = closestBlock(selection.anchorNode, editor);
        if (!block || block.querySelector('img, iframe')) {
            if (blockMenu) blockMenu.hidden = true;
            return;
        }

        const workspaceRect = app.querySelector('.canvas-workspace')?.getBoundingClientRect();
        const blockRect = block.getBoundingClientRect();
        if (!workspaceRect || !blockMenu) return;
        blockMenu.hidden = false;
        blockMenu.classList.toggle('is-on-filled-block', (block.textContent || '').trim() !== '');
        blockMenu.style.top = `${blockRect.top - workspaceRect.top + Math.max(0, (blockRect.height - 34) / 2)}px`;
    }

    function insertBlock(type) {
        rememberSelection();
        if (blockActions) blockActions.hidden = true;
        blockToggle?.setAttribute('aria-expanded', 'false');

        if (type === 'image') {
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
            placeCaret(code);
        }
        if (type === 'divider') {
            insertNode(document.createElement('hr'));
            insertParagraphAfterSelection();
        }
        if (type === 'dropcap') {
            const block = closestBlock(savedRange?.startContainer, activeEditor());
            if (block?.tagName === 'P') block.classList.toggle('has-drop-cap');
        }
        changed();
    }

    async function uploadImage(file) {
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 10 * 1024 * 1024) {
            window.alert('Gambar harus JPG, PNG, atau WebP dan maksimal 10MB.');
            return;
        }
        if (saveState) {
            saveState.textContent = 'Mengupload gambar…';
            saveState.className = 'canvas-save-state is-saving';
        }
        const formData = new FormData();
        formData.append('image', file);
        try {
            const response = await fetch(app.dataset.uploadUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: formData,
            });
            const payload = await response.json();
            if (!response.ok) throw new Error(errorMessage(payload));
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
        figure.className = 'article-image--inline';
        const image = document.createElement('img');
        image.src = url;
        image.alt = alt;
        const caption = document.createElement('figcaption');
        caption.contentEditable = 'true';
        figure.append(image, caption);
        insertNode(figure);
        insertParagraphAfterSelection();
    }

    function handleEditorClick(event) {
        if (event.target instanceof HTMLImageElement && event.target.closest('figure')) {
            selectedFigure = event.target.closest('figure');
            const rect = event.target.getBoundingClientRect();
            imageToolbar.hidden = false;
            const width = imageToolbar.offsetWidth;
            imageToolbar.style.left = `${Math.max(8, rect.left + rect.width / 2 - width / 2)}px`;
            imageToolbar.style.top = `${Math.max(8, rect.top - imageToolbar.offsetHeight - 10)}px`;
        }
    }

    function hideImageToolbar() {
        if (imageToolbar) imageToolbar.hidden = true;
        selectedFigure = null;
    }

    function setImageLayout(layout) {
        if (!selectedFigure || !['inline', 'outset', 'screen'].includes(layout)) return;
        selectedFigure.className = `article-image--${layout}`;
        changed();
        positionImageToolbar(selectedFigure);
    }

    function positionImageToolbar(figure) {
        const image = figure.querySelector('img');
        if (!image || !imageToolbar) return;
        const rect = image.getBoundingClientRect();
        imageToolbar.style.left = `${Math.max(8, rect.left + rect.width / 2 - imageToolbar.offsetWidth / 2)}px`;
        imageToolbar.style.top = `${Math.max(8, rect.top - imageToolbar.offsetHeight - 10)}px`;
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
            insertParagraphAfterSelection();
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
        let className = type === 'video' ? 'article-video' : 'article-embed';

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

    function setupPublish() {
        const error = publishDrawer?.querySelector('[data-publish-error]');
        const submit = publishDrawer?.querySelector('[data-publish-submit]');
        const scheduledAt = publishDrawer?.querySelector('[data-scheduled-at]');
        app.querySelector('[data-publish-open]')?.addEventListener('click', async () => {
            if (dirty && !(await saveNow())) return;
            refreshPreview();
            publishDrawer.hidden = false;
        });
        publishDrawer?.querySelectorAll('[data-publish-close]').forEach((button) => button.addEventListener('click', () => { publishDrawer.hidden = true; }));
        publishDrawer?.querySelectorAll('input[name="publish_mode"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                const schedule = radio.checked && radio.value === 'schedule';
                if (scheduledAt) scheduledAt.hidden = !schedule;
                if (submit) submit.textContent = schedule ? 'Schedule to publish' : 'Publish now';
            });
        });
        submit?.addEventListener('click', async () => {
            const mode = publishDrawer.querySelector('input[name="publish_mode"]:checked')?.value || 'now';
            const tags = (publishDrawer.querySelector('[data-publish-tags]')?.value || '').split(',').map((tag) => tag.trim()).filter(Boolean).slice(0, 5);
            if (error) error.hidden = true;
            submit.disabled = true;
            try {
                const response = await jsonRequest(app.dataset.publishUrl, 'POST', {
                    publish_mode: mode,
                    scheduled_at: mode === 'schedule' ? scheduledAt?.value : null,
                    tags,
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
        const firstImage = idDocument?.querySelector('[data-editor] img');
        const previewTitle = publishDrawer?.querySelector('[data-preview-title]');
        const previewSubtitle = publishDrawer?.querySelector('[data-preview-subtitle]');
        const previewImage = publishDrawer?.querySelector('[data-preview-image] img');
        if (previewTitle) previewTitle.textContent = title;
        if (previewSubtitle) previewSubtitle.textContent = subtitle;
        if (previewImage && firstImage) previewImage.src = firstImage.src;
    }

    function handleEditorShortcut(event) {
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
        if (!block) return;
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
            placeCaret(code);
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
        if (!editor) return;
        if (!selection || selection.rangeCount === 0 || !editor.contains(selection.anchorNode)) {
            editor.append(node);
            return;
        }
        const block = closestBlock(selection.anchorNode, editor);
        if (block && (block.textContent || '').trim() === '' && block.tagName === 'P') {
            block.replaceWith(node);
        } else if (block && block !== editor) {
            block.after(node);
        } else {
            selection.getRangeAt(0).insertNode(node);
        }
    }

    function insertParagraphAfterSelection() {
        const editor = activeEditor();
        if (!editor) return;
        const paragraph = document.createElement('p');
        paragraph.append(document.createElement('br'));
        const last = editor.lastElementChild;
        if (!last || last !== paragraph) editor.append(paragraph);
        placeCaret(paragraph);
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
