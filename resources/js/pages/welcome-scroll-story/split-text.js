function localeKey(value) {
    return String(value || 'id').toLowerCase().split('-')[0];
}

function latinSegments(text) {
    return Array.from(text);
}

function arabicSegments(text) {
    return text.split(/(\s+)/u).filter(Boolean);
}

export function prepareStoryText(root) {
    const locale = localeKey(
        root.dataset.storyLocale || document.documentElement.lang
    );
    const arabic = locale === 'ar';

    return Array.from(root.querySelectorAll('[data-story-text]')).map((element) => {
        const source = element.textContent.trim();
        const segments = arabic ? arabicSegments(source) : latinSegments(source);
        const fragment = document.createDocumentFragment();
        const units = [];

        element.setAttribute('aria-label', source);
        element.textContent = '';

        segments.forEach((segment, index) => {
            if (/^\s+$/u.test(segment)) {
                fragment.appendChild(document.createTextNode(segment));
                return;
            }

            const unit = document.createElement('span');
            unit.className = 'story-unit';
            unit.setAttribute('aria-hidden', 'true');
            unit.dataset.storyUnit = '';
            unit.dataset.storyIndex = String(index);
            unit.textContent = segment;
            fragment.appendChild(unit);
            units.push(unit);
        });

        element.appendChild(fragment);

        return {
            element,
            effect: element.dataset.storyEffect || 'rise',
            units,
        };
    });
}
