const root = document.querySelector('[data-article-canvas]');

if (root) {
    const dataNode = document.querySelector('[data-article-canvas-arabic-data]');
    let arabic = { title: '', subtitle: '', content: '' };

    if (dataNode) {
        try {
            arabic = {
                ...arabic,
                ...JSON.parse(dataNode.textContent || '{}'),
            };
        } catch {
            // Keep the Arabic canvas usable even if persisted data is malformed.
        }
    }

    const switcher = root.querySelector('.canvas-language-switch');

    if (switcher && !switcher.querySelector('[data-language="ar"]')) {
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.language = 'ar';
        button.setAttribute('aria-pressed', 'false');
        button.textContent = 'AR';
        switcher.append(button);
    }

    const workspace = root.querySelector('.canvas-workspace');
    const blockMenu = workspace?.querySelector('[data-block-menu]');

    if (workspace && !workspace.querySelector('[data-document-language="ar"]')) {
        const documentSection = document.createElement('section');
        documentSection.className = 'canvas-document';
        documentSection.dataset.documentLanguage = 'ar';
        documentSection.setAttribute('aria-label', 'لوحة المقال باللغة العربية');
        documentSection.setAttribute('lang', 'ar');
        documentSection.setAttribute('dir', 'rtl');
        documentSection.hidden = true;

        const title = document.createElement('textarea');
        title.className = 'canvas-title';
        title.dataset.title = '';
        title.rows = 1;
        title.maxLength = 200;
        title.placeholder = 'العنوان';
        title.setAttribute('aria-label', 'عنوان المقال باللغة العربية');
        title.value = typeof arabic.title === 'string' ? arabic.title : '';

        const subtitle = document.createElement('textarea');
        subtitle.className = 'canvas-subtitle';
        subtitle.dataset.subtitle = '';
        subtitle.rows = 1;
        subtitle.maxLength = 300;
        subtitle.placeholder = 'العنوان الفرعي (اختياري)';
        subtitle.setAttribute('aria-label', 'العنوان الفرعي للمقال باللغة العربية');
        subtitle.value = typeof arabic.subtitle === 'string' ? arabic.subtitle : '';

        const editor = document.createElement('div');
        editor.className = 'canvas-body';
        editor.dataset.editor = '';
        editor.contentEditable = 'true';
        editor.setAttribute('role', 'textbox');
        editor.setAttribute('aria-multiline', 'true');
        editor.dataset.placeholder = 'اكتب قصتك هنا...';
        editor.spellcheck = true;
        editor.innerHTML = typeof arabic.content === 'string' ? arabic.content : '';

        documentSection.append(title, subtitle, editor);

        if (blockMenu) {
            workspace.insertBefore(documentSection, blockMenu);
        } else {
            workspace.append(documentSection);
        }
    }
}
