# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE
Updated: 2026-08-01
Repository: `Asyraf2003/schoolai`
Baseline audited source: `a580a509fecaaf912d718e56a38695212d41fcc3`
Published navbar implementation head before this ledger update:
`9efc9902b7b376b9a13c10cb37634913cf718f29`

Commit publication is source evidence only. It does not prove rendering,
browser parity, accessibility, performance, or lifecycle behavior.

## Active product goal

Build a distinctive Al Mustaqbal experience with cinematic motion and meaningful
spatial storytelling while retaining:

- semantic content and usable static/reduced fallbacks;
- fluid XS, SM, MD, LG, XL, and 2XL behavior from 360px upward;
- ID/EN in LTR and AR in RTL;
- Chromium plus real Safari/WebKit support;
- keyboard, touch, pointer, zoom, orientation, and short-height access;
- Lighthouse/PageSpeed `100/100/100/100` as a measured target;
- maintainable Blade, CSS, JS, media, and future WebGL ownership.

## B00 baseline result

Status: `COMPLETE_WITH_KNOWN_GAPS`

The B00 evidence remains the baseline for later comparisons:

- Laravel 13 + Blade + Vite 8;
- About intentionally disabled and protected;
- Testimonial source exists but is not rendered on the homepage;
- homepage CSS retains 47 ordered imports;
- no WebGL dependency, renderer, scene, or production lab existed;
- `npx vite build` passed during B00;
- the official structure command failed on pre-existing oversized Vision/Mission
  files and stale Hero equivalence records;
- the full PHP suite had one stale About test failure while 140 tests passed;
- runtime locale output proved ID/EN LTR and AR RTL;
- Brave/Chromium found a 390px Hero ornament collision and missing desktop
  navigation at 1181px;
- real Safari/WebKit and full performance/accessibility proof remain missing.

These baseline gaps are not resolved by the navbar roll implementation.

## Active production batch

Blueprint: `docs/architecture/blueprints/2026-08-01-navbar-mega-3d-roll.md`

Blueprint ID: `NAV-MEGA-ROLL-001`
Blueprint state: `IMPLEMENTING`
Active surface: unified public navigation
Owner decision: accepted through explicit implementation direction on
2026-08-01.

### Goal

Apply the Codrops-inspired 3D text roll to:

- desktop top-level navigation;
- desktop mega-menu links;
- hamburger top-level navigation;
- hamburger nested submenu links;
- navigation CTA labels;
- all six responsive tiers through the existing 1180/1181 navigation modes.

Media remains static. The reference image-follow behavior, cursor, fonts, page
composition, and runtime architecture are not copied.

### Source facts

- Server-rendered labels remain the no-JS fallback.
- `data-nav-roll="main"` marks top-level and CTA labels.
- `data-nav-roll="sub"` marks nested mega-menu labels.
- One navbar partial enhances labels into two visual text layers.
- CSS owns perspective, clipping, base/clone geometry, and reduced motion.
- The Web Animations API owns bounded roll timelines.
- No Three.js, GSAP, Splitting, WebGL context, RAF loop, or dependency was added.
- ID/EN use grapheme-level stagger.
- AR uses a whole-word roll to preserve Arabic joining.
- Pointer, focus, click/tap, hamburger entrance, and nested-menu opening are
  supported triggers.
- Existing navigation open/close, inert, focus, scroll-lock, locale, media, and
  route ownership remains unchanged.
- About and Testimonial were not touched.

### Files in the batch

- `resources/views/partials/site-navbar/header.blade.php`
- `resources/views/partials/site-navbar/mobile-navigation.blade.php`
- `resources/views/partials/site-navbar/styles/mega-roll.blade.php`
- `resources/views/partials/site-navbar/mega-roll-script.blade.php`
- `tests/Feature/PublicUnifiedNavigationTest.php`
- the active blueprint and this ledger.

## Open GAP

### `STRUCTURE-GAP-001`

The official structure gate has known baseline debt in Vision/Mission files and
Hero equivalence records. The current batch still requires the command to be run
and reported rather than silently waived.

### `TEST-GAP-001`

The full PHP suite previously had one stale test for the protected disabled About
surface. Focused navigation tests and the full suite must be rerun after pull.

### `RESPONSIVE-GAP-001`

The baseline 390px Hero collision and missing navigation at 1181px remain open.
The navbar roll must be tested at 1180 and 1181 without claiming those unrelated
baseline failures are fixed.

### `NAV-ROLL-PROOF-GAP-001`

No local build/test output or browser recording exists yet for the expanded
six-tier roll implementation.

Required proof includes:

- 360, 390, 640, 768, 1024, 1180, 1181, 1280, 1536, and 1920;
- ID, EN, and AR with LTR/RTL behavior;
- hamburger entrance, nested open, desktop hover, keyboard focus, and touch;
- reduced motion and repeated open/close;
- current Chromium and real Safari/WebKit.

### `BROWSER-PERF-GAP-001`

Real Safari, accessibility, Lighthouse/PageSpeed, long-task, transfer, zoom,
orientation, short-height, BFCache, and field CWV proof remain incomplete.

### `ENGINE-LAB-GAP-001`

WebGL remains a future accepted capability, but no engine ADR or isolated lab
exists. This DOM text roll does not justify selecting an engine.

## Accepted decisions

- Preserve one semantic DOM/content source.
- Preserve XS 360–639, SM 640–767, MD 768–1023, LG 1024–1279,
  XL 1280–1535, and 2XL 1536+.
- Preserve hamburger through 1180px and desktop navigation from 1181px.
- Preserve Inter/LTR for ID/EN and Cairo/RTL for AR.
- Touch behavior must not depend on hover.
- Arabic joining must not be broken into independently transformed letters.
- Reduced motion keeps labels static.
- Do not install Three.js for navigation text motion.
- About and Testimonial remain protected and out of scope.
- Commit success is not runtime proof.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G00 governance hardening | `PASS` | active architecture contracts |
| G01 execution foundation | `PASS` | migration, lab, and handoff rules |
| B00 current baseline | `COMPLETE_WITH_KNOWN_GAPS` | retained baseline evidence |
| N00 navbar media mapping | `PUBLISHED_NOT_RUNTIME_PROVEN` | local media paths committed |
| N01 navbar text-overlay removal | `PUBLISHED_NOT_RUNTIME_PROVEN` | overlay markup removed |
| N02 unified navbar 3D roll | `IMPLEMENTING` | source published; proof missing |
| E00 WebGL engine ADR | `BLOCKED_BY_MISSING_EVIDENCE` | no accepted WebGL scene |
| R00 PageSpeed/CWV acceptance | `BLOCKED_BY_MISSING_EVIDENCE` | lab and field evidence absent |

## STATUS

- Active surface: unified public navigation.
- Production source mutation: `PUBLISHED`.
- Runtime completion: `BLOCKED_BY_MISSING_EVIDENCE`.
- About/Testimonial protection: preserved by inspected diff scope.
- New graphics dependency: none.

## NEXT VALID STEP

Execution channel: `owner/local terminal`.

Pull current `main`, then run:

```bash
git diff --check
npm run check:structure
npm run build
php artisan test --filter=PublicUnifiedNavigationTest
php artisan test
```

After automated proof, capture the declared navigation matrix in Chromium and
real Safari/WebKit. Do not begin another production surface before recording the
result of `NAV-ROLL-PROOF-GAP-001`.
