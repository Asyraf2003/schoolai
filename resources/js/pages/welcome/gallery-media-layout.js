export function galleryMediaLayoutClass(url, isVideo) {
  if (!isVideo) return 'is-image';

  try {
    var parsed = new URL(url, window.location.href);
    var host = parsed.hostname.toLowerCase().replace(/^www\./, '');

    if (host === 'facebook.com' || host === 'tiktok.com') {
      return 'is-portrait';
    }

    if (host === 'instagram.com') {
      return /^\/p\//.test(parsed.pathname) ? 'is-square' : 'is-portrait';
    }

    return 'is-landscape';
  } catch (error) {
    return 'is-landscape';
  }
}

export function applyGalleryMediaLayout(element, url, isVideo) {
  if (!element) return;

  element.classList.remove('is-landscape', 'is-portrait', 'is-square', 'is-image');
  element.classList.add(galleryMediaLayoutClass(url, isVideo));
}
