# Unified Navbar 3D Roll

BLUEPRINT ID: `NAV-MEGA-ROLL-001`
STATUS: `IMPLEMENTING`
OWNER: Asyraf
DATE: 2026-08-01
SOURCE MAIN SHA: `aca6145443e0aee820ad36b0580aeee528d7a9b7`
ACTIVE SURFACE: unified public navigation
TARGET EXECUTION CHANNEL: Web AI with explicit GitHub main authorization
REFERENCE: `https://github.com/codrops/3DLettersMenuHover/`

## Owner goal

Apply the Codrops-inspired 3D text roll across all six responsive tiers:

- desktop main-navigation labels;
- desktop mega-menu labels;
- hamburger main-navigation labels;
- hamburger nested submenu labels;
- navigation CTA labels.

Preserve SchoolAI media as static content. Do not copy the reference image
follow, cursor, fonts, page composition, or global architecture.

## FACT

- Desktop navigation is active from 1181px.
- Hamburger navigation is active through 1180px and already exposes an `active`
  layer class plus `.nav-mega.is-open` nested state.
- ID/EN use Latin/LTR and AR uses Arabic/RTL.
- The Codrops reference uses duplicate text layers and `rotateX` character
  transitions.
- SchoolAI has no GSAP, Splitting, or Three.js dependency.

## Decision

- Use one semantic server-rendered label and project-owned DOM enhancement.
- Use the Web Animations API with CSS 3D geometry; add no dependency or RAF.
- ID/EN segment by grapheme and stagger each character.
- AR rolls a complete word so Arabic joining remains intact.
- Pointer enter/exit, focus/blur, click/tap, hamburger entrance, and nested-menu
  opening can trigger the roll.
- Reduced motion keeps every label static.
- Generated visual layers are `aria-hidden`; one visually hidden accessible copy
  preserves the label.

## Scope

Editable:

- `resources/views/partials/site-navbar.blade.php`
- `resources/views/partials/site-navbar/header.blade.php`
- `resources/views/partials/site-navbar/mobile-navigation.blade.php`
- `resources/views/partials/site-navbar/styles/mega-roll.blade.php`
- `resources/views/partials/site-navbar/mega-roll-script.blade.php`
- `tests/Feature/PublicUnifiedNavigationTest.php`
- this blueprint and current state when required.

Protected and unchanged:

- media paths, crop, and image behavior;
- navigation open/close, focus, scroll-lock, and locale controllers;
- routes, translations, About, Testimonial, Hero, DB, and Vite graph;
- the 1180/1181 navigation boundary.

## Storyboard

- Idle: the original word remains visible.
- Pointer/focus enter: original letters roll away on the X axis while cloned
  letters enter from the opposite vertical side.
- Pointer/focus exit: the roll direction reverses.
- Tap/click: a forward roll runs without delaying the action.
- Hamburger open: top-level labels and CTA roll in sequence after their existing
  cinematic entrance begins.
- Nested menu open: the trigger and newly exposed submenu labels roll in sequence.
- Reduced motion or unsupported Web Animations API: static semantic labels.

## Six-tier contract

| Tier | Navigation mode | Roll trigger |
|---|---|---|
| XS 360-639 | hamburger | entrance, tap, focus, nested open |
| SM 640-767 | hamburger | entrance, tap, focus, nested open |
| MD 768-1023 | hamburger | entrance, tap, focus, nested open |
| LG 1024-1180 | hamburger | entrance, tap, focus, nested open |
| LG 1181-1279 | desktop | pointer, keyboard, click, mega open |
| XL 1280-1535 | desktop | pointer, keyboard, click, mega open |
| 2XL 1536+ | desktop | pointer, keyboard, click, mega open |

The global six tiers remain unchanged; 1180/1181 is the existing navigation
sub-boundary inside LG.

## Locale and accessibility

- ID/EN: per-grapheme stagger.
- AR: whole-word neutral X-axis roll; no automatic reversal of time.
- Keyboard focus receives the same motion as pointer hover.
- Touch does not depend on hover.
- Screen readers receive one label only.
- No-JS and reduced-motion results remain fully usable.

## Proof gates

```bash
git diff --check
git status --short
npm run check:structure
npm run build
php artisan test --filter=PublicUnifiedNavigationTest
php artisan test
```

Runtime proof must cover 360, 390, 640, 768, 1024, 1180, 1181, 1280, 1536,
1920; ID/EN/AR; LTR/RTL; pointer, touch, keyboard; reduced motion; repeated
open/close; Chromium and Safari/WebKit.

Commit publication is not runtime proof. Completion remains
`BLOCKED_BY_MISSING_EVIDENCE` until local and browser gates run.

## Rollback

Remove the navbar partial includes and all `data-nav-roll` hooks. The original
server-rendered labels remain the fallback before enhancement.
