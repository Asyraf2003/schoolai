# HOME-HERO-STABILIZATION-001

Status: `COMPLETE_WITH_EXTERNAL_SAFARI_DEFERRED`
Owner: Homepage Hero
Target branch: `main`
Baseline commit: `93ed595707c23b4b9c6d87414af7615452539936`
Reconciled main commit: `0cdfebf87c8d632dbc636d8a86ea5a7436637d57`
Date: 2026-08-01
Progress: `98%`

## FACT

- Existing translation/controller data supports image, direct native video,
  poster, fallback image, localized copy, focal position, overlay, and CTA.
- Hero and navigation ownership had been coupled through language-flag rendering
  and anonymous cascade modules.
- The 390px collision came from mobile ornament/control positioning.
- The 1181px dead zone came from a stale `max-width: 1200px` navbar rule while
  the hamburger contract ended at 1180px.
- About and Testimonial are protected and not Hero dependencies.
- The final candidate preserves the latest concurrent navbar 3D-roll
  orchestration from `main`; Hero changes only own asset loading and the proven
  1180/1181 boundary required by this surface.

## GAP

- Real macOS Safari was unavailable: `DEFERRED_TO_REAL_MAC_SAFARI`.
- Lighthouse/PageSpeed/CWV are unmeasured.
- Three oversized Vision/Mission files and one stale disabled-About test remain
  outside this surface boundary.

## GOAL

Deliver one semantic Hero across mixed media, six responsive tiers, ID/EN/AR,
LTR/RTL, keyboard, reduced motion, failure fallback, no-JS, and the exact
1180/1181 navigation contract without redesigning unrelated sections.

## IMPACT

- Hero receives explicit source ownership and deterministic lifecycle behavior.
- Navigation remains visually consistent while its breakpoint gap is corrected.
- Existing Controller/Blade/translation data flow is preserved.
- No database schema or dependency is added.

## DECISION

- Use CSS transforms, opacity, bounded transition-only blur, and native JS.
- Keep poster/fallback visible until video reaches `playing`.
- Treat autoplay as optional enhancement; reduced motion disables it.
- Render semantic controls in Blade and reveal them only after enhancement.
- Separate navigation CSS/JS from Hero and language-flag ownership.
- Protect migrated Hero and affected `welcome.css` order/checksums through the
  effective equivalence ledger.

## SOURCE OWNERSHIP

Hero:

- `BuildsHomeHero.php`: normalized media contract;
- `home/sections/hero.blade.php`: semantic DOM and static fallback;
- `welcome-hero.css`: Hero entry only;
- `surfaces/home/hero/{layout,media,motion,controls,responsive,locale,reduced-motion}.css`;
- `welcome-hero.js`: entry only;
- `surfaces/home/hero/{controller,media,events}.js`.

Navigation boundary:

- `welcome-navigation.css` and its semantic modules;
- `welcome/029-*.css` for the legacy shared breakpoint correction;
- `welcome/navigation-mega.js` for mega-menu behavior;
- `site-navbar.blade.php` for navigation asset loading.

Every new Hero source file is at or below 200 lines.

## DATA AND MEDIA CONTRACT

Each normalized slide exposes declared/render type, sanitized media URL, poster,
local fallback, alt text, focal position, overlay strength, video MIME type,
localized copy, and CTA.

Unsupported or failed video degrades to image without schema changes. Legacy
YouTube/iframe media remains rejected.

## TRANSITION STATE MACHINE

1. `static`: Blade first slide visible before JS.
2. `active`: exactly one slide is interactive.
3. `transitioning`: old `is-leaving`, new `is-entering`, bounded to 980ms.
4. `suspended`: timers/media paused when hidden, offscreen, or navigating.
5. `media-error`: poster/local image/gradient fallback remains visible.
6. `disposed`: listeners, timer, observer, transition, and media state cleaned.

Rapid input clears prior transient classes. Automatic changes never move focus.

## RESPONSIVE CONTRACT

- XS 360–639;
- SM 640–767;
- MD 768–1023;
- LG 1024–1279;
- XL 1280–1535;
- 2XL 1536+.

