function localeKey(value) {
    return String(value || 'id').toLowerCase().split('-')[0];
}

function segmentsFor(text, arabic) {
    return arabic ? text.split(/(\s+)/u).filter(Boolean) : Array.from(text);
}

function textNodes(element) {
    const walker = document.createTreeWalker(
        element,
        NodeFilter.SHOW_TEXT,
        {
            acceptNode(node) {
                return node.nodeValue
                    ? NodeFilter.FILTER_ACCEPT
                    : NodeFilter.FILTER_REJECT;
            },
        }
    );
    const nodes = [];

    while (walker.nextNode()) nodes.push(walker.currentNode);
    return nodes;
}

function splitNode(node, arabic, units) {
    const fragment = document.createDocumentFragment();

    segmentsFor(node.nodeValue, arabic).forEach((segment) => {
        if (/^\s+$/u.test(segment)) {
            fragment.appendChild(document.createTextNode(segment));
            return;
        }

        const unit = document.createElement('span');
        unit.className = 'story-unit';
        unit.dataset.storyUnit = '';
        unit.setAttribute('aria-hidden', 'true');
        unit.textContent = segment;
        fragment.appendChild(unit);
        units.push(unit);
    });

    node.replaceWith(fragment);
}

function prepareElement(element, arabic) {
    const source = element.getAttribute('aria-label') || element.textContent.trim();

    if (element.dataset.storyPrepared === 'true') {
        return {
            element,
            units: Array.from(element.querySelectorAll('[data-story-unit]')),
        };
    }

    const units = [];
    element.setAttribute('aria-label', source);
    textNodes(element).forEach((node) => splitNode(node, arabic, units));
    element.dataset.storyPrepared = 'true';

    return { element, units };
}

export function prepareStoryText(root) {
    const locale = localeKey(
        root.dataset.storyLocale || document.documentElement.lang
    );
    const arabic = locale === 'ar';

    return Array.from(root.querySelectorAll('[data-story-text]')).map((element) => {
        const prepared = prepareElement(element, arabic);
        return {
            ...prepared,
            effect: 'stretch',
        };
    });
}
