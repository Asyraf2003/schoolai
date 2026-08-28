const HOMEPAGE_CURSOR_CHARACTERS = ['cwo', 'cwe'];

export function initHomepageCursor() {
    const body = document.body;

    if (!body?.classList.contains('home-page')) {
        return;
    }

    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        body.removeAttribute('data-cursor-character');
        return;
    }

    const character =
        HOMEPAGE_CURSOR_CHARACTERS[
            Math.floor(Math.random() * HOMEPAGE_CURSOR_CHARACTERS.length)
        ];

    body.dataset.cursorCharacter = character;
}
