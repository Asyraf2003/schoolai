import '../../../css/pages/welcome/program-showcase-desktop.css';
import { mountProgramFormation } from '../../surfaces/home/program-journey/formation.js';
import { mountProgramJourney } from '../../surfaces/home/program-journey/controller.js';

let preparationPromise = null;

export function prepareHomepageProgram() {
  if (preparationPromise) return preparationPromise;

  const root = document.querySelector('[data-program-kinetic]');
  if (!root) return Promise.resolve(null);

  mountProgramFormation(root);
  const journeyCleanup = mountProgramJourney(root);

  preparationPromise = Promise.resolve(journeyCleanup.ready)
    .then(() => root);

  return preparationPromise;
}
