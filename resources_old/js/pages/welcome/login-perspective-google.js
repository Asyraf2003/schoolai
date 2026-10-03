import { resetGoogleLogin } from '../public-login.js';

export function mountPerspectiveGoogleBridge(panel, activateState, stateElement, currentState) {
    var activePopup = null;
    var activeGoogleLink = null;
    var activeToken = null;
    var popupWatch = null;
    var popupMessageReceived = false;
    var channel = 'BroadcastChannel' in window
        ? new BroadcastChannel('schoolai-google-auth')
        : null;

    function stopPopupWatch() {
        if (popupWatch) window.clearInterval(popupWatch);
        popupWatch = null;
    }

    function watchPopup() {
        stopPopupWatch();
        popupWatch = window.setInterval(function () {
            if (!activePopup || !activePopup.closed) return;
            stopPopupWatch();
            window.setTimeout(function () {
                if (!popupMessageReceived) resetGoogleLogin(activeGoogleLink);
                activePopup = null;
                activeGoogleLink = null;
                activeToken = null;
            }, 300);
        }, 400);
    }

    function acceptResult(payload) {
        if (!payload || payload.source !== 'schoolai-google-auth') return;
        if (!activeToken || payload.token !== activeToken) return;

        popupMessageReceived = true;
        stopPopupWatch();
        var role = payload.role || currentState();
        activateState(role);
        var state = stateElement(role);
        var message = state ? state.querySelector('[data-auth-message]') : null;
        resetGoogleLogin(activeGoogleLink);

        if (message) {
            message.textContent = payload.message || '';
            if (payload.ok) message.setAttribute('data-auth-tone', 'success');
        }

        activePopup = null;
        activeGoogleLink = null;
        activeToken = null;
        if (payload.ok && payload.redirect) {
            window.setTimeout(function () {
                window.location.assign(payload.redirect);
            }, 450);
        }
    }

    window.addEventListener('auth:google-popup-opened', function (event) {
        if (!event.detail || !panel.contains(event.detail.link)) return;
        activePopup = event.detail.popup;
        activeGoogleLink = event.detail.link;
        activeToken = event.detail.token;
        popupMessageReceived = false;
        watchPopup();
    });

    window.addEventListener('message', function (event) {
        if (event.origin !== window.location.origin || !activePopup) return;
        if (event.source !== activePopup) return;
        acceptResult(event.data);
    });

    if (channel) {
        channel.addEventListener('message', function (event) {
            acceptResult(event.data);
        });
    }

    window.addEventListener('storage', function (event) {
        if (event.key !== 'schoolai-google-auth' || !event.newValue) return;
        try {
            acceptResult(JSON.parse(event.newValue));
        } catch (error) {
            // Ignore malformed cross-tab messages.
        }
    });
}
