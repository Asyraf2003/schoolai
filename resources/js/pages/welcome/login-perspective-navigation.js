function initLoginPerspectiveNavigation() {
  var loginItems = Array.prototype.slice.call(document.querySelectorAll('.nav-login'));
  if (!loginItems.length) return;

  var triggers = [];

  loginItems.forEach(function (item) {
    var trigger = item.querySelector('.nav-link');
    if (!trigger) return;

    item.classList.remove('nav-mega', 'is-open');
    item.removeAttribute('data-nav-mega');

    trigger.removeAttribute('data-nav-mega-toggle');
    trigger.classList.remove('nav-mega__trigger');
    trigger.classList.add('nav-login__trigger');
    trigger.setAttribute('data-login-perspective-open', '');
    trigger.setAttribute('aria-controls', 'loginPerspectiveNav');
    trigger.setAttribute('aria-expanded', 'false');
    trigger.removeAttribute('aria-haspopup');

    item.querySelectorAll('[data-nav-mega-panel]').forEach(function (panel) {
      panel.remove();
    });

    triggers.push(trigger);
  });

  if (!triggers.length) return;

  var locale = (document.documentElement.lang || 'id').toLowerCase();
  var labels = locale.indexOf('ar') === 0
    ? {
        admin: 'تسجيل الدخول كمسؤول',
        guru: 'تسجيل الدخول كمعلم',
        murid: 'تسجيل الدخول كطالب',
      }
    : locale.indexOf('en') === 0
      ? {
          admin: 'Login as Admin',
          guru: 'Login as Teacher',
          murid: 'Login as Student',
        }
      : {
          admin: 'Login sebagai Admin',
          guru: 'Login sebagai Guru',
          murid: 'Login sebagai Murid',
        };

  var portalUrl = new URL(triggers[0].href, window.location.href);
  var loginPath = portalUrl.pathname.replace(/\/+$/, '');
  var roles = [
    { label: labels.admin, href: portalUrl.origin + loginPath + '/admin' },
    { label: labels.guru, href: portalUrl.origin + loginPath + '/guru' },
    { label: labels.murid, href: portalUrl.origin + loginPath + '/murid' },
  ];

  var nav = document.createElement('nav');
  nav.id = 'loginPerspectiveNav';
  nav.className = 'login-perspective__nav outer-nav left vertical';
  nav.setAttribute('data-login-perspective-nav', '');
  nav.setAttribute('aria-label', triggers[0].getAttribute('aria-label') || 'Login');
  nav.hidden = true;

  roles.forEach(function (role) {
    var link = document.createElement('a');
    link.href = role.href;
    link.textContent = role.label;
    nav.appendChild(link);
  });

  var body = document.body;
  var root = document.createElement('div');
  var container = document.createElement('div');
  var wrapper = document.createElement('div');

  root.className = 'login-perspective effect-airbnb';
  root.setAttribute('data-login-perspective', '');
  container.className = 'login-perspective__container';
  container.setAttribute('data-login-perspective-container', '');
  wrapper.className = 'login-perspective__wrapper';
  wrapper.setAttribute('data-login-perspective-wrapper', '');

  Array.prototype.slice.call(body.childNodes).forEach(function (node) {
    wrapper.appendChild(node);
  });

  container.appendChild(wrapper);
  root.appendChild(container);
  root.appendChild(nav);
  body.appendChild(root);

  var docScroll = 0;
  var isOpen = false;
  var closeTimer = null;

  function scrollY() {
    return window.pageYOffset || document.documentElement.scrollTop || 0;
  }

  function closeMobileNavigation() {
    document.dispatchEvent(new CustomEvent('mobile-navigation:request-close', {
      detail: { immediate: true },
    }));
  }

  function restorePage() {
    window.clearTimeout(closeTimer);
    closeTimer = null;

    root.classList.remove('modalview');
    container.classList.remove('transform');
    wrapper.style.top = '0px';
    nav.hidden = true;
    isOpen = false;

    document.body.scrollTop = docScroll;
    document.documentElement.scrollTop = docScroll;
    window.scrollTo(0, docScroll);

    triggers.forEach(function (trigger) {
      trigger.setAttribute('aria-expanded', 'false');
    });
  }

  function closeMenu() {
    if (!isOpen || !root.classList.contains('animate')) return;

    container.classList.add('transform');

    var finished = false;
    var onTransitionEnd = function (event) {
      if (finished || event.target !== container || event.propertyName.indexOf('transform') === -1) {
        return;
      }

      finished = true;
      container.removeEventListener('transitionend', onTransitionEnd);
      restorePage();
    };

    container.addEventListener('transitionend', onTransitionEnd);
    root.classList.remove('animate');

    closeTimer = window.setTimeout(function () {
      if (finished) return;
      finished = true;
      container.removeEventListener('transitionend', onTransitionEnd);
      restorePage();
    }, 460);
  }

  function openMenu(event) {
    event.preventDefault();
    event.stopPropagation();
    if (isOpen) return;

    closeMobileNavigation();
    docScroll = scrollY();
    wrapper.style.top = (docScroll * -1) + 'px';

    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;

    nav.hidden = false;
    root.classList.add('modalview');
    isOpen = true;

    triggers.forEach(function (trigger) {
      trigger.setAttribute('aria-expanded', 'true');
    });

    window.setTimeout(function () {
      root.classList.add('animate');
    }, 25);
  }

  triggers.forEach(function (trigger) {
    trigger.addEventListener('click', openMenu);
  });

  container.addEventListener('click', closeMenu);

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && isOpen) {
      event.preventDefault();
      closeMenu();
    }
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initLoginPerspectiveNavigation, { once: true });
} else {
  initLoginPerspectiveNavigation();
}
