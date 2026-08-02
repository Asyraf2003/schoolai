function setButtonState(button, state, seconds) {
    if (!button) return;

    button.disabled = state !== 'idle';
    if (state === 'loading') {
        button.textContent = button.dataset.loadingLabel || 'Loading…';
    } else if (state === 'locked') {
        var template = button.dataset.lockedLabel || 'Try again in :seconds seconds';
        button.textContent = template.replace(':seconds', String(seconds));
    } else {
        button.textContent = button.dataset.idleLabel || 'Login';
    }
}

function startCountdown(form, seconds) {
    var button = form.querySelector('button[type="submit"]');
    var remaining = Math.max(0, Number(seconds) || 0);

    if (!remaining) {
        setButtonState(button, 'idle');
        return;
    }

    setButtonState(button, 'locked', remaining);
    var timer = window.setInterval(function () {
        remaining -= 1;
        if (remaining <= 0) {
            window.clearInterval(timer);
            setButtonState(button, 'idle');
            return;
        }
        setButtonState(button, 'locked', remaining);
    }, 1000);
}

function mountStudentLogin(form) {
    var button = form.querySelector('button[type="submit"]');
    var message = document.querySelector('[data-auth-message]');
    var password = form.querySelector('input[name="password"]');

    startCountdown(form, form.dataset.retryAfter);

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!form.reportValidity() || button.disabled) return;

        setButtonState(button, 'loading');
        if (message) message.textContent = '';

        try {
            var response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: new FormData(form),
                credentials: 'same-origin'
            });
            var payload = await response.json();

            if (response.ok && payload.redirect) {
                window.location.assign(payload.redirect);
                return;
            }

            if (message) message.textContent = payload.message || form.dataset.failureMessage;
            if (password) password.value = '';
            if (response.status === 429) {
                startCountdown(form, payload.retry_after);
                return;
            }
        } catch (error) {
            if (message) message.textContent = form.dataset.networkMessage;
            if (password) password.value = '';
        }

        setButtonState(button, 'idle');
    });
}

document.querySelectorAll('[data-async-auth-form]').forEach(mountStudentLogin);

document.querySelectorAll('[data-google-login]').forEach(function (link) {
    link.addEventListener('click', function () {
        link.setAttribute('aria-disabled', 'true');
        var label = link.querySelector('span');
        if (label) label.textContent = link.dataset.loadingLabel || 'Loading…';
    });
});
