export function createProgramIntegration(root, reducedMotion) {
  const visionRoot = document.querySelector('[data-vision-story]');
  const visionTrack = visionRoot?.querySelector('[data-vision-track]');
  const wide = window.matchMedia('(min-width: 1181px)');
  const home = document.createComment('program-home');
  root.parentNode?.insertBefore(home, root);

  function canIntegrate() {
    return wide.matches
      && !reducedMotion
      && document.documentElement.classList.contains('vision-motion-capable')
      && visionRoot
      && visionTrack;
  }

  function sync() {
    const integrate = canIntegrate();
    if (integrate && root.parentNode !== visionTrack) {
      visionTrack.appendChild(root);
      root.classList.add('is-integrated');
      visionRoot.classList.add('has-integrated-program');
      return true;
    }

    if (!integrate && root.parentNode === visionTrack && home.parentNode) {
      home.parentNode.insertBefore(root, home.nextSibling);
      root.classList.remove('is-integrated');
      visionRoot.classList.remove('has-integrated-program');
      delete visionRoot.dataset.programStoryTravel;
      visionRoot.style.removeProperty('--program-story-travel');
      return true;
    }

    return false;
  }

  function destroy() {
    if (root.parentNode === visionTrack && home.parentNode) {
      home.parentNode.insertBefore(root, home.nextSibling);
    }
    root.classList.remove('is-integrated');
    visionRoot?.classList.remove('has-integrated-program');
    if (visionRoot) {
      delete visionRoot.dataset.programStoryTravel;
      visionRoot.style.removeProperty('--program-story-travel');
    }
    home.remove();
  }

  return { sync, destroy };
}
