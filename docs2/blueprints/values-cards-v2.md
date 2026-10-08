# MAP-V2-14 — Values cards integration

Status: implementation submitted for review; runtime/browser proof not yet independently certified.

## FACT

- Baseline main: 0f33fbd (PR #74), 20 alternating background rows at 4.5s.
- Current V2 Values section: 500svh scroll-driven white SVG path, two-line heading, shared Program/Values type plane.
- Legacy Values source: four localized Q/I/G/N text cards with CSS3D front/back; old 670svh scroll controller must not be imported.
- Source of truth: lang/{id,en,ar}/home.php under nilai_sekolah.items.

## DECISION

Render four semantic article cards via LandingValuesPresenter with escaped localized fields and 01–04 identifiers.
Move front/back identity and ornament into V2, not legacy Three.js/controller.
Use exactly one existing 500svh scroll world and line. Line-ready layout overlays cards at distinct responsive positions.
Desktop >=1280px uses four-card sticky deck with progress-driven reversible stack/fan/flip/upright/exit.
Mobile uses four vertically separated cards; tablet 768–1279px uses a 2x2 layout.
Scroll progress on cards is set by a small controller using requestAnimationFrame on scroll only; no new dependency or continuous loop.
No-JS, reduced-motion, absent GSAP/IO: articles stay readable in natural grid flow.
No card photos/canvas; one decorative back pattern is reused from canonical media config.

## UNCHANGED

Program dialog and its kinetic type, 20 background lines, 4.5s opposing directions,
Program/Values morph, heading introduction and exact SVG path/draw owner.

## PROOF PLAN

- Run PHP tests: tests/Feature/LandingValuesV2Test.php for 3 locales/4 cards/escaping.
- Run JS syntax/build, scope regression and existing Program/Values Chromium browser suites.
- Check mobile 360,390,640; tablet 768,1024; desktop 1280,1440,1536; ID EN AR.
- Verify 200% zoom, short landscape, RTL font clipping, no horizontal overflow, pagehide/bfcache, reduced motion, missing GSAP, line reversibility, browser engines.
- Current branch contains implementation; no green/browser performance claim is implied without executing these gates.
