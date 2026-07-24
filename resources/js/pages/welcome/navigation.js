import { initializeNavigationMenus } from './navigation-menus.js';
import { initializeNavigationState } from './navigation-state.js';

document.addEventListener('DOMContentLoaded', function () {
  initializeNavigationMenus();
  initializeNavigationState();
});
