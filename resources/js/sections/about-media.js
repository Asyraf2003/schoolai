export function createAboutMedia(root, stories, signal) {
    const layers = [...root.querySelectorAll('[data-about-layer]')];
    const inline = stories.map(story => story.querySelector('[data-about-preview]'));
    const videos = [...inline, ...layers.map(layer => layer.querySelector('video'))];
    let desired = null;
    let current = null;
    let selected = null;
    let wide = false;
    let allowed = false;

    const reveal = layer => {
        if (layer !== desired) return;
        layers.forEach(candidate => { candidate.dataset.visible = String(candidate === layer); });
        current = layer;
    };
    const hydrate = (video, url) => {
        if (video.getAttribute('src') === url) return;
        video.dataset.ready = 'false';
        video.src = url;
        video.preload = 'auto';
        video.load();
    };
    const synchronize = () => {
        const active = wide ? desired?.querySelector('video') : inline[stories.indexOf(selected)];
        videos.forEach(video => {
            if (video !== active || !allowed) {
                video.pause();
                if (inline.includes(video)) video.dataset.ready = 'false';
            }
            else if (video.getAttribute('src') && video.paused && !video.error) video.play().catch(() => {});
        });
    };
    videos.forEach(video => {
        const ready = () => {
            if (video.readyState < 2) return;
            video.dataset.ready = String(!inline.includes(video) || (!video.paused && allowed));
            if (wide && video.parentElement === desired) reveal(desired);
        };
        video.addEventListener('loadeddata', ready, { signal });
        video.addEventListener('playing', ready, { signal });
        for (const type of ['waiting', 'error', 'emptied']) {
            video.addEventListener(type, () => { video.dataset.ready = 'false'; }, { signal });
        }
    });
    layers.forEach(layer => {
        layer.querySelector('img').addEventListener('load', () => reveal(layer), { signal });
        layer.querySelector('img').addEventListener('error', () => {
            if (layer.querySelector('video').readyState >= 2) reveal(layer);
        }, { signal });
    });

    return {
        prepareInline(story) {
            hydrate(inline[stories.indexOf(story)], story.dataset.preview);
        },
        update(story, options) {
            selected = story;
            wide = options.wide;
            allowed = options.play;
            if (wide) {
                if (desired?.dataset.story !== story.dataset.aboutStory) {
                    desired = layers.find(layer => layer !== current) ?? layers[0];
                    desired.dataset.visible = 'false';
                    desired.dataset.story = story.dataset.aboutStory;
                    const video = desired.querySelector('video');
                    video.pause();
                    if (video.getAttribute('src') && video.getAttribute('src') !== story.dataset.preview) {
                        video.removeAttribute('src');
                        video.load();
                    }
                    desired.querySelector('img').src = story.dataset.poster;
                    if (desired.querySelector('img').complete && desired.querySelector('img').naturalWidth) reveal(desired);
                }
                if (options.prepare) hydrate(desired.querySelector('video'), story.dataset.preview);
            } else if (options.prepare) {
                this.prepareInline(story);
            }
            synchronize();
        },
        pause() { allowed = false; synchronize(); },
        dispose() {
            allowed = false;
            videos.forEach(video => { video.pause(); video.removeAttribute('src'); video.load(); });
        },
    };
}
