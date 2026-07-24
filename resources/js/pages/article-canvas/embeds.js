import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function createEmbedActions(context, state, actions) {
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
        const dialog = context.app.querySelector('[data-unsplash-dialog]');
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
                const response = await fetch(`${context.app.dataset.unsplashUrl}?query=${encodeURIComponent(query.value)}`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const payload = await readJsonResponse(response);
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
                        actions.insertImage(photo.url, photo.alt || `Foto oleh ${photo.credit_name}`);
                        dialog.hidden = true;
                        actions.changed();
                    });
                    results.append(button);
                });
            } catch (caught) {
                if (error) { error.textContent = caught.message; error.hidden = false; }
            }
        });
    }

    function openUnsplash() {
        const dialog = context.app.querySelector('[data-unsplash-dialog]');
        if (!dialog) return;
        dialog.hidden = false;
        window.setTimeout(() => dialog.querySelector('[data-unsplash-query]')?.focus(), 0);
    }

    return { embedFromUrl, setupUnsplash, openUnsplash };
}
