// Helper DOM kecil: semua query/event dasar dikumpulkan agar page module tidak berulang-ulang.
export function qs(selector, scope = document) {
    return scope.querySelector(selector);
}

export function on(target, event, handler, options) {
    target?.addEventListener(event, handler, options);
}

export function ready(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }

    callback();
}
