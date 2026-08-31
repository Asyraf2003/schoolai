import { resetGoogleLogin } from '../public-login.js';

export function mountPerspectiveGoogleBridge(panel, activateState, stateElement, currentState) {
    var activePopup = null;
    var activeGoogleLink = null;
    var popupWatch = null;
    var popupMessageReceived = false;

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
            }, 300);
        }, 400);
    }

    window.addEventListener('auth:google-popup-opened', function (event) {
        if (!event.detail || !panel.contains(event.detail.link)) return;
        activePopup = event.detail.popup;
        activeGoogleLink = event.detail.link;
        popupMessageReceived = false;
        watchPopup();
    });

    window.addEventListener('message', function (event) {
        if (event.origin !== window.location.origin || !event.data) return;
        if (event.data.source !== 'schoolai-google-auth') return;
        if (activePopup && event.source !== activePopup) return;

        popupMessageReceived = true;
        stopPopupWatch();
        var role = event.data.role || currentState();
        activateState(role);
        var state = stateElement(role);
        var message = state ? state.querySelector('[data-auth-message]') : null;
        resetGoogleLogin(activeGoogleLink);

        if (message) {
            message.textContent = event.data.message || '';
            if (event.data.ok) message.setAttribute('data-auth-tone', 'success');
        }

        activePopup = null;
        activeGoogleLink = null;
        if (event.data.ok && event.data.redirect) {
            window.setTimeout(function () {
                window.location.assign(event.data.redirect);
            }, 450);
        }
    });
}
