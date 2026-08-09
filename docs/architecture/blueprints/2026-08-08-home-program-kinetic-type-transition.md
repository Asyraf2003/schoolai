# Homepage Program — Kinetic Type Transition Blueprint

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Date: 2026-08-08
Owner decision: replace Program with the Codrops kinetic-type transition behavior while adapting SchoolAI content and final detail presentation.
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

The active SchoolAI adaptation:

- uses GSAP 3.7.1 timing/easing semantics from the reference;
- preserves the reference kinetic type scale, quarter-turn, line stagger, item exit, detail media reveal and reversible choreography;
- keeps SchoolAI content, locale ownership, accessibility integration and six-card adaptation;
- intentionally removes article metadata and multi-paragraph hierarchy from the final detail screen;
- does not copy the Codrops images or Adobe Typekit font.

## GOAL

Render six Program cards in the Codrops staggered composition. Activating one card transitions through the kinetic typography field into a clean full-viewport detail containing localized Back, one large Program image, the selected Program title and one description. Back or Escape reverses the sequence and restores the card composition.

## CONTENT CONTRACT

All public Program copy is locale-owned in:

- `lang/id/home_program.php`
- `lang/en/home_program.php`
- `lang/ar/home_program.php`

Blade contains no locale switch/match for public Program copy.

Idle cards render only title + description. The detail state renders only:

- localized Back label (`Kembali`, `Back`, `العودة`);
- triple-chevron back mark (`<<<`, mirrored under RTL);
- selected Program image;
- selected Program title;
- selected Program description.

Item metadata such as code, eyebrow, summary and next code may remain in locale data, but they are not rendered in the public detail state.

## DETAIL PRESENTATION CONTRACT

- One large Program image is restored in detail, using the same development media source as the selected idle card.
- No `01 / 06` number.
- No eyebrow/category.
- No secondary intro/summary.
- No next-program code.
- Detail title keeps the Program section-heading editorial grammar but at roughly one-third of the previously oversized detail scale: light display weight, tight Latin tracking, restrained scale and uppercase presentation in ID/EN.
- Arabic detail title uses Cairo, normal Arabic casing and Arabic-appropriate tracking/line-height.
- Description is one restrained readable paragraph.
- Desktop uses clean copy + image columns; compact tiers stack image before copy.
- Back remains a semantic button with focus restoration, Escape support and RTL chevron mirroring.

## PROGRAM HEADING CENTER-SPLIT CONTRACT

- The Program section heading remains a two-line center-split reveal in ID/EN.
- Line one starts below its own mask and resolves upward; line two starts above its own mask and resolves downward.
- The masks have explicit breathing room between them; negative block margins are forbidden because they visually merge the two reveal zones.
- Hidden travel exceeds one line height so glyphs begin fully outside their masks.
- Reveal is replayable: leaving the heading viewport resets the reveal state and re-entering from either above or below triggers it again.
- No horizontal heading translation is introduced.
- Reduced motion resolves immediately.

## REFERENCE FIDELITY

Adopt directly:

- GSAP timeline model;
- `power2.inOut`, `power1`, `power3`, `power4`, `expo` and `back` easing roles;
- type field scale to `2.7`;
- LTR quarter-turn `-90deg`, mirrored to `+90deg` in RTL;
- type-line `20% -> -200%` travel with `0.04` stagger, mirrored in RTL;
- alternating `+25% / -25%` item exit;
- detail copy entering vertically;
- image wrapper `100% -> 0` and image `-100% -> 0` reveal;
- reversible close timeline.

Intentionally do not adopt from the reference detail article:

- article number;
- category/eyebrow;
- multi-paragraph intro + description hierarchy;
- next-item metadata.

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

Current development idle-card and detail media use the same six Unsplash URLs. Final production media is expected to move to Cloudflare-delivered school photography.

Recommended Cloudflare contract:

- source originals retained once;
- delivery variants sized to viewport need;
- WebP/AVIF negotiation where supported;
- responsive `srcset`/`sizes` rather than one oversized file for every device;
- lazy loading for non-critical Program media;
- immutable CDN cache headers for versioned assets.

## RTL / LTR

- text direction follows document `dir`;
- Arabic uses the existing Cairo stack;
- kinetic rotation and lateral line travel mirror under RTL;
- the `<<<` Back mark mirrors to point toward the RTL return direction;
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

- all six cards open the correct clean detail;
- detail contains only Back, image, title and one description;
- title scale is materially smaller than the prior oversized detail implementation;
- ID/EN title typography follows the Program heading grammar;
- Arabic detail remains readable and mirrors Back direction correctly;
- Program section center split replays after leaving and re-entering from either scroll direction;
- the two heading masks preserve a visible gap and do not collide during reveal;
- back/Escape and focus restoration work;
- repeated open/close leaves no stale body lock;
- LTR/RTL kinetic travel is correct;
- reduced motion is readable;
- 360 through 1920 widths are usable;
- Chromium and WebKit match the intended kinetic transition character.

Until runtime proof exists, status remains `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`.
