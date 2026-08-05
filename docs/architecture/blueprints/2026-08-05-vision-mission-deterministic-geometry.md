# Vision/Mission Deterministic Geometry

BLUEPRINT ID: `HOME-VISION-006-DETERMINISTIC`
STATUS: `IMPLEMENTING`
OWNER: Asyraf Mubarak
DATE: 2026-08-05
SOURCE MAIN SHA: `c9bc2fc22780b950b548f1f0ac491f96d254eb61`
ACTIVE SURFACE: homepage `#visi-misi`
TARGET CHANNEL: Web AI direct GitHub `main`

## Goal

Make the Vision/Mission layout and animation deterministic across reloads,
responsive widths, locales, and normal scrolling.

## Accepted decisions

- Static and enhanced states share the same Vision, Mission, and image stages.
- A capability marker gates sticky motion.
- Enhancement initializes atomically after bounded font and image preparation.
- Compact Mission uses effect25 only, without a second parent fade or slide.
- Compact Mission starts when its title enters from the viewport bottom and
  completes when the title center reaches viewport center.
- Image-stack timing uses the same local entry-to-center geometry.
- Upward compact entry keeps Mission final.
- Vision container and text are centered in ID, EN, and AR.
- Homepage translation data is read per request; the locale-unsafe cache is
  removed.
- Gallery and Values failures remain outside this batch.

## Architecture

Base CSS owns stable semantic stage geometry. The capability class and
`.is-enhanced` add pinning and track motion only. Timeline geometry is measured
relative to the track. One rendered master progress owns track movement,
Mission typography progress, and image-stack swap.

## Scope

Editable:

- `app/Http/Controllers/HomeController.php`
- `app/Http/Controllers/Concerns/BuildsHomePage.php`
- Vision/Mission CSS and JavaScript owners
- this blueprint and `UI_UX_CURRENT_STATE.md`

Protected:

- Vision/Mission Blade and locale copy
- Hero, Values, Programs, Gallery, Articles, navigation, and footer
- media assets, routes, database, authentication, and dependencies

## Proof plan

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeVisionMissionHeadingTest
php artisan test
```

Runtime proof covers all six tiers, ID/EN/AR, both directions, reload states,
resize, reduced motion, Chromium, and WebKit.
