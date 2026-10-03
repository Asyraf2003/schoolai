export function onMediaQueryChange(query, listener) {
    if (typeof query.addEventListener === 'function') {
        query.addEventListener('change', listener);
        return function () { query.removeEventListener('change', listener); };
    }

    query.addListener(listener);
    return function () { query.removeListener(listener); };
}

export function initMegaMenus() {
    var header = document.getElementById('navbar');
    var mobileMenu = document.getElementById('navMenu');
    var menus = Array.prototype.slice.call(document.querySelectorAll('[data-nav-mega]'));

    if (!header) return;

    function setMenuState(menu, isOpen, focusFirstLink) {
        var toggle = menu.querySelector('[data-nav-mega-toggle]');
        var panel = menu.querySelector('[data-nav-mega-panel]');
        if (!toggle || !panel) return;

        menu.classList.toggle('is-open', isOpen);
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        panel.inert = !isOpen;

        if (isOpen && focusFirstLink) {
            var firstLink = panel.querySelector('a[href]');
            if (firstLink) firstLink.focus();
        }
    }

    function syncHeaderState() {
        var hasOpenMegaMenu = menus.some(function (menu) {
            return menu.classList.contains('is-open');
        });
        var hasOpenMobileMenu = mobileMenu && mobileMenu.classList.contains('active');

        header.classList.toggle('has-open-menu', hasOpenMegaMenu || hasOpenMobileMenu);
    }

    if (mobileMenu && typeof MutationObserver === 'function') {
        var mobileMenuObserver = new MutationObserver(syncHeaderState);
        mobileMenuObserver.observe(mobileMenu, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

    function closeMenus(exceptMenu) {
        menus.forEach(function (menu) {
            if (menu !== exceptMenu) setMenuState(menu, false, false);
        });
        syncHeaderState();
    }

    menus.forEach(function (menu) {
        var toggle = menu.querySelector('[data-nav-mega-toggle]');
        var panel = menu.querySelector('[data-nav-mega-panel]');
        if (!toggle || !panel) return;

        setMenuState(menu, false, false);

        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var shouldOpen = !menu.classList.contains('is-open');
            closeMenus(menu);
            setMenuState(menu, shouldOpen, false);
            syncHeaderState();

            var languageMenu = document.querySelector('.nav-language.is-open');
            if (languageMenu) {
                languageMenu.classList.remove('is-open');
                var languageButton = languageMenu.querySelector('.nav-language__button');
                if (languageButton) languageButton.setAttribute('aria-expanded', 'false');
            }
        });

        toggle.addEventListener('keydown', function (event) {
            if (event.key !== 'ArrowDown') return;
            event.preventDefault();
            closeMenus(menu);
            setMenuState(menu, true, true);
            syncHeaderState();
        });

        panel.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;
            event.preventDefault();
            event.stopPropagation();
            setMenuState(menu, false, false);
            syncHeaderState();
            toggle.focus();
        });

        panel.querySelectorAll('a[href]').forEach(function (link) {
            link.addEventListener('click', function () {
                setMenuState(menu, false, false);
                syncHeaderState();
            });
        });
    });

    document.addEventListener('click', function (event) {
        if (event.target.closest('[data-nav-mega]')) return;
        closeMenus();
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.nav-language__button')) return;
        closeMenus();
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        var openMenu = menus.find(function (menu) {
            return menu.classList.contains('is-open');
        });

        if (!openMenu) return;

        var toggle = openMenu.querySelector('[data-nav-mega-toggle]');
        closeMenus();
        if (toggle) toggle.focus();
    });

    syncHeaderState();
}
