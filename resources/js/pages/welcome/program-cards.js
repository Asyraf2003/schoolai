import '../../../css/pages/welcome/program-showcase-desktop.css';
import { mountProgramJourney } from '../../surfaces/home/program-journey/controller.js';

function startProgramJourney() {
  var root = document.querySelector('[data-program-journey]');
  if (root) mountProgramJourney(root);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', startProgramJourney, { once: true });
} else {
  startProgramJourney();
}
