export const HERO_RIGHT_TO_LEFT = 1;
export const HERO_LEFT_TO_RIGHT = -1;

export function isHeroRtl() {
  return document.documentElement.dir === 'rtl';
}

export function automaticHeroDirection() {
  return isHeroRtl() ? HERO_LEFT_TO_RIGHT : HERO_RIGHT_TO_LEFT;
}

export function oppositeHeroDirection(direction) {
  return direction === HERO_RIGHT_TO_LEFT ? HERO_LEFT_TO_RIGHT : HERO_RIGHT_TO_LEFT;
}

export function dotHeroDirection(currentIndex, nextIndex, slideCount) {
  if (currentIndex === nextIndex || slideCount < 2) return automaticHeroDirection();

  var forward = (nextIndex - currentIndex + slideCount) % slideCount;
  var backward = (currentIndex - nextIndex + slideCount) % slideCount;
  var logicalForward = forward <= backward;
  var automatic = automaticHeroDirection();

  return logicalForward ? automatic : oppositeHeroDirection(automatic);
}
