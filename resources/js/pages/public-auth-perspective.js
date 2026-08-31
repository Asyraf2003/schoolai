function currentScrollY() {
    return window.pageYOffset || document.documentElement.scrollTop || 0;
}

function mountAuthPerspective(root) {
    var container = root.querySelector('[data-auth-perspective-container]');
    var wrapper = root.querySelector('[data-auth-perspective-wrapper]');
    var openButtons = Array.prototype.slice.call(root.querySelectorAll('[data-auth-perspective-open]'));
    var roleLinks = Array.prototype.slice.call(root.querySelectorAll('[data-auth-role-target]'));
    var panels = Array.prototype.slice.call(root.querySelectorAll('[data-auth-role-panel]'));
    var docScroll = 0;
    var closeTimer = null;

    if (!container || !wrapper || !roleLinks.length || !panels.length) return;

    function openMenu() {
        if (root.classList.contains('animate')) return;

        docScroll = currentScrollY();
        wrapper.style.top = (docScroll * -1) + 'px';
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
        root.classList.add('modalview');

        window.setTimeout(function () {
            root.classList.add('animate');
        }, 25);
    }

    function finishClose() {
        window.clearTimeout(closeTimer);
        root.classList.remove('modalview');
        container.classList.remove('transform');
        wrapper.style.top = '0px';
        document.body.scrollTop = docScroll;
        document.documentElement.scrollTop = docScroll;
        window.scrollTo(0, docScroll);
    }

    function closeMenu() {
        if (!root.classList.contains('animate')) return;

        container.classList.add('transform');

        var onEnd = function (event) {
            if (event.target !== container || event.propertyName.indexOf('transform') === -1) return;
            container.removeEventListener('transitionend', onEnd);
            finishClose();
        };

        container.addEventListener('transitionend', onEnd);
        root.classList.remove('animate');
        closeTimer = window.setTimeout(function () {
            container.removeEventListener('transitionend', onEnd);
            finishClose();
        }, 460);
    }

    function activateRole(role, href) {
        panels.forEach(function (panel) {
            var active = panel.getAttribute('data-auth-role-panel') === role;
            panel.hidden = !active;
        });

        if (href && window.history && window.history.replaceState) {
            window.history.replaceState(null, '', href);
        }

        window.setTimeout(function () {
            var activePanel = panels.find(function (panel) {
                return !panel.hidden;
            });
            var heading = activePanel ? activePanel.querySelector('h1') : null;
            if (heading) {
                heading.setAttribute('tabindex', '-1');
                heading.focus({ preventScroll: true });
            }
        }, 430);
    }

    openButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            openMenu();
        });
    });

    roleLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            var role = link.getAttribute('data-auth-role-target');
            activateRole(role, link.href);
            closeMenu();
        });
    });

    container.addEventListener('click', function (event) {
        if (!root.classList.contains('animate')) return;
        event.preventDefault();
        closeMenu();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && root.classList.contains('animate')) {
            closeMenu();
        }
    });

    if (root.getAttribute('data-auth-auto-open') === '1') {
        window.setTimeout(openMenu, 80);
    }
}

document.querySelectorAll('[data-auth-perspective]').forEach(mountAuthPerspective);
