function renderErrors(container, errors) {
    container.replaceChildren();
    var messages = Object.values(errors || {}).flat();

    messages.forEach(function (message) {
        var paragraph = document.createElement('p');
        paragraph.textContent = String(message);
        container.appendChild(paragraph);
    });
}

document.querySelectorAll('[data-student-password-form]').forEach(function (form) {
    var button = form.querySelector('button[type="submit"]');
    var status = document.querySelector('[data-student-password-status]');
    var errors = form.querySelector('[data-student-password-errors]');

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!form.reportValidity() || button.disabled) return;

        button.disabled = true;
        button.textContent = button.dataset.loadingLabel || 'Menyimpan…';
        status.textContent = '';
        renderErrors(errors, {});

        try {
            var response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: new FormData(form),
                credentials: 'same-origin'
            });
            var payload = await response.json();

            if (!response.ok) {
                renderErrors(errors, payload.errors || { form: [payload.message] });
            } else {
                form.reset();
                status.textContent = payload.message;
            }
        } catch (error) {
            renderErrors(errors, { form: ['Layanan tidak dapat dihubungi. Coba lagi.'] });
        }

        button.disabled = false;
        button.textContent = button.dataset.idleLabel || 'Simpan Password';
    });
});
