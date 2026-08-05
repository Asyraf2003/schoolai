# Vision/Mission Mature Motion with Static Copy

BLUEPRINT ID: `HOME-VISION-011-MATURE-MOTION-STATIC-COPY`
STATUS: `OWNER_ACCEPTED`
OWNER: Asyraf Mubarak
DATE: 2026-08-05
MATURE BASELINE: `a1287ed901f983d7b6cffda03db723b511bc1832`
ACTIVE SURFACE: homepage `#visi-misi`
TARGET BRANCH: `main`

## Goal

Restore the last mature Vision/Mission composition and motion system, while
removing only the Vision and Mission text entrance animation.

## Owner decisions

- Desktop `>=1181px` keeps the horizontal scroll journey.
- Desktop keeps Vision and Mission in their mature positions.
- Large tablet `1024-1180px` keeps the accepted two-column compact layout.
- Narrow tablet and phone `<=1023px` keep the close stacked layout.
- Image 2-3 swap and Program journey remain animated.
- Vision and Mission copy is visible immediately at every width.
- Copy must not be split, faded, lifted, reset, or progress-driven.
- ID, EN, and AR keep the same semantic DOM and centered compact alignment.

## Architecture

The mature Blade and CSS owners are restored from `a1287ed9`.
The existing story controller still owns track travel and image-stack progress.
Typography preparation, typography entry, compact copy lift, and copy progress
are removed from the active controller and timeline.

The unused typography modules may remain in source, but production no longer
imports them through the active Vision/Mission path.

## Protected scope

- locale copy and semantic content;
- Hero, Values, Gallery, Articles, navigation, footer, routes, DB, auth;
- global responsive tiers and the `1181px` horizontal boundary;
- image assets and Program content.

## Proof plan

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeVisionMissionHeadingTest
php artisan test
```

Runtime proof must cover ID, EN, and AR at 390, 768, 1023, 1024, 1180, 1181,
1440, and 1920 widths. Copy must be visible before controller preparation, while
the image and track journey must retain the mature behavior.
