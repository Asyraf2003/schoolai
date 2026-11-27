# Uniform Scroll Pacing, White Gallery, Testimonials, and Stories Choreography

BLUEPRINT ID: `HOME-SCROLL-GALLERY-TESTIMONIALS-STORIES-01`
STATUS: `OWNER_ACCEPTED`
OWNER: Asyraf
DATE: 2026-11-27
SOURCE MAIN SHA: `8ca97044ae0f15e0cdc4f468659ffcc63511938f` (with local preview baseline from `2026-11-26-gallery-testimonials-handoff.md`)
ACTIVE ROUTE/SURFACE: Homepage `/` (`scroll-pacing`, `gallery`, `testimonials`, `stories`)
TARGET EXECUTION CHANNEL: Local agent

## 1. Owner goal and decisions

Explicit owner decisions from prompt + clarification Q&A (`A1`, `A2`, `A3`, `A4`):

1. **Uniform top-to-bottom scroll speed and standardized inertia (`GAP-SCROLL-01`)**:
   - Replace the variable per-section pacing (`2x` delta turbo + `600ms` coast in free space vs `limitPacingTravel` speed brakes in About/Program/Values/Gallery) with one uniform standard across the entire homepage.
   - Constant weighted luxury input gain `SCROLL_INPUT_GAIN = 0.85` (within owner's `0.8`–`0.9` spec) and standard frame-rate-independent exponential inertia decay (`SCROLL_INERTIA_TAU_MS = 180`, `step = pending * (1 - exp(-elapsed / 180))`) so speed tapers smoothly `20 -> 19 -> 18 -> ... -> 0` on release, with identical wheel distance and inertia from Hero to Footer.
   - Preserve `footerGate` two-stage white/blue `/galeri` checkpoint behavior and native nested/touch/reduced-motion exemptions.
2. **Homepage section sequence**:
   - Reorder homepage to `Hero -> About -> Program/Values -> Gallery -> Testimonials -> Stories & Insights -> Footer`.
3. **White Gallery + 34-tile bottom alignment (`GAP-GALLERY-BOTTOM-01`, `GAP-GALLERY-WHITE-01`)**:
   - Gallery background is white (`#fff`), blending smoothly from Values blue (`#2038ff`) into `#fff` on entry (and reversing to `#2038ff`).
   - Heading (`Galeri`/`Gallery`/`المعرض`) renders in dark ink (`#171717`) on `#fff`.
   - Center statement renders in dark ink (`#171717`) with a soft white radial mist halo (`--gallery-statement-mist`) that fades in with word reveal and fades out during exit so text remains readable over both dark/bright media and white space.
   - Keep the 30 tiles (10 unique assets across 3 passes in `3:4:3` lanes: 9/12/9) and add **2 random bottom tiles on the left and 2 on the right** (**34 tiles total = 11 left / 12 center / 11 right**), randomly selected per page render without duplicating immediate vertical or horizontal neighbors, preserving the fixed lead (`index 0`) and semantic pass 1.
   - Replace hardcoded `700svh`/`1000svh` tracks with measured `1:1` scroll-to-visual rise (`risePx = settledRise`) and standardized `H`-proportional shrink/assembly (`1.15 * H`) and exit (`0.75 * H`).
   - Release sticky (`bottom bottom`) at **80% of the exit blur/fade** (`exitPinnedPx = exitPx * 0.80`), so Testimonials rises `1:1` from the bottom edge while the final `20%` (`exitTailPx = exitPx * 0.20`) finishes dissolving to `#fff` without double-speeding or black gaps.
4. **Standardized Stories & Insights travel (`GAP-STORIES-01`)**:
   - Place Stories & Insights after Testimonials on `#fff`.
   - Enforce at least `100svh` (`stageHeight`) per chapter transition (`Math.max(stageHeight, anchors[i - 1] + heights[i - 1] / 2)`) with `1:1` caption movement so each full-viewport story scrolls at the same deliberate cadence as the rest of the page.

## 2. Scope and ownership

- **Editable**:
  - `resources/views/landing/index.blade.php`, `resources/views/landing/gallery.blade.php`
  - `resources/css/sections/gallery.css`, `resources/css/sections/stories.css`
  - `resources/js/index.js`, `resources/js/sections/program.js`, `resources/js/sections/values.js`, `resources/js/sections/section-heading.js`
  - `resources/js/sections/scroll-pacing-core.js`, `resources/js/sections/scroll-pacing.js` (and retire superseded `scroll-pacing-policy.js`, `scroll-pacing-policy-cache.js`, `scroll-pacing-poses.js`, `scroll-pacing-transitions.js`)
  - `resources/js/sections/gallery.js`, `resources/js/sections/gallery-geometry.js`, `resources/js/sections/gallery-timeline.js`, `resources/js/sections/gallery-media.js`
  - `resources/js/sections/stories-motion.js`
  - Affected PHP, Node, and browser tests + `docs/architecture/UI_UX_CURRENT_STATE.md`
- **Read-only / Protected**:
  - Header/navigation, Hero, About, Program cards/dialog, Values cards/line, Testimonials wall/rotation/loop/drag, Footer white/blue `/galeri` gate, local `.env` and SQLite/MySQL DB, `LandingGalleryPresenter` 10-item catalog source.

## 3. Six-tier, locale, and accessibility contracts

- **Six tiers (XS 360–639, SM 640–767, MD 768–1023, LG 1024–1279, XL 1280–1535, 2XL >=1536)**:
  - Gallery uses shared `3:4:3` geometry (`11/12/11` enhanced, `1 lead + 3-column` 11-row balanced static grid in reduced/no-JS) and `1:1` pixel-measured sticky length across all tiers.
  - Desktop fine-pointer (`>= 1024px`, `hover: hover`, `pointer: fine`) uses uniform `0.85x` wheel gain + `180ms` exponential inertia; touch, tablet/phone, coarse pointer, and `prefers-reduced-motion` stay browser-native.
- **Locales (`ID`, `EN`, `AR`)**:
  - One semantic Blade tree. `ID`/`EN` stay Inter/LTR; `AR` retains Cairo/RTL (`gallery` columns mirror horizontally; vertical scroll direction stays unmirrored).
  - Pass 1 keeps localized `alt` + `figcaption`; passes 2, 3, and the 4 bottom additions remain `aria-hidden="true"` decorative tiles; statement `.sr-only` remains complete in the accessibility tree.
- **Lifecycle & budgets**:
  - No new dependencies; Gallery still caps hydrated/playing videos at 2 and releases on exit cutoff (`galleryMediaCutoff`), hidden tab, `pagehide`, and `reduced-motion`. All source files <= 200 lines.

## 4. Proof gates

- `git diff --check`
- `npm run check:structure`
- `npm run build`
- `php artisan test --compact` (SQLite in-memory)
- `node --test tests/Unit/*.test.mjs tests/Js/*.test.mjs` (unit + geometry + stories + footer-gate + pacing)
- Headless Chromium browser inspection against built local server (uniform wheel speed ratio across Hero/Values/Gallery/Testimonials/Stories, 34 Gallery tiles, white Gallery transition & 80% blur handoff to Testimonials, Stories `100svh` travel, ID/EN/AR, 390/768/1440px, reduced-motion & no-JS).
