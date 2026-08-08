import '../../../css/pages/welcome/program-showcase-desktop.css';
import { mountProgramJourney } from '../../surfaces/home/program-journey/controller.js';

function startProgramJourney() {
  const root = document.querySelector('[data-program-kinetic]');
  if (root) mountProgramJourney(root);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', startProgramJourney, { once: true });
} else {
  startProgramJourney();
}
