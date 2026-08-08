# Homepage Program — Kinetic Type Transition Blueprint

Status: `APPROVED_FOR_IMPLEMENTATION`
Date: 2026-08-08
Owner decision: replace the current Program journey completely.
Reference: `https://github.com/codrops/KineticTypePageTransition`
Reference license: MIT.

## FACT

- The reference is a click-driven kinetic typography page-transition concept.
- Its core choreography is: staggered media items -> items fade/translate away -> oversized repeated type scales/rotates and travels laterally -> selected detail enters -> back action reverses the transition.
- The reference implementation uses GSAP.
- SchoolAI already owns a dedicated Program Vite entry and four Program JS surface modules plus five Program CSS modules.
- The homepage supports ID/EN/AR, LTR/RTL, compact and wide layouts, reduced motion, Chromium and WebKit.
- Program must remain an independent section below About/Visi/Misi.

## GOAL

Replace the existing scroll-journey Program with a six-program interactive kinetic-type showcase:

1. PG — Playgroup
2. TK — Kindergarten
3. SD — Islamic Elementary School
4. TQ — Qur’an Memorization
5. MB — Language Partner
6. LT — Literacy & Library

The idle composition shows all six program cards. Activating one card runs a kinetic typography transition and opens a focused full-viewport program detail. Back/Escape restores the six-card composition.

## REFERENCE TRANSLATION

Adopt:

- staggered media-card composition;
- repeated oversized kinetic type as the transition field;
- scale + quarter-turn rotation of the type field;
- staggered horizontal type-line travel;
- alternating card exit motion;
- opposite image-wrap/image reveal for the detail;
- staggered detail-copy entrance;
- reversible back transition.

Do not copy:

- Codrops copy, images, fonts or visual identity;
- global body overflow ownership from the demo;
- Adobe Typekit dependencies;
- GSAP dependency when native WAAPI is sufficient;
- exact colors or dimensions.

## CONTENT CONTRACT

All public Program copy is locale-owned in:

- `lang/id/home_program.php`
- `lang/en/home_program.php`
- `lang/ar/home_program.php`

Blade contains no locale switch/match for Program public copy.

Each item owns:

- code;
- eyebrow/category;
- title;
- summary;
- full description;
- optional next program code;
- remote Unsplash media URL and focal position.

## INTERACTION CONTRACT

Idle:

- six cards are visible and keyboard focusable;
- wide composition uses a staggered kinetic editorial arrangement;
- compact composition becomes a readable two-column/one-column flow.

Open:

- clicked item is recorded as the active item;
- cards exit with alternating vertical displacement;
- kinetic type scales and rotates while lines travel across the viewport;
- selected detail becomes visible before the type transition fully clears;
- detail copy and image reveal with bounded WAAPI animations;
- document scroll is locked only while the fixed detail experience is open.

Close:

- back button and Escape both close;
- detail elements exit;
- kinetic type reverses;
- cards return;
- scroll state is restored;
- keyboard focus returns to the originating card.

## RTL

- semantic text direction follows document `dir`;
- Arabic uses the existing Cairo stack;
- quarter-turn and lateral type travel mirror direction in RTL;
- visual media ordering remains the same six-program logical sequence;
- keyboard behavior is identical.

## REDUCED MOTION

With `prefers-reduced-motion: reduce`:

- no kinetic scale/rotation;
- no large staggered travel;
- detail opens/closes with immediate state changes or a minimal opacity transition;
- all content remains available.

## NO-JS / FAILURE FALLBACK

Before enhancement:

- all six cards remain readable;
- each card includes its full description in normal document flow;
- no fixed overlay blocks page navigation.

JS enhancement may hide fallback-only description after mount.

## PERFORMANCE

- no new runtime dependency;
- use CSS transforms/opacity and WAAPI only;
- remote images are lazy except where the browser chooses otherwise;
- animation does not write document scroll each frame;
- no WebGL;
- no continuous RAF loop.

## SCOPE

Allowed:

- Program Blade;
- Program CSS modules and their existing entry;
- Program JS modules and existing entry;
- focused Program feature test;
- new Program locale files;
- Program blueprint/current-state docs.

Forbidden:

- About/Visi/Misi;
- Hero;
- Values;
- Gallery;
- Articles;
- navbar/footer;
- unrelated source-structure failures.

## PROOF GATE

Static/local:

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- focused Program test
- full `php artisan test`

Runtime:

- six idle cards render;
- every card opens the correct detail;
- back and Escape close correctly;
- focus restoration works;
- repeated open/close does not leave body scroll locked;
- ID/EN/AR and RTL are correct;
- 360 through 1920 widths are usable;
- Chromium and WebKit forward/reverse interactions are clean;
- reduced motion remains readable.

Until browser/runtime proof exists, status is `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`.