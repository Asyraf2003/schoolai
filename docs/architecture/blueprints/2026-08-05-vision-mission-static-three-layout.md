# Vision/Mission Static Three-Layout Contract

BLUEPRINT ID: `HOME-VISION-008-STATIC`
STATUS: `OWNER_ACCEPTED`
OWNER: Asyraf Mubarak
DATE: 2026-08-05
SOURCE MAIN SHA: `a1287ed901f983d7b6cffda03db723b511bc1832`
ACTIVE ROUTE/SURFACE: homepage `#visi-misi`
TARGET BRANCH: `main`

## Goal

Make Vision/Mission permanently readable with no scroll-triggered motion while
retaining three responsive compositions and the current desktop copy hierarchy.

## Owner decisions

- The whole Vision/Mission surface is static at every width.
- No sticky journey, horizontal travel, typography entrance, lift, image swap,
  opacity gate, observer, RAF, or WAAPI runtime remains in production.
- Desktop `>=1181px` keeps Vision left, Mission right, and Mission slightly lower.
- Large tablet `1024-1180px` places Vision and Mission side by side.
- Narrow tablet and phone `<=1023px` stack Vision then Mission closely.
- Vision and Mission are centered in ID, EN, and AR.
- Image and Program follow with the existing bounded `2-3svh` rhythm.
- All three school images remain visible in the static composition.

## Scope

Editable:

- `resources/views/welcome.blade.php`
- `vite.config.js`
- `resources/css/pages/welcome-vision-waapi/base.css`
- `resources/css/pages/welcome-vision-waapi/enhanced.css`
- `resources/css/pages/welcome-vision-waapi/compact.css`
- `resources/css/pages/welcome-vision-waapi/responsive.css`
- this blueprint and `UI_UX_CURRENT_STATE.md`

Protected:

- Vision/Mission Blade semantics and locale copy;
- Hero, Values, Programs source, Gallery, Articles, navigation, footer, About,
  Testimonial, routes, controllers, DB, authentication, and dependencies.

## Architecture

CSS is the sole production owner. The Vision/Mission JavaScript entry is removed
from both the rendered Vite list and Vite input list. Existing motion modules may
remain as unreferenced source history, but they are not bundled or executed.

## Responsive contract

- XS `<640`: stacked compact copy, centered.
- SM `640-767`: stacked compact copy, centered.
- MD `768-1023`: stacked compact copy, centered.
- LG `1024-1180`: two centered columns, Mission slightly lower.
- LG `1181-1279`: desktop two-column hierarchy, Mission lower.
- XL and 2XL: same static desktop hierarchy with bounded copy and media widths.

## Proof plan

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=HomeVisionMissionHeadingTest
php artisan test
```

Runtime proof must cover ID/EN/AR at 390, 768, 1023, 1024, 1180, 1181, 1440,
and 1920 widths in Chromium and WebKit. The content must remain fully visible
with JavaScript disabled.
