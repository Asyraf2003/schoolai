import { mountPublicLogin, resetGoogleLogin } from './public-login.js';

function initWelcomeLoginPerspective() {
    var template = document.querySelector('[data-login-perspective-template]');
    var triggers = Array.prototype.slice.call(document.querySelectorAll('.nav-login .nav-link'));
    if (!template || !triggers.length) return;

    var panel = template.content.firstElementChild.cloneNode(true);
    var states = Array.prototype.slice.call(panel.querySelectorAll('[data-login-perspective-state]'));
    var body = document.body;
    var root = document.createElement('div');
    var container = document.createElement('div');
    var wrapper = document.createElement('div');
    var docScroll = 0;
    var isOpen = false;
    var closeTimer = null;
    var activePopup = null;
    var activeGoogleLink = null;
    var popupWatch = null;
    var popupMessageReceived = false;
    var currentState = 'choices';

    root.className = 'login-perspective effect-airbnb';
    root.setAttribute('data-login-perspective', '');
    container.className = 'login-perspective__container';
    container.setAttribute('data-login-perspective-container', '');
    wrapper.className = 'login-perspective__wrapper';
    wrapper.setAttribute('data-login-perspective-wrapper', '');
    panel.hidden = true;

    triggers.forEach(function (trigger) {
        trigger.setAttribute('data-login-perspective-open', '');
        trigger.setAttribute('aria-controls', panel.id);
        trigger.setAttribute('aria-expanded', 'false');
    });

    Array.prototype.slice.call(body.childNodes).forEach(function (node) {
        wrapper.appendChild(node);
    });

    container.appendChild(wrapper);
    root.appendChild(container);
    root.appendChild(panel);
    body.appendChild(root);
    mountPublicLogin(panel);

    function scrollY() {
        return window.pageYOffset || document.documentElement.scrollTop || 0;
    }

    function stateElement(name) {
        return states.find(function (state) {
            return state.getAttribute('data-login-perspective-state') === name;
        });
    }

    function focusState(state) {
        var target = state.querySelector('button, a, input, h2');
        if (!target) return;
        if (target.matches('h2')) target.setAttribute('tabindex', '-1');
        target.focus({ preventScroll: true });
    }

    function activateState(name) {
        var next = stateElement(name) || stateElement('choices');
        currentState = next.getAttribute('data-login-perspective-state');
        states.forEach(function (state) {
            state.hidden = state !== next;
        });
        window.requestAnimationFrame(function () {
            focusState(next);
        });
    }

    function resetMessages() {
        panel.querySelectorAll('[data-auth-message]').forEach(function (message) {
            message.textContent = '';
            message.removeAttribute('data-auth-tone');
        });
        panel.querySelectorAll('[data-google-login]').forEach(resetGoogleLogin);
    }

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

    function openPerspective(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        if (isOpen) return;

        document.dispatchEvent(new CustomEvent('mobile-navigation:request-close', {
            detail: { immediate: true }
        }));
        docScroll = scrollY();
        wrapper.style.top = (docScroll * -1) + 'px';
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
        activateState('choices');
        panel.hidden = false;
        root.classList.add('modalview');
        isOpen = true;

        triggers.forEach(function (trigger) {
            trigger.setAttribute('aria-expanded', 'true');
        });

        window.setTimeout(function () {
            root.classList.add('animate');
        }, 25);
    }

    function restorePage() {
        window.clearTimeout(closeTimer);
        root.classList.remove('modalview');
        container.classList.remove('transform');
        wrapper.style.top = '0px';
        panel.hidden = true;
        isOpen = false;
        activateState('choices');
        resetMessages();
        document.body.scrollTop = docScroll;
        document.documentElement.scrollTop = docScroll;
        window.scrollTo(0, docScroll);
        triggers.forEach(function (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
        });
    }

    function closePerspective() {
        if (!isOpen || !root.classList.contains('animate')) return;
        container.classList.add('transform');
        root.classList.remove('animate');
        closeTimer = window.setTimeout(restorePage, 460);
    }

    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', openPerspective);
    });
    panel.querySelectorAll('[data-login-role-target]').forEach(function (button) {
        button.addEventListener('click', function () {
            activateState(button.getAttribute('data-login-role-target'));
        });
    });
    panel.querySelectorAll('[data-login-perspective-back]').forEach(function (button) {
        button.addEventListener('click', function () {
            activateState('choices');
        });
    });

    container.addEventListener('click', closePerspective);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && isOpen) closePerspective();
    });

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
        var role = event.data.role || currentState;
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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWelcomeLoginPerspective, { once: true });
} else {
    initWelcomeLoginPerspective();
}
