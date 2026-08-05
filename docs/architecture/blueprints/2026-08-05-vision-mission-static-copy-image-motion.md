# Vision/Mission Static Copy with Image Motion

State: `IMPLEMENTING`
Date: 2026-08-05
Repository: `Asyraf2003/schoolai`
Branch: `main`
Surface: homepage `#visi-misi`
Baseline: `91719bd35cfbe11b16c8047a3f1ac74c27d32ce4`

## Goal

Keep Vision and Mission copy permanently readable while restoring the decorative
image 2-3 scroll swap. Program content remains static and follows the image
stage normally.

## Owner decisions

- Vision and Mission copy never depends on JavaScript for visibility.
- No typography split, opacity reveal, lift, or scroll transform may target copy.
- Desktop `>=1181px`: Vision left, Mission right, Mission optically lower.
- Large tablet `1024-1180px`: Vision and Mission use two centered columns.
- Narrow tablet and phone `<=1023px`: Vision then Mission in a close stack.
- ID, EN, and AR copy boxes and text are centered.
- Only the image frame receives scroll motion.
- Image 2 starts in the frame; image 3 rises from below.
- The image swap starts when the frame enters from the viewport bottom and
  completes when the frame center reaches viewport center.
- The image holds briefly through a bounded sticky stage, then Program follows.
- Program heading and description remain static and visible.
- Reduced motion and JavaScript failure use the existing static image fallback.

## Architecture

- Keep one semantic Blade DOM.
- Keep CSS as the sole owner of all copy layout and visibility.
- Use one small production entry for image progress only.
- Use one passive scroll listener, one IntersectionObserver, and one RAF owner.
- Do not import the legacy Vision/Mission typography controller.
- The production image controller must not query `data-vision-copy` or
  `data-vision-typography`.

## Editable files

- `resources/js/pages/welcome-vision-story.js`
- `resources/css/pages/welcome-vision-waapi/enhanced.css`
- `resources/views/welcome.blade.php`
- `vite.config.js`
- architecture ledger and this blueprint

## Protected files and surfaces

- Vision/Mission Blade content and locale strings
- static three-layout copy owners in base/compact CSS
- Hero, Values, Programs source, Gallery, Articles, navigation, footer
- routes, controllers, database, authentication, dependencies

## Proof gates

- current `main` and fast-forward parent verification
- JavaScript syntax
- CSS brace balance
- source line limit
- Vite and Blade entry verification
- `git diff --check`
- `npm run check:structure`
- `npm run build`
- focused and full PHP tests
- Chromium and WebKit at all six tiers in ID, EN, and AR

Unavailable runtime gates remain `BLOCKED_BY_MISSING_EVIDENCE`.
