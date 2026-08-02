# Homepage Atmospheric Depth Gallery

BLUEPRINT ID: `HOME-GALLERY-003`
STATUS: `IMPLEMENTING`
OWNER: Asyraf
DATE: 2026-08-03
SOURCE MAIN SHA: `6c572ab40fd15362830646844abf7a71d5efa7fa`
ACTIVE ROUTE/SURFACE: homepage Gallery only
TARGET EXECUTION CHANNEL: Web AI with GitHub connector
REFERENCE: `houmahani/codrops-depth-gallery`

## Owner goal

Replace the homepage Gallery presentation with the reference's atmospheric
scroll-through-depth experience. Preserve current database gallery content,
photo/video opening behavior, homepage section order, three locales, and one
shared UI architecture.

The reference contributes the depth journey, alternating media placement,
scroll-driven camera feeling, velocity-reactive atmosphere, and palette changes.
Its assets, flower identity, labels, source modules, shaders, and branding are
not copied.

## FACT

- Homepage receives at most six normalized gallery items from the existing
  database/content pipeline.
- Existing Gallery is a DOM copy column plus sticky visual and lightbox.
- Existing homepage has no production WebGL dependency.
- Current source structure gate fails before this batch because
  `resources/css/pages/welcome-hero.css` does not match its recorded checksum.
  Gallery implementation must not alter Hero or disguise that independent
  failure.
- The owner explicitly authorized direct publication to `main` for this Gallery
  replacement.

## Scope

SCOPE IN:
- homepage Gallery Blade composition;
- dedicated Gallery CSS entry and surface modules;
- dedicated Gallery JS entry, scene math, raw WebGL atmosphere, and lifecycle;
- Vite and homepage route entry registration;
- focused source-contract test;
- this blueprint and current-state ledger.

SCOPE OUT:
- dedicated `/galeri` page and its gallery wall;
- Hero, Vision/Mission, Values, Programs, Articles, navigation, footer;
- database schema, admin CRUD, translations, authentication;
- external packages and `package-lock.json`;
- fixing the pre-existing Hero checksum mismatch.

## Semantic and fallback experience

- The heading and all gallery title, metadata, caption, and links remain HTML.
- Each media card remains a normal anchor when JavaScript is unavailable.
- JavaScript enhances anchors with the existing accessible homepage lightbox.
- Reduced motion retains a static responsive card grid and does not initialize
  the depth journey or WebGL renderer.
- WebGL failure or context loss retains the DOM depth journey over a CSS
  atmosphere; content and links remain available.
- Canvas is decorative and hidden from assistive technology.

## Ownership

| Concern | Target owner |
|---|---|
| section composition | `home/sections/gallery*.blade.php` |
| layout/treatment | `css/surfaces/home/gallery-depth/` |
| route CSS entry | `css/pages/welcome-depth-gallery.css` |
| interaction/lifecycle | `js/surfaces/home/gallery-depth/controller.js` |
| depth math/palette | `js/surfaces/home/gallery-depth/scene.js` |
| raw WebGL atmosphere | `js/surfaces/home/gallery-depth/renderer.js` |
| route JS entry | `js/pages/welcome-depth-gallery.js` |

## Storyboard

- Static: semantic cards render in document flow.
- Eligible: IntersectionObserver activates near the section.
- Active: viewport becomes sticky; scroll progress moves one card at a time
  through CSS 3D depth while alternating inline positions.
- Atmosphere: one low-power WebGL canvas blends the active/next palette and
  reacts subtly to pointer and scroll velocity.
- Suspended: no RAF while offscreen or the document is hidden.
- Failed: canvas is removed and CSS atmosphere remains.
- Disposed: RAF, observer, listeners, program, buffer, and inline transforms are
  released on permanent page exit.

## Six-tier contract

| Tier | Contract |
|---|---|
| XS 360–639 | nearly full-width card, short depth travel, touch-first |
| SM 640–767 | wider static two-column fallback; same depth scene |
| MD 768–1023 | larger card and balanced tablet framing |
| LG 1024–1279 | expanded depth perspective; navigation remains untouched |
| XL 1280–1535 | full alternating cinematic composition |
| 2XL >=1536 | bounded card width with wider atmospheric field |

The same DOM, controller, canvas, and content source serve all tiers. Fluid
sizing handles interior widths. Short-height profiles reduce card height.

## Locale and direction

- ID and EN share LTR composition.
- AR uses the same DOM and time/depth progression in RTL.
- Text alignment follows logical `start`; Arabic letter spacing is not forced.
- The media sequence and vertical scroll direction do not reverse for Arabic.
- Locale switching remains the existing server-rendered page transition and
  naturally reconstructs renderer geometry after reload.

## Capability and performance

- No dependency or package change.
- Renderer is raw WebGL1 with one canvas, program, buffer, and RAF.
- DPR is capped at 1.5 and power preference is low-power.
- Renderer initializes only near the Gallery and stops offscreen/hidden.
- No gallery texture is uploaded to GPU; image media remains browser-managed
  DOM content and lazy-loaded.
- Static HTML is available before enhancement.

## Accessibility

- Canvas is `aria-hidden`.
- Stage and items retain list semantics.
- Cards are links with visible focus and no-JS destinations.
- Only the visually active depth card remains keyboard-tabbable while enhanced.
- Existing dialog close, Escape, and focus restoration are retained.
- Reduced-motion mode remains complete and static.

## Proof gates

Automated:
- `git diff --check`
- `npm run check:structure`
- `npm run build`
- focused `HomeDepthGalleryTest`
- `php artisan test`

Runtime:
- representative six-tier widths plus affected boundaries;
- ID, EN, AR and LTR/RTL;
- Chromium and WebKit;
- normal/reduced motion, pointer/touch/keyboard, short height, orientation;
- offscreen suspension, hidden tab, BFCache, context loss, lightbox focus;
- Lighthouse/PageSpeed comparison against a proven baseline.

## Known blocker

The user-provided baseline stops at `npm run check:structure` because the Hero
entry checksum is stale. That remains `BLOCKED_BY_MISSING_EVIDENCE` and is not
changed by this Gallery patch. Gallery source publication does not convert the
unrun build, PHP suite, runtime matrix, or performance gates into PASS.
