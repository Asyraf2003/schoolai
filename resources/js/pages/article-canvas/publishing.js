import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function createPublishingActions(context, state, actions) {
    function setupPublish(categoryManager) {
        const error = context.publishDrawer?.querySelector('[data-publish-error]');
        const submit = context.publishDrawer?.querySelector('[data-publish-submit]');
        const publishAt = context.publishDrawer?.querySelector('[data-publish-at]');
        const dateLabel = context.publishDrawer?.querySelector('[data-publish-date-label]');
        const author = context.publishDrawer?.querySelector('[data-publish-author]');

        context.app.querySelector('[data-publish-open]')?.addEventListener('click', async () => {
            if (state.dirty && !(await actions.saveNow())) return;
            refreshPreview();
            context.publishDrawer.hidden = false;
        });
        context.publishDrawer?.querySelectorAll('[data-publish-close]').forEach((button) => button.addEventListener('click', () => { context.publishDrawer.hidden = true; }));
        context.publishDrawer?.querySelectorAll('input[name="publish_mode"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                const schedule = radio.checked && radio.value === 'schedule';
                if (dateLabel) dateLabel.textContent = schedule ? 'Jadwal publikasi' : 'Tanggal publikasi';
                if (submit) submit.textContent = schedule ? 'Schedule to publish' : 'Publish now';
                if (schedule && publishAt && new Date(publishAt.value).getTime() <= Date.now()) {
                    publishAt.value = toDateTimeLocal(new Date(Date.now() + 60 * 60 * 1000));
                }
                if (!schedule && publishAt && new Date(publishAt.value).getTime() > Date.now() + 5 * 60 * 1000) {
                    publishAt.value = toDateTimeLocal(new Date());
                }
                refreshPreview();
            });
        });
        author?.addEventListener('input', refreshPreview);
        publishAt?.addEventListener('input', refreshPreview);

        submit?.addEventListener('click', async () => {
            const mode = context.publishDrawer.querySelector('input[name="publish_mode"]:checked')?.value || 'now';
            if (error) error.hidden = true;
            submit.disabled = true;
            try {
                const response = await jsonRequest(context.app.dataset.publishUrl, 'POST', {
                    publish_mode: mode,
                    published_at: mode === 'now' ? publishAt?.value : null,
                    scheduled_at: mode === 'schedule' ? publishAt?.value : null,
                    author: author?.value || '',
                    tags: categoryManager?.get() || [],
                }, context.csrf);
                state.dirty = false;
                window.location.assign(response.redirect);
            } catch (caught) {
                if (error) { error.textContent = caught.message; error.hidden = false; }
                submit.disabled = false;
            }
        });
    }

    function refreshPreview() {
        const idDocument = context.documents.find((document) => document.dataset.documentLanguage === 'id');
        const title = idDocument?.querySelector('[data-title]')?.value.trim() || 'Artikel tanpa judul';
        const subtitle = idDocument?.querySelector('[data-subtitle]')?.value.trim() || '';
        const previewTitle = context.publishDrawer?.querySelector('[data-preview-title]');
        const previewSubtitle = context.publishDrawer?.querySelector('[data-preview-subtitle]');
        const previewImage = context.publishDrawer?.querySelector('[data-preview-image] img');
        const previewAuthor = context.publishDrawer?.querySelector('[data-preview-author]');
        const previewDate = context.publishDrawer?.querySelector('[data-preview-date]');
        const previewReading = context.publishDrawer?.querySelector('[data-preview-reading]');
        const author = context.publishDrawer?.querySelector('[data-publish-author]')?.value.trim() || 'Admin';
        const publishAt = context.publishDrawer?.querySelector('[data-publish-at]')?.value;
        if (previewTitle) previewTitle.textContent = title;
        if (previewSubtitle) {
            previewSubtitle.textContent = subtitle;
            previewSubtitle.hidden = subtitle === '';
        }
        if (previewImage && state.thumbnailUrl) previewImage.src = state.thumbnailUrl;
        if (previewAuthor) previewAuthor.textContent = author;
        if (previewDate && publishAt) {
            const date = new Date(publishAt);
            if (!Number.isNaN(date.getTime())) {
                previewDate.dateTime = date.toISOString();
                previewDate.textContent = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(date);
            }
        }
        if (previewReading) previewReading.textContent = `${Math.max(1, Math.ceil(Math.max(1, state.currentWords) / 220))} menit baca`;
    }

    return { setupPublish, refreshPreview };
}
