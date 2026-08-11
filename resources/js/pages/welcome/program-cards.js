import '../../../css/pages/welcome/program-showcase-desktop.css';
import { mountProgramFormation } from '../../surfaces/home/program-journey/formation.js';
import { mountProgramJourney } from '../../surfaces/home/program-journey/controller.js';

function startProgramJourney() {
  const root = document.querySelector('[data-program-kinetic]');
  if (!root) return;

  mountProgramFormation(root);
  mountProgramJourney(root);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', startProgramJourney, { once: true });
} else {
  startProgramJourney();
}