Implementation uses fluid values and logical properties. Chromium certified 360,
390, 640, 768, 1024, 1180, 1181, 1279, 1280, 1536, and 1920. Hamburger remains
active through 1180; desktop navigation starts at 1181.

## LOCALE AND DIRECTION

- ID/EN: Inter, LTR;
- AR: Cairo, RTL;
- one DOM/content flow;
- mirrored spatial controls and reading-direction keyboard/swipe behavior;
- bounded Arabic title/description line-height and wrapping.

## ACCESSIBILITY AND FALLBACK

- Native buttons, localized labels, visible focus, no focus trap;
- autoplay pause/play without focus movement;
- reduced motion disables autoplay/choreography, not content;
- inactive slides use `aria-hidden` and `inert`;
- no-JS first slide remains meaningful;
- media failure falls through poster → local image → stable gradient;
- explicit media dimensions and stable geometry reduce CLS risk.

## LIFECYCLE CLEANUP

Only active video sources hydrate. Inactive video pauses/resets. Visibility,
intersection, locale submit, pagehide, BFCache, resize, and disposal are handled.
Listeners use `AbortController`; observers/timers are cleaned.

## PERFORMANCE BUDGET AND RESULT

- No Three.js, Babylon.js, GSAP, WebGL, heavy animation package, or global loop.
- Blur is transition-only and capped at 14px.
- First visible media is eager/high-priority; inactive media remains lazy.
- Hero CSS: 22.65 kB → 12.136 kB raw; 4.99 kB → 2.980 kB gzip.
- Hero JS: 9.29 kB → 8.027 kB raw; 2.93 kB → 2.667 kB gzip.
- Navigation CSS is separately owned at 13.915 kB raw / 3.275 kB gzip.
- No Lighthouse/PageSpeed result is inferred from build success.

## PROOF

Automated/runtime result:

- diff hygiene and clean proof checkout: PASS;
- `npx vite build`: PASS;
- focused Hero/navigation: 7 passed, 163 assertions;
- Chromium: 33 locale-width cases PASS;
- keyboard, pointer, repeated transitions, visibility, BFCache, media failure,
  reduced motion, and no-JS: PASS;
- full Laravel: 144 passed, one stale About test failed, 1375 assertions;
- structure: only three known Vision/Mission line-limit failures;
- Hero and affected `welcome.css` equivalence: PASS.

Safari status: `DEFERRED_TO_REAL_MAC_SAFARI`.

Exact owner proof checklist:

1. Test all certified widths on real Safari and resize across each boundary.
2. Switch ID/EN/AR through POST/session/redirect and verify language/direction.
3. Verify muted inline video, poster, autoplay policy, inactive/hidden pause.
4. Force video/image failures and verify the fallback chain.
5. Repeat arrows/dots/keyboard/pointer/touch; verify settled single-active state.
6. Enable Reduce Motion; verify no autoplay/choreography and complete content.
7. Disable JS; verify the first semantic slide and fallback media.
8. Navigate away/back; verify BFCache without duplicate listeners/timers.
9. Check overflow, copy/media/control collisions, visible focus, and visual CLS.
10. Inspect console/network/media for errors and repeated inactive downloads.

## ROLLBACK BOUNDARY

Revert the single squash commit. No schema migration, package change, or content
migration is required. Existing locale/media data remains compatible.

## OUT OF SCOPE

Vision/Mission, School Values, Featured Programs, Gallery, Articles, Footer,
About, Testimonial, admin, editor, global typography redesign, schema, WebGL,
3D experiments, and unrelated navbar redesign.

## EXECUTION ORDER

- H00 preflight and ownership map: PASS
- H01 media contract: PASS
- H02 transition/lifecycle: PASS
- H03 responsive fluid six-tier system: PASS
- H04 ID/EN/AR and LTR/RTL: PASS_CHROMIUM
- H05 navigation 1180/1181: PASS
- H06 accessibility/reduced-motion/failure fallback: PASS
- H07 automated/runtime proof: PASS_CHROMIUM
- H08 docs/equivalence/atomic main delivery: PASS_ON_SQUASH

## STATUS AND NEXT VALID STEP

Source work is complete. Real Safari remains the only browser acceptance deferred.
Run the owner Safari checklist, record the evidence, then decide the next homepage
surface from fresh proof. Do not automatically start another section.
