import { loadGsapLibrary } from './gsap-loader.js';

export function loadProgramGsap(signal) {
    return loadGsapLibrary('gsap', signal);
}
