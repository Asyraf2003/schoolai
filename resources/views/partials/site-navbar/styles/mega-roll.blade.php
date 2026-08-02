.nav-shell [data-nav-roll] {
  position: relative;
  display: inline-grid;
  max-width: 100%;
  line-height: 1.1;
}

.nav-shell .nav-roll__viewport {
  position: relative;
  display: grid;
  max-width: 100%;
  overflow: hidden;
  perspective: 720px;
  perspective-origin: 50% 50%;
}

.nav-shell .nav-roll__layer {
  grid-area: 1 / 1;
  display: inline-flex;
  white-space: pre;
  transform-style: preserve-3d;
}

.nav-shell .nav-roll__layer--clone {
  position: absolute;
  inset: 0 auto auto 0;
}

html[dir="rtl"] .nav-shell .nav-roll__layer--clone {
  inset: 0 0 auto auto;
}

.nav-shell .nav-roll__char {
  display: inline-block;
  opacity: 1;
  backface-visibility: hidden;
  transform: translateY(0) rotateX(0deg);
  transform-origin: 50% 100%;
  will-change: transform, opacity;
}

.nav-shell .nav-roll__layer--clone .nav-roll__char {
  opacity: 0;
  transform: translateY(-105%) rotateX(90deg);
  transform-origin: 50% 0%;
}

.nav-shell .nav-roll--arabic .nav-roll__char {
  backface-visibility: visible;
  transform: none;
  will-change: opacity, clip-path, filter;
}

.nav-shell .nav-roll--arabic .nav-roll__layer--clone .nav-roll__char {
  opacity: 0;
  transform: none;
  -webkit-clip-path: inset(0 0 0 100%);
  clip-path: inset(0 0 0 100%);
  filter: brightness(1.55) drop-shadow(0 0 0.34em currentColor);
}

.nav-shell .nav-roll__accessible {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

@media (min-width: 1181px) {
  html[dir="rtl"] .nav-shell [data-nav-roll="main"] {
    line-height: 1.28;
  }

  html[dir="rtl"] .nav-shell [data-nav-roll="main"] .nav-roll__viewport {
    padding-block-end: 0.2em;
    margin-block-end: -0.2em;
  }
}

@media (prefers-reduced-motion: reduce) {
  .nav-shell .nav-roll__layer--clone {
    display: none;
  }

  .nav-shell .nav-roll__char {
    opacity: 1;
    transform: none;
    will-change: auto;
  }
}
