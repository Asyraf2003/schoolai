# Homepage Program — Kinetic Type Transition Blueprint

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Date: 2026-08-08
Owner decision: replace Program completely with the Codrops kinetic-type template behavior.
Reference: `https://github.com/codrops/KineticTypePageTransition`
Reference license: MIT.

## FACT

- The reference is a click-driven kinetic typography page transition, not a scroll gallery.
- Its choreography is: staggered media items -> items fade/translate away -> oversized repeated type scales/rotates and travels laterally -> selected detail enters -> back action reverses the transition.
- The reference uses GSAP `^3.7.1` and `imagesloaded` only for preload handling.
- The Codrops code is MIT licensed; its Adobe/Typekit font licensing is separate and is not inherited through MIT.
- SchoolAI Program contains six paths: PG, TK, SD, TQ, MB, LT.
- Homepage supports ID/EN/AR, LTR/RTL, reduced motion, compact/wide layouts, Chromium and WebKit.

## OWNER CORRECTION

The first SchoolAI implementation translated the Codrops behavior to WAAPI. The owner subsequently chose higher reference fidelity and explicitly approved using the template behavior directly.

Therefore the active implementation now:

- uses GSAP 3.7.1 timing/easing semantics from the reference;
- preserves the reference scale `2.7`, quarter-turn, line stagger, item exit and article reveal choreography;
- keeps SchoolAI content, locale ownership, accessibility integration and six-card adaptation;
- does not copy the Codrops images or Adobe Typekit font.

## GOAL

Render six Program cards in the Codrops staggered composition. Activating one card transitions into its full-viewport detail through the kinetic typography field. Back or Escape reverses the sequence and restores the card composition.

## CONTENT CONTRACT

All public Program copy is locale-owned in:

- `lang/id/home_program.php`
- `lang/en/home_program.php`
- `lang/ar/home_program.php`

Each item owns code, category, title, summary, description and optional next-program code. Blade contains no locale switch/match for public Program copy.

## REFERENCE FIDELITY

Adopt directly:

- GSAP timeline model;
- `power2.inOut`, `power1`, `power3`, `power4`, `expo` and `back` easing roles;
- type field scale to `2.7`;
- LTR quarter-turn `-90deg`, mirrored to `+90deg` in RTL;
- type-line `20% -> -200%` travel with `0.04` stagger, mirrored in RTL;
- alternating `+25% / -25%` item exit;
- detail copy entering from `50%` Y with stagger;
- image wrapper `100% -> 0` and image `-100% -> 0` reveal;
- reversible close timeline.

Adapt because SchoolAI has six items instead of four:

- desktop item width and vertical interval;
- localized copy length;
- responsive compact layout;
- RTL motion direction;
- focus trap, Escape handling and focus restoration;
- Program-local body scroll lock only while detail is open.

Do not copy:

- Codrops prose/content;
- Codrops image files;
- Adobe Typekit kit/fonts;
- global page identity outside Program.

## GSAP DELIVERY

SchoolAI does not add GSAP to `package.json` or `package-lock.json` in this batch. Program lazily requests the same GSAP 3.7.1 runtime from jsDelivr only when the Program enhancer mounts. This keeps the main Vite dependency graph unchanged while preserving reference timing.

If GSAP cannot load, full Program descriptions remain available through the non-enhanced fallback.

## MEDIA

Current development media uses six Unsplash URLs. Final production media is expected to move to Cloudflare-delivered school photography.

Recommended Cloudflare contract:

- source originals retained once;
- delivery variants sized to viewport need;
- WebP/AVIF negotiation where supported;
- responsive `srcset`/`sizes` rather than one 2400px file for every device;
- lazy loading for non-critical Program media;
- immutable CDN cache headers for versioned assets.

## RTL / LTR

- text direction follows document `dir`;
- Arabic uses the existing Cairo stack;
- kinetic rotation and lateral line travel mirror under RTL;
- six-program logical order remains PG -> TK -> SD -> TQ -> MB -> LT;
- keyboard behavior remains direction-neutral.

## REDUCED MOTION

With `prefers-reduced-motion: reduce`, the kinetic GSAP transition is bypassed and detail state changes immediately while preserving semantic content, dialog focus behavior and Back/Escape controls.

## THIRD-PARTY NOTICE

The required MIT copyright and permission notice is stored at:

`docs/licenses/CODROPS_KINETIC_TYPE_PAGE_TRANSITION_MIT.md`

## SCOPE

Allowed: Program Blade, Program CSS/JS modules, Program locale files, focused Program test, Program blueprint/current-state docs, and the Codrops MIT notice.

Forbidden: About/Visi/Misi, Hero, Values, Gallery, Articles, navbar/footer and unrelated source-structure failures.

## PROOF GATE

Static/local:

- `git diff --check`
- `php artisan test --filter=HomeProgramJourneyTest`
- full `php artisan test`
- `npm run check:structure`
- `npm run build`

Runtime:

- all six cards open the correct detail;
- back/Escape and focus restoration work;
- repeated open/close leaves no stale body lock;
- LTR/RTL kinetic travel is correct;
- reduced motion is readable;
- 360 through 1920 widths are usable;
- Chromium and WebKit match the intended Codrops transition character;
- Cloudflare image migration is measured separately when final assets exist.

Until runtime proof exists, status remains `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`.
