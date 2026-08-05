# Vision/Mission Compact Rhythm and Scroll Lift

BLUEPRINT ID: `HOME-VISION-007-COMPACT-RHYTHM`
STATUS: `OWNER_ACCEPTED / IMPLEMENTING`
OWNER: Asyraf Mubarak
DATE: 2026-08-05
SOURCE MAIN SHA: `bc6e3d1819edfc69e3f4aefa20a3d8409d86883e`
ACTIVE ROUTE/SURFACE: homepage `#visi-misi`
TARGET EXECUTION CHANNEL: Web AI GitHub direct `main`

## Goal

Replace the full-viewport compact Vision and Mission stages with one close,
intrinsic vertical story while preserving a visible scroll-driven lift. Keep the
horizontal composition from `1181px` unchanged.

## Owner-accepted result

For every vertical layout through `1180px`:

```text
header clearance + 2-3svh
Vision, centered
2-3svh
Mission, centered
2-3svh
main image and image 2-3 frame
2-3svh
Program title
```

- Vision and Mission are centered in ID, EN, and AR.
- Final layout gaps use a bounded value around `2.5svh`.
- Vision, Mission, and the Program title begin about `10svh` lower and settle to
  their normal position as their own title moves from the viewport bottom to the
  viewport center.
- Translation is implemented with transform, never animated margin or layout.
- Upward entry remains final/static.
- Image 2-3 continues to rise from below and completes when its frame reaches
  viewport center.
- Reduced motion and failed enhancement retain the same close semantic layout.

## Scope

Editable:

- compact/responsive Vision CSS owners;
- Vision story timeline, typography, entry, and one geometry helper;
- this blueprint and `UI_UX_CURRENT_STATE.md`.

Protected:

- desktop horizontal layout from `1181px`;
- Blade, locale copy, media assets, Hero, Values, Programs source, Gallery,
  Articles, navigation, footer, routes, DB, authentication, and dependencies.

## Architecture

Compact geometry is CSS-first and intrinsic. The controller remains the only
scroll/RAF owner. Timeline derives local ranges for Vision, Mission, image frame,
and Program title. Child lift animations and typography use the already-smoothed
master progress. Wide typography retains its short play-on-entry behavior.

## Proof plan

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeVisionMissionHeadingTest
php artisan test
```

Runtime review covers 360, 390, 640, 768, 1024, and 1180 widths; short/tall
heights; ID/EN/AR; down/up/re-entry; reduced motion; Chromium and WebKit.
