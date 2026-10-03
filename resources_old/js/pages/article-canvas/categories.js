import { closestBlock, isCaretAtEnd, isSafeHttpUrl, jsonRequest, placeCaret, toDateTimeLocal } from './helpers.js';

export function createCategoryActions(context, state, actions) {
    function setupCategories() {
        const dataElement = context.app.querySelector('[data-category-data]');
        const chips = context.app.querySelector('[data-category-chips]');
        const input = context.app.querySelector('[data-category-input]');
        const suggestionBox = context.app.querySelector('[data-category-suggestions]');
        let data = { selected: [], suggestions: [] };
        try { data = JSON.parse(dataElement?.textContent || '{}'); } catch { /* Use empty defaults. */ }
        let selected = Array.isArray(data.selected) ? data.selected.filter((item) => typeof item === 'string').slice(0, 5) : [];
        const suggestions = Array.isArray(data.suggestions) ? data.suggestions.filter((item) => typeof item === 'string') : [];

        const normalized = (value) => value.trim().replace(/\s+/g, ' ').toLocaleLowerCase('id');

        function add(value) {
            const clean = value.trim().replace(/\s+/g, ' ');
            if (!clean || selected.length >= 5 || selected.some((item) => normalized(item) === normalized(clean))) return;
            const canonical = suggestions.find((item) => normalized(item) === normalized(clean)) || clean;
            selected.push(canonical.slice(0, 40));
            if (input) input.value = '';
            render();
        }

        function remove(index) {
            selected.splice(index, 1);
            render();
            input?.focus();
        }

        function renderSuggestions() {
            if (!suggestionBox || !input) return;
            const query = normalized(input.value);
            const matches = suggestions
                .filter((item) => !selected.some((current) => normalized(current) === normalized(item)))
                .filter((item) => query === '' || normalized(item).includes(query))
                .slice(0, 8);
            suggestionBox.replaceChildren();
            matches.forEach((item) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = item;
                button.addEventListener('mousedown', (event) => event.preventDefault());
                button.addEventListener('click', () => add(item));
                suggestionBox.append(button);
            });
            suggestionBox.hidden = matches.length === 0;
        }

        function render() {
            chips?.replaceChildren();
            selected.forEach((item, index) => {
                const chip = document.createElement('span');
                chip.textContent = item;
                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.setAttribute('aria-label', `Hapus kategori ${item}`);
                removeButton.textContent = '×';
                removeButton.addEventListener('click', () => remove(index));
                chip.append(removeButton);
                chips?.append(chip);
            });
            if (input) input.disabled = selected.length >= 5;
            renderSuggestions();
            actions.refreshPreview();
        }

        input?.addEventListener('focus', renderSuggestions);
        input?.addEventListener('input', () => {
            if (input.value.includes(',')) {
                input.value.split(',').forEach(add);
            }
            renderSuggestions();
        });
        input?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                add(input.value.replace(/,$/, ''));
            }
            if (event.key === 'Backspace' && input.value === '' && selected.length > 0) {
                remove(selected.length - 1);
            }
            if (event.key === 'Escape' && suggestionBox) suggestionBox.hidden = true;
        });
        input?.addEventListener('blur', () => {
            window.setTimeout(() => {
                if (input.value.trim()) add(input.value);
                if (suggestionBox) suggestionBox.hidden = true;
            }, 120);
        });

        render();
        return { get: () => [...selected] };
    }

    return { setupCategories };
}
