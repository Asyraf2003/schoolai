# HOME-HERO-STABILIZATION-001

Status: OWNER_ACCEPTED
Owner: Homepage Hero
Target branch: `main`
Baseline commit: `93ed595707c23b4b9c6d87414af7615452539936`
Date: 2026-08-01

## FACT

- Hero data already normalizes `image`, direct native `video`, poster, locale copy,
  focal position, overlay strength, and CTA destination in `BuildsHomeHero`.
- The first parity slide is a direct MP4; image slides and a page-level fallback
  image already exist for ID, EN, and AR.
- Hero markup, CSS, JS, and navbar ownership were coupled through
  `partials/language-flag.blade.php`.
- The previous Blade rendered only arrows while CSS/JS also expected dots,
  progress, counter, and playback controls.
- The 390px collision came from mobile arrow positions in an inline Hero style.
- The 1181px dead zone came from a stale `max-width: 1200px` navbar rule while
  the hamburger contract ended at 1180px.
- About and Testimonial remain protected and are not dependencies of Hero.

## GAP

- Real Mac Safari proof is unavailable in the execution environment.
- Full structure and full Laravel gates contain pre-existing out-of-scope debt:
  Vision/Mission line limits and one stale disabled-About test.
- PageSpeed/Lighthouse and field CWV remain unmeasured.

## GOAL

Deliver one stable semantic Hero across mixed media, six responsive tiers,
ID/EN/AR, LTR/RTL, keyboard, reduced motion, failure fallback, no-JS, and the
1180/1181 navigation boundary without redesigning unrelated homepage sections.

## IMPACT

- Hero gets explicit source ownership and deterministic lifecycle behavior.
- Navigation CSS remains visually unchanged except for the proven boundary.
- Existing Controller/Blade/translation data flow is preserved.
- No database schema or dependency is added.

## DECISION

- Use CSS transforms, opacity, bounded blur, and small native JavaScript.
- Keep poster/fallback visible until video reaches `playing`.
- Treat autoplay as optional enhancement; reduced motion disables it.
- Render controls semantically in Blade and reveal them only after JS enhances.
- Move navigation CSS/JS ownership out of Hero entry points.
- Use an effective equivalence override record so all existing ledger entries
  remain enforced while the intentionally migrated Hero record is replaced.

## SOURCE OWNERSHIP

### Hero

- `app/Http/Controllers/Concerns/BuildsHomeHero.php`: normalized media contract.
- `resources/views/home/sections/hero.blade.php`: semantic DOM and static fallback.
- `resources/css/pages/welcome-hero.css`: Hero CSS entry only.
- `resources/css/surfaces/home/hero/layout.css`: geometry and content hierarchy.
- `resources/css/surfaces/home/hero/media.css`: media, poster, and error surface.
- `resources/css/surfaces/home/hero/motion.css`: transition choreography.
- `resources/css/surfaces/home/hero/controls.css`: indicator and transport controls.
- `resources/css/surfaces/home/hero/responsive.css`: fluid tier behavior.
- `resources/css/surfaces/home/hero/locale.css`: RTL and Arabic typography.
- `resources/css/surfaces/home/hero/reduced-motion.css`: motion fallback.
- `resources/js/pages/welcome-hero.js`: entry only.
- `resources/js/surfaces/home/hero/controller.js`: state and autoplay policy.
- `resources/js/surfaces/home/hero/media.js`: hydration/playback/failure state.
- `resources/js/surfaces/home/hero/events.js`: input and lifecycle listeners.

### Navigation boundary

- `resources/css/pages/welcome-navigation.css` and its modules own shared navbar.
- `resources/css/pages/welcome/029-*.css` owns the stale legacy breakpoint fix.
- `resources/js/pages/welcome/navigation-mega.js` owns mega-menu behavior.
- `resources/views/partials/site-navbar.blade.php` loads navigation entries.

## DATA AND MEDIA CONTRACT

Each normalized slide exposes:

- `type`: declared `image` or `video`;
- `render_type`: safe rendered type;
- `media_url`: sanitized image/direct video URL;
- `poster_url`: video poster or page fallback;
- `fallback_url`: final local fallback image;
- `media_alt`, `focal_position`, `overlay_strength`, `video_mime_type`;
- localized `eyebrow`, `title`, `description`, and CTA.

Invalid/unsupported video degrades to image without schema changes. YouTube and
old iframe media remain rejected.

