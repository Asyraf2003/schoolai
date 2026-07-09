import { qs } from '../core/dom.js';

// Page module login: tempat logic login kecil, misalnya fokus field error pertama.
export function mount() {
    const firstError = qs('.auth-login__error, .field-error');
    const firstInput = qs('input[autofocus]');

    if (firstError) {
        firstError.scrollIntoView({ block: 'center', behavior: 'smooth' });
        return;
    }

    firstInput?.focus();
}
