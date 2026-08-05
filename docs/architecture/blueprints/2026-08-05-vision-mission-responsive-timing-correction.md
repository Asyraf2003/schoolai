# Vision/Mission Responsive Timing Correction

BLUEPRINT ID: `HOME-VISION-005-RESPONSIVE`
STATUS: `IMPLEMENTING`
OWNER: Asyraf Mubarak
DATE: 2026-08-05
SOURCE MAIN SHA: `a274b07d80f681f3acb0db6a41a91f35c39d44f7`
ACTIVE ROUTE/SURFACE: homepage `#visi-misi`
TARGET EXECUTION CHANNEL: Web AI GitHub direct `main`

## Goal

Correct narrow-tablet clipping, compact timing, Arabic Vision centering, image
swap geometry, and late enhancement reflow without changing other homepage
surfaces or the six-tier responsive contract.

## Owner decisions

- Animation completion is measured from the Vision or Mission kicker/title, not
  from the center of the entire text panel.
- Horizontal mode retains a short play-on-entry entrance.
- Vertical mode maps each typography effect from title entry at the viewport
  bottom to completion when that title reaches viewport center.
- Upward entry and late preparation show final static text.
- Vision/Mission sizing uses a hybrid approach: height-aware font scaling plus
  vertical repositioning of the existing copy wrapper. No extra wrapper is added.
- The existing copy wrapper becomes the centered horizontal stage.
- Mission remains lower than Vision, but its offset is bounded rather than fixed
  at `15svh`.
- Frame image swap remains center-based after typography and font geometry are
  prepared before measurement.
- Vision is centered through `1180px` for ID, EN, and AR; Mission stays logical
  start.
- Hero remains the critical path. Vision code/media warm in the background after
  Hero. If preparation finishes too late, the static layout remains for that
  visit instead of switching layout in view.
- Gallery and Values failures are recorded but remain outside this batch.

## Scope

Editable:

- `resources/css/pages/welcome-vision-waapi/base.css`
- `resources/css/pages/welcome-vision-waapi/enhanced.css`
- `resources/css/pages/welcome-vision-waapi/compact.css`
- `resources/css/pages/welcome-vision-waapi/responsive.css`
- `resources/js/surfaces/home/vision-story/controller.js`
- `resources/js/surfaces/home/vision-story/entry.js`
- `resources/js/surfaces/home/vision-story/preparation.js`
- `resources/js/surfaces/home/vision-story/timeline.js`
- `resources/js/surfaces/home/vision-story/typography.js`
- this blueprint and `UI_UX_CURRENT_STATE.md`

Protected:

- Blade and locale content;
- Hero, Values, Programs, Gallery, Articles, navigation, footer, About, and
  Testimonial;
- routes, controllers, database, authentication, media assets, dependencies, and
  six global breakpoint tiers.

## Architecture

The controller remains the single runtime owner. `entry.js` owns title-relative
entry state, while `preparation.js` owns bounded image/font warm-up and the
no-visible-mutation gate. Timeline continues to own track, Mission panel, frame,
and image-stack geometry. Typography remains native WAAPI.

Typography wrappers are created before font settlement and geometry measurement.
The enhanced class is applied only while the section remains below the visible
viewport. Two animation frames then allow enhanced CSS geometry to settle before
measurement and timeline creation.

## Six-tier contract

- XS `<640`: vertical, height-aware compact type, Vision center, Mission start.
- SM `640-767`: same behavior with existing media width tier.
- MD `768-1023`: vertical; each title controls its own short reveal range.
- LG `1024-1180`: vertical/hamburger behavior remains.
- LG `1181-1279`: horizontal wrapper centered; Mission type/offset are bounded.
- XL `1280-1535`: horizontal composition retained with intrinsic stage centering.
- 2XL `>=1536`: existing width bounds remain; motion values stay bounded.

## Proof plan

Required after publication:

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeVisionMissionHeadingTest
php artisan test
```

Runtime proof must cover 360, 390, 640, 768, 1024, 1180, 1181, 1280, 1440,
1536, and 1920 widths, including short-height landscape cases; ID/EN/AR;
downward/upward entry; resize; reduced motion; Chromium; WebKit; and three
comparable deployed performance runs.

Known pre-existing/local test report before this correction:

- Gallery soft-delete homepage assertion failed;
- Values test used a page-wide `aria-pressed` absence assertion;
- Vision/Mission English copy assertion failed;
- 191 tests passed and 3 failed.

This batch does not change those PHP assertions or their non-motion owners.
