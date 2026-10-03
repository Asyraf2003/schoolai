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

function messageFor(form) {
    var scope = form.closest('[data-auth-scope]');
    return scope ? scope.querySelector('[data-auth-message]') : document.querySelector('[data-auth-message]');
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

export function mountStudentLogin(form) {
    if (!form || form.dataset.authMounted === '1') return;
    form.dataset.authMounted = '1';

    var button = form.querySelector('button[type="submit"]');
    var message = messageFor(form);
    var password = form.querySelector('input[name="password"]');

    startCountdown(form, form.dataset.retryAfter);

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!form.reportValidity() || button.disabled) return;

        setButtonState(button, 'loading');
        if (message) {
            message.textContent = '';
            message.removeAttribute('data-auth-tone');
        }

        try {
            var response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: new FormData(form),
                credentials: 'same-origin'
            });
            var payload = await response.json();

            if (response.ok && payload.redirect) {
                if (message) {
                    message.textContent = form.dataset.successMessage || '';
                    message.setAttribute('data-auth-tone', 'success');
                }
                window.setTimeout(function () {
                    window.location.assign(payload.redirect);
                }, 450);
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

export function resetGoogleLogin(link) {
    if (!link) return;
    link.removeAttribute('aria-disabled');
    var label = link.querySelector('span');
    if (label && link.dataset.idleLabel) label.textContent = link.dataset.idleLabel;
}

function popupToken() {
    if (window.crypto && typeof window.crypto.randomUUID === 'function') {
        return window.crypto.randomUUID();
    }

    var bytes = new Uint8Array(16);
    window.crypto.getRandomValues(bytes);
    return Array.prototype.map.call(bytes, function (byte) {
        return byte.toString(16).padStart(2, '0');
    }).join('');
}

export function mountGoogleLogin(link) {
    if (!link || link.dataset.authMounted === '1') return;
    link.dataset.authMounted = '1';

    var label = link.querySelector('span');
    if (label && !link.dataset.idleLabel) link.dataset.idleLabel = label.textContent || '';

    link.addEventListener('click', function (event) {
        if (link.getAttribute('aria-disabled') === 'true') {
            event.preventDefault();
            return;
        }

        if (link.dataset.googlePopup !== '1') {
            link.setAttribute('aria-disabled', 'true');
            if (label) label.textContent = link.dataset.loadingLabel || 'Loading…';
            return;
        }

        event.preventDefault();
        var token = popupToken();
        var popupUrl = new URL(link.href, window.location.href);
        popupUrl.searchParams.set('popup', '1');
        popupUrl.searchParams.set('popup_token', token);
        var popup = window.open(
            popupUrl.toString(),
            'schoolai-google-auth',
            'popup=yes,width=520,height=680,resizable=yes,scrollbars=yes'
        );

        if (!popup) {
            window.location.assign(link.href);
            return;
        }

        link.setAttribute('aria-disabled', 'true');
        if (label) label.textContent = link.dataset.loadingLabel || 'Loading…';
        window.dispatchEvent(new CustomEvent('auth:google-popup-opened', {
            detail: { popup: popup, token: token, role: link.dataset.authRole || null, link: link }
        }));
    });
}

export function mountPublicLogin(root) {
    var scope = root || document;
    scope.querySelectorAll('[data-async-auth-form]').forEach(mountStudentLogin);
    scope.querySelectorAll('[data-google-login]').forEach(mountGoogleLogin);
}

function mountDocumentLogin() {
    mountPublicLogin(document);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountDocumentLogin, { once: true });
} else {
    mountDocumentLogin();
}
