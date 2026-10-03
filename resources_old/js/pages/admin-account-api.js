export const accountUrl = (template, id) => template.replace('__ACCOUNT__', String(id));

export function setFormBusy(form, busy) {
    form.setAttribute('aria-busy', busy ? 'true' : 'false');
    form.querySelectorAll('button, input, select').forEach((field) => {
        field.disabled = busy;
    });
}

export function showFormErrors(form, payload) {
    const owner = form.querySelector('[data-form-errors]');
    owner.replaceChildren();
    const messages = payload.errors
        ? Object.values(payload.errors).flat()
        : [payload.message || 'Permintaan tidak dapat diproses.'];

    messages.forEach((message) => {
        const item = document.createElement('p');
        item.textContent = String(message);
        owner.append(item);
    });
}

export async function sendAccountMutation(url, formData) {
    const token = document.querySelector('input[name="_token"]')?.value || '';
    const response = await fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
        },
    });
    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        const error = new Error(payload.message || 'Permintaan gagal.');
        error.payload = payload;
        throw error;
    }

    return payload;
}
