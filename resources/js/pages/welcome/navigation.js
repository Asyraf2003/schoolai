import { initializeNavigationMenus } from './navigation-menus.js';
import { initializeNavigationMegaMenus } from './navigation-mega.js';
import { initializeNavigationState } from './navigation-state.js';

document.addEventListener('DOMContentLoaded', function () {
  initializeNavigationMenus();
  initializeNavigationMegaMenus();
  initializeNavigationState();
});
