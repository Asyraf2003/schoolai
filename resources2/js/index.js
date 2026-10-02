import { mountHero } from './sections/hero.js';

const hero = document.querySelector('[data-v2-section="hero"]');
if (hero) mountHero(hero);
