# Vision/Mission Fast Entrance Typography

BLUEPRINT ID: `HOME-VISION-004-ENTRANCE`
STATUS: `IMPLEMENTING`
OWNER: Asyraf Mubarak
DATE: 2026-08-05
SOURCE MAIN SHA: `3387885eaf0d98d5accc8a7cc0f2f0afce0ffd2e`
ACTIVE ROUTE/SURFACE: homepage `#visi-misi`
TARGET EXECUTION CHANNEL: Web AI GitHub direct `main`

## Goal

Keep the current Vision/Mission paper story and desktop composition, while
replacing the typography scrub with a short entrance effect. The effect gives a
brief motion cue without making visitors wait for the Vision or Mission copy.

## Owner decisions

- Vision translates Codrops On-Scroll Typography Animations Set 2 model 12
  (`effect27`) into deterministic word-level WAAPI motion.
- Mission translates Set 2 model 10 (`effect25`) into baseline `scaleY` reveal.
- The entrance starts only when scrolling down from above and the section top
  crosses into the viewport.
- Scrolling upward from below never replays the entrance; copy is immediately
  shown in its final state.
- At roughly one-quarter section entry, the short entrance should already be
  complete or nearly complete.
- Motion values stay deterministic and bounded so words remain structured.
- The existing six layout tiers remain authoritative. The `1180/1181` boundary
  is a behavior boundary inside LG, not a seventh tier.
- In the vertical journey through `1180px`, Vision is centered and Mission uses
  logical start: left for ID/EN and right for AR.
- Desktop from `1181px` retains its current composition and direction behavior.
- No GSAP, Lenis, Splitting, Three.js, or new dependency is added.

## Scope

Editable:

- `resources/js/surfaces/home/vision-story/controller.js`
- `resources/js/surfaces/home/vision-story/timeline.js`
- `resources/js/surfaces/home/vision-story/typography.js`
- `resources/css/pages/welcome-vision-waapi/base.css`
- `resources/css/pages/welcome-vision-waapi/compact.css`
- `resources/css/pages/welcome-vision-waapi/responsive.css`
- this blueprint and `UI_UX_CURRENT_STATE.md`

Protected:

- Blade content and locale files unless a proven hook gap appears;
- Hero, Values, Programs, Gallery, Articles, navigation, footer, About, and
  Testimonial;
- routes, controllers, database, authentication, media assets, package graph,
  and the desktop paper-story composition.

## Architecture

The existing lazy loader remains the entry owner. One Vision controller keeps
ownership of observer, scroll direction, resize, RAF smoothing, and cleanup.
The existing track and image timeline remains scroll-linked. Typography owns a
separate paused WAAPI entrance with `reset`, `play`, and `finish` states; it is
not scrubbed by page progress.

Vision uses complete word wrappers in ID, EN, and AR. Mission uses grapheme
units grouped inside stable word wrappers for ID/EN and complete words for AR.
Arabic letters are never split, preserving shaping and joining.

## Six-tier contract

- XS `<640`: vertical, Vision center, Mission logical start, smallest bounded
  transform distance.
- SM `640-767`: same interaction with fluid existing type scale.
- MD `768-1023`: vertical, same logical alignment and short entrance.
- LG `1024-1180`: vertical/hamburger journey; same alignment.
- LG `1181-1279`: current horizontal desktop composition retained.
- XL `1280-1535`: current desktop composition retained.
- 2XL `>=1536`: current bounded copy composition; motion values do not expand
  with viewport width.

## Fallback and performance

No-JS, reduced motion, unsupported WAAPI, late preparation, and failure show the
complete static text. Images retain native lazy loading and reserved dimensions.
The controller remains dynamically imported after Hero presentation. Runtime
cost must be measured after deployment; if the effect causes attributable,
significant regression on target mobile or WebKit profiles, the typography
enhancement is removed and the static result remains.

## Proof gates

Required source proof:

```bash
git diff --check
git status --short
npm run check:structure
npm run build
php artisan test --filter=HomeVisionMissionHeadingTest
php artisan test
```

Required runtime proof covers 360, 390, 640, 768, 1024, 1180, 1181, 1280,
1440, 1536, and 1920 in ID/EN/AR, Chromium/WebKit, downward and upward entry,
re-entry, fast scroll, resize/orientation, reduced motion, and 200% zoom.
Performance comparison uses at least three comparable deployed runs and reports
median plus worst, transfer/chunk delta, long tasks, rendering cost, LCP, CLS,
and interaction proxy.
