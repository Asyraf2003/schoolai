import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function wireToolbar(context, state, actions) {
    context.countToggle?.addEventListener('click', () => {
        const open = context.countPopover?.hidden ?? true;
        if (context.countPopover) context.countPopover.hidden = !open;
        context.countToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    context.blockToggle?.addEventListener('mousedown', (event) => event.preventDefault());
    context.blockToggle?.addEventListener('click', () => {
        actions.rememberSelection();
        const open = context.blockActions?.hidden ?? true;
        if (context.blockActions) context.blockActions.hidden = !open;
        context.blockToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        actions.syncContextualControls();
    });

    context.app.querySelectorAll('[data-format]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => actions.applyFormat(button.dataset.format));
    });

    context.app.querySelectorAll('[data-insert]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => actions.insertBlock(button.dataset.insert));
    });

    context.app.querySelectorAll('[data-block-color]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => actions.applyBlockPalette('article-color--', button.dataset.blockColor, context.textColorClasses));
    });

    context.app.querySelectorAll('[data-block-background]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => actions.applyBlockPalette('article-bg--', button.dataset.blockBackground, context.backgroundClasses));
    });

    context.app.querySelector('[data-color-toggle]')?.addEventListener('mousedown', (event) => event.preventDefault());
    context.app.querySelector('[data-color-toggle]')?.addEventListener('click', () => {
        if (context.inlineColors) context.inlineColors.hidden = !context.inlineColors.hidden;
    });

    context.app.querySelectorAll('[data-text-color]').forEach((button) => {
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => actions.applyTextColor(button.dataset.textColor));
    });
}
