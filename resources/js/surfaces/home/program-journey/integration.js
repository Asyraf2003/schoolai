export function createProgramIntegration(root) {
  const visionRoot = document.querySelector('[data-vision-story]');

  function sync() {
    root.classList.remove('is-integrated');
    visionRoot?.classList.remove('has-integrated-program');
    if (visionRoot) {
      delete visionRoot.dataset.programStoryTravel;
      visionRoot.style.removeProperty('--program-story-travel');
    }
    return false;
  }

  function destroy() {
    sync();
  }

  return { sync, destroy };
}
