# Navbar Mega Menu 3D Roll

BLUEPRINT ID: `NAV-MEGA-ROLL-001`
STATUS: `IMPLEMENTING`
OWNER: Asyraf
DATE: 2026-08-01
SOURCE MAIN SHA: `e5c71501dd741c2e1e35a2fbfb02888753ba5cec`
ACTIVE SURFACE: unified public desktop mega menu
TARGET EXECUTION CHANNEL: Web AI with explicit GitHub main authorization
REFERENCE: `https://github.com/codrops/3DLettersMenuHover/`

## Owner goal

Apply only the Codrops-inspired 3D letter roll to mega-menu link titles.
Preserve the existing SchoolAI media as static content. Do not copy the demo's
image-follow behavior, cursor, page composition, fonts, or global interaction.

## FACT

- Mega-menu labels are semantic links rendered by the shared site navbar.
- Desktop navigation starts at 1181px; mobile/tablet remains at or below 1180px.
- ID and EN use Latin/LTR; AR uses Arabic/RTL.
- Current package dependencies do not include GSAP, Splitting, or Three.js.
- The Codrops reference is MIT licensed and implements two text layers with
  per-character `rotateX` transitions.

## Decision

- Use project-owned DOM enhancement plus CSS transforms.
- Add no runtime dependency and no render loop.
- ID/EN segment by grapheme and stagger each character.
- AR rolls the complete word so Arabic joining remains intact.
- Hover and keyboard focus share the same result.
- Reduced motion and widths at or below 1180px show static text.
- Generated visual layers are hidden from assistive technology; one accessible
  text copy remains.

## Scope

Editable:

- `resources/views/partials/site-navbar.blade.php`
- `resources/views/partials/site-navbar/header.blade.php`
- `resources/views/partials/site-navbar/mobile-navigation.blade.php`
- `resources/views/partials/site-navbar/styles/mega-roll.blade.php`
- `resources/views/partials/site-navbar/mega-roll-script.blade.php`
- `tests/Feature/PublicUnifiedNavigationTest.php`

Protected and unchanged:

- media paths and image behavior;
- mega-menu open/close controller and focus lifecycle;
- routes, translations, About, Testimonial, Hero slider, DB, and Vite graph;
- navigation boundary of 1180/1181px.

## Storyboard

- Idle: original word is visible.
- Hover/focus: original layer rolls down and back; cloned layer rolls from above
  into the same position.
- Exit/blur: transition reverses to the original layer.
- Reduced motion/mobile/tablet: clone is hidden and the original word stays
  static.

## Proof gates

Automated gates required locally:

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=PublicUnifiedNavigationTest
php artisan test
```

Runtime proof must cover 1180/1181, ID/EN/AR, keyboard focus, pointer hover,
reduced motion, Chromium, and Safari/WebKit.

Commit publication is not runtime proof. Until local and browser gates run,
status remains `BLOCKED_BY_MISSING_EVIDENCE` for completion.

## Rollback

Remove the two navbar partial includes and `data-nav-roll` hooks. Original link
text remains the server-rendered fallback before enhancement.