## TRANSITION STATE MACHINE

States are represented by one active index plus transient classes:

1. `static`: Blade first slide visible before JS.
2. `active`: exactly one `.is-active` slide is interactive.
3. `transitioning`: old `.is-leaving`, new `.is-entering` for 980ms.
4. `suspended`: timers and media paused when hidden/offscreen/navigating.
5. `media-error`: poster/local fallback remains visible.
6. `disposed`: listeners, timers, observer, and media are cleaned.

Rapid input clears the prior transition before starting the next. Focus is never
moved by automatic changes.

## RESPONSIVE CONTRACT

- XS 360–639
- SM 640–767
- MD 768–1023
- LG 1024–1279
- XL 1280–1535
- 2XL 1536+

The implementation uses `clamp()`, logical properties, viewport units, flexible
copy widths, and one responsive control rail. Certified boundaries are 360,
390, 639/640, 767/768, 1023/1024, 1180/1181, 1279/1280, 1535/1536, and 1920.
Hamburger is active through 1180; desktop navigation starts at 1181.

## LOCALE AND DIRECTION

- ID and EN: Inter, LTR.
- AR: Cairo, RTL.
- One semantic DOM and one content flow.
- Logical alignment and mirrored spatial controls are used.
- Arrow keys and swipe direction follow reading direction.
- Arabic title/description line-height and wrapping are explicitly bounded.

## ACCESSIBILITY AND FALLBACK

- First slide content and poster/image remain available without JavaScript.
- Controls are native buttons with visible focus and localized labels.
- Autoplay has a pause/play control and does not move focus.
- Reduced motion disables autoplay and transition animation.
- Inactive slides are `aria-hidden` and `inert`.
- Image/video failure falls back to poster, then local image, then a stable
  gradient surface.
- Explicit media dimensions and fixed Hero geometry prevent media CLS.

## LIFECYCLE CLEANUP

- Only active video sources are hydrated.
- Inactive videos pause and reset.
- Visibility, Hero intersection, locale submit, pagehide, and BFCache are handled.
- Event listeners use `AbortController`; observers and timers are disposed.
- BFCache pages suspend and resume rather than permanently losing controls.

## PERFORMANCE BUDGET

- No Three.js, Babylon.js, GSAP, WebGL, or animation dependency.
- No global animation loop.
- Blur is transition-only and bounded to 14px.
- First visible media is eager/high priority; inactive media stays lazy.
- Bundle raw/gzip delta must be recorded from the Vite manifest.
- Lighthouse/PageSpeed is never inferred from build success.

## PROOF MATRIX

Automated:

- `git diff --check` and clean final status;
- focused Hero/navigation feature tests;
- `npx vite build`;
- effective source-equivalence validation;
- bundle manifest raw/gzip report;
- full Laravel suite with stale About failure classified separately;
- structure output with Vision/Mission debt classified separately.

Runtime Chromium:

- all certified widths for ID/LTR, EN/LTR, AR/RTL;
- overflow, copy/control collision, media crop, wrapping, focus, nav mode;
- repeated transitions, keyboard, pointer, swipe-safe handling;
- reduced motion, video/image failure, JS-disabled fallback, BFCache.

Safari:

- status remains `DEFERRED_TO_REAL_MAC_SAFARI` until the owner runs the exact
  checklist in the final handoff on real Safari.

## ROLLBACK BOUNDARY

Revert the single squash commit. No schema migration, package change, or content
migration is required. Existing translations and media data remain compatible.

## OUT OF SCOPE

Vision/Mission, School Values, Featured Programs, Gallery, Articles, Footer,
About, Testimonial, admin, editor, typography redesign, database schema, WebGL,
3D experiments, and unrelated navbar redesign.

## EXECUTION ORDER

- H00 preflight and ownership map: COMPLETE
- H01 media contract: IMPLEMENTED_PENDING_PROOF
- H02 transition/lifecycle: IMPLEMENTED_PENDING_PROOF
- H03 responsive system: IMPLEMENTED_PENDING_PROOF
- H04 locale/RTL: IMPLEMENTED_PENDING_PROOF
- H05 navigation boundary: IMPLEMENTED_PENDING_PROOF
- H06 accessibility/fallback: IMPLEMENTED_PENDING_PROOF
- H07 automated/runtime proof: PENDING
- H08 docs/atomic merge: PENDING
