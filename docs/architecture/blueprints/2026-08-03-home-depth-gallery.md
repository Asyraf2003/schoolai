# Homepage Atmospheric Depth Gallery

BLUEPRINT ID: `HOME-GALLERY-003`
STATUS: `IMPLEMENTING`
OWNER: Asyraf
DATE: 2026-08-03
SOURCE MAIN SHA: `70800053340caf6643998e09a743bd3ea61b5348`
ACTIVE ROUTE/SURFACE: homepage Gallery only
TARGET EXECUTION CHANNEL: Web AI with GitHub connector
REFERENCE: `houmahani/codrops-depth-gallery`

## Owner goal

Use the reference's atmospheric scroll-through-depth experience while keeping
SchoolAI database content, photo/video opening behavior, homepage section order,
three locales, and one shared UI architecture.

Owner correction requires:

- title, metadata, and caption beside the media instead of over the image;
- square image corners with no decorative crop or forced aspect ratio;
- intrinsic image proportions constrained only by maximum width and height;
- clearly visible background palette changes as the active item changes;
- one working composition across all six responsive tiers.

The reference contributes depth travel, alternating media placement, adjacent
labels, velocity-reactive atmosphere, and palette changes. Its assets, flower
identity, labels, modules, shaders, and branding are not copied.

## FACT

- Homepage receives at most six normalized gallery items from the existing
  database/content pipeline.
- The first implementation incorrectly overlaid text, rounded/cropped images,
  and left the previous homepage Gallery controller and partial in source.
- Current source structure gate already fails outside Gallery because
  `resources/css/pages/welcome-hero.css` does not match its recorded checksum.
- Legacy CSS is distributed through mixed historical modules that also own
  Programs, Footer, and shared rules. They cannot be deleted as Gallery-only
  files without a separate owner split and checksum migration.
- The owner authorized direct publication to `main` for this Gallery scope.

## Scope

SCOPE IN:
- homepage Gallery Blade composition;
- dedicated Gallery CSS and JS surface modules;
- visible atmosphere synchronization and raw WebGL shader treatment;
- removal of the retired homepage Gallery Blade partial/controller/import;
- focused source-contract test;
- blueprint and current-state ledger.

SCOPE OUT:
- dedicated `/galeri` page and gallery wall controller;
- mixed legacy CSS migration outside proven Gallery ownership;
- Hero, Vision/Mission, Values, Programs, Articles, navigation, footer;
- database schema, admin CRUD, translations, authentication;
- external packages and `package-lock.json`;
- the pre-existing Hero checksum mismatch.

## Semantic and fallback experience

- Heading, title, metadata, caption, and links remain HTML outside canvas.
- Media and copy are adjacent siblings. Copy never overlays the image.
- Images use intrinsic width/height with `max-width` and `max-height`; no
  `object-fit: cover`, forced ratio, clipping, or rounded media corners.
- Each item remains a normal anchor without JavaScript.
- JavaScript enhances it with the shared accessible homepage lightbox.
- Reduced motion retains a static responsive sequence and skips depth/WebGL.
- WebGL failure retains depth motion and DOM-controlled palette changes.
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
| shared media dialog | `js/pages/welcome/gallery-story-lightbox.js` |

Retired owners removed in this batch:

- `views/home/sections/gallery-story.blade.php`;
- `js/pages/welcome/gallery-story.js`;
- its import from `js/pages/welcome.js`.

## Storyboard

- Static: intrinsic media and adjacent copy render in document flow.
- Eligible: IntersectionObserver activates near the section.
- Active: viewport becomes sticky and each media/copy pair travels through CSS
  3D depth with alternating physical placement.
- Atmosphere: the DOM viewport and WebGL canvas blend the same active/next
  palette, ensuring the color change remains visible with or without WebGL.
- Suspended: no RAF while offscreen or document-hidden.
- Failed: WebGL is removed while DOM depth and palette progression remain.
- Disposed: RAF, observer, listeners, GPU resources, and inline transforms clear.

## Six-tier contract

| Tier | Contract |
|---|---|
| XS 360–639 | stacked intrinsic media/copy within bounded sticky height |
| SM 640–767 | adjacent media/copy with mirrored alternating order |
| MD 768–1023 | larger intrinsic media bounds and tablet perspective |
| LG 1024–1279 | wider side-copy composition and short-height guard |
| XL 1280–1535 | full alternating cinematic field |
| 2XL >=1536 | bounded 1320px composition inside wider atmosphere |

The same DOM, controller, canvas, and content source serve all tiers. RTL mirrors
adjacent placement through CSS order while vertical time/depth stays unchanged.

## Capability and performance

- No package or lockfile change.
- One raw WebGL1 canvas/program/buffer and one RAF.
- DPR capped at 1.5 with low-power preference.
- Renderer initializes near Gallery and stops offscreen/hidden.
- Images remain lazy DOM media and are not uploaded as GPU textures.
- DOM background palette updates independently from WebGL.

## Accessibility

- Canvas is `aria-hidden`.
- Stage/items retain list semantics and cards remain links.
- Focus is visible and only the active enhanced item is tabbable.
- Shared dialog retains Escape, close controls, and focus restoration.
- Reduced-motion result remains complete and readable.

## Proof gates

Automated:
- `git diff --check`;
- `npm run check:structure`;
- `npm run build`;
- focused `HomeDepthGalleryTest`;
- `php artisan test`.

Runtime:
- 360, 390, 640, 768, 1024, 1280, 1440, 1536, and 1920;
- global boundary pairs and relevant short-height/orientation cases;
- ID, EN, AR and LTR/RTL;
- Chromium and WebKit;
- normal/reduced motion, pointer/touch/keyboard;
- offscreen suspension, hidden tab, BFCache, context loss, lightbox focus;
- Lighthouse/PageSpeed comparison against a proven baseline.

## Known blocker

The repository structure gate is already blocked by the unrelated Hero checksum.
This Gallery correction must not hide or repair that failure. Connector source
inspection can prove ownership and declarations, but build, browser, runtime,
and performance remain `BLOCKED_BY_MISSING_EVIDENCE` until actually run.
