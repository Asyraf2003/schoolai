export function createDesktopHeaderVisibility(navbar, hero, getDocumentTop) {
  var desktopHeader = window.matchMedia('(min-width: 1181px)');
  var hidden = false;
  var lastScrollY = window.scrollY;
  var direction = 0;
  var distance = 0;

  function setHidden(shouldHide) {
    if (!navbar || shouldHide === hidden) return;

    hidden = shouldHide;
    navbar.classList.toggle('is-scroll-hidden', shouldHide);
  }

  function reset(scrollY) {
    lastScrollY = scrollY;
    direction = 0;
    distance = 0;
  }

  function show() {
    setHidden(false);
    reset(window.scrollY);
  }

  function update() {
    if (!navbar) return;

    var currentScrollY = window.scrollY;
    var delta = currentScrollY - lastScrollY;
    lastScrollY = currentScrollY;

    if (!desktopHeader.matches || !hero || navbar.classList.contains('has-open-menu')) {
      show();
      return;
    }

    var heroBottom = getDocumentTop(hero) + Math.max(hero.offsetHeight, hero.scrollHeight, 1);
    var outsideHero = currentScrollY + navbar.offsetHeight >= heroBottom;

    if (!outsideHero) {
      show();
      return;
    }

    if (!delta) return;

    var nextDirection = delta > 0 ? 1 : -1;
    if (nextDirection !== direction) {
      direction = nextDirection;
      distance = 0;
    }

    distance += Math.abs(delta);
    if (distance < 12) return;

    setHidden(direction > 0);
    distance = 0;
  }

  if (navbar) navbar.addEventListener('focusin', show);

  return { update: update, show: show };
}
