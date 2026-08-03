function numberValue(value, fallback = 0) {
    const parsed = Number.parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
}

export function readGalleryData(root) {
    return Array.from(root.querySelectorAll('[data-depth-gallery-source]'))
        .map((element, index) => ({
            element,
            index,
            title: element.getAttribute('data-title') || '',
            caption: element.getAttribute('data-caption') || '',
            mediaUrl: element.getAttribute('data-media-url') || '',
            textureSrc: element.getAttribute('data-thumbnail-url') || '',
            isVideo: element.getAttribute('data-is-video') === '1',
            position: {
                x: numberValue(element.getAttribute('data-position-x')),
                y: 0,
            },
            fallbackColor: element.getAttribute('data-fallback-color') || '#ffffff',
            accentColor: element.getAttribute('data-accent-color') || '#ffffff',
            backgroundColor: element.getAttribute('data-background-color') || '#fffaf0',
            blob1Color: element.getAttribute('data-blob1-color') || '#ffdf94',
            blob2Color: element.getAttribute('data-blob2-color') || '#fce7c4',
        }));
}
