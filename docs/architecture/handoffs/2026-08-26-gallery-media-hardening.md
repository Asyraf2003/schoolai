# Gallery + Media Hardening Handoff — 2026-08-26

## Purpose

This handoff freezes the current findings around homepage Gallery behavior, slow-network/mobile resilience, first-party media storage, and the remaining execution order before Testimonial and Article are reopened on the homepage.

Use this document as the entry point for follow-up sessions. Execute one bounded packet at a time and prove it before moving to the next packet.

## Current baseline

Repository: `Asyraf2003/schoolai`

Baseline main commit before this document:

- `50fc18ee2c56c9ec17f8c95dcce91fc277912ae9` — `Remove legacy homepage gallery runtime`

Already completed:

- Homepage Gallery no longer uses the previous Three/WebGL depth-gallery engine.
- The dead `resources/js/surfaces/home/gallery-depth/` runtime family was removed.
- The dead `resources/css/surfaces/home/gallery-depth/` family was removed.
- Stale lifecycle/scroll-clock tests tied to the removed engine were removed.
- `/galeri` remains a separate surface and still owns `resources/js/pages/welcome/gallery-wall.js` plus `gallery-route-transition.js`; do not remove those as part of homepage Gallery cleanup.
- Homepage Gallery is currently DOM/CSS based and owned by:
  - `resources/js/pages/welcome-depth-gallery.js`
  - `resources/css/pages/welcome-depth-gallery.css`
  - `resources/views/home/sections/gallery-depth.blade.php`
- Static Islamic geometry assets used by UI presentation remain release-bundled under `public/media/...`; these are not CRUD content and are not required to live in R2.

Important validation gap:

- The cleanup commit is on `main`, but a fresh local `npm run build` proof after that cleanup should still be captured before further structural Gallery cleanup is considered PASS.

---

## Packet G1 — Gallery fail-open progressive enhancement

Priority: **P0**

### Observed risk

The active Gallery CSS currently hides media/copy by default through fallback custom properties such as opacity and clip-window values. The Gallery JS is loaded later through homepage preparation.

If JavaScript is slow, a dynamic import is delayed, or Gallery preparation fails, the Gallery markup can exist while the actual media/copy remains visually hidden.

This is a real slow-network resilience bug, not merely animation polish.

### Target

Make static content the safe default:

1. Gallery media and copy are visible without Gallery JavaScript.
2. JavaScript adds an explicit enhancement-ready state only after the controller has mounted successfully.
3. Motion-only initial opacity/clip/translate rules apply under that enhancement-ready state.
4. If JS fails, times out, is blocked, or arrives late, all Gallery content remains readable and usable.
5. `prefers-reduced-motion: reduce` remains fully static and readable.

### Do not change

- Current alternating desktop composition.
- Current Gallery copy/content.
- CTA behavior.
- `/galeri` page runtime.
- Global/native browser scroll behavior.

### Proof

At minimum prove:

- normal JS load;
- JavaScript disabled or Gallery import intentionally failed;
- slow-network/throttled load while scrolling to Gallery before the Gallery chunk is ready;
- reduced-motion mode.

All four cases must keep Gallery content visible.

---

## Packet G2 — Decouple Gallery preparation from earlier homepage modules

Priority: **P0**

### Observed risk

`resources/js/pages/welcome/preparation.js` currently prepares sections sequentially:

`hero -> program -> values -> vision -> gallery -> footer`

Gallery therefore waits for earlier dynamic imports/preparation even though the current Gallery controller is small and does not need the removed WebGL runtime.

On slow connections this increases the window in which a user can reach Gallery before its enhancement module is available.

### Target

- Remove unnecessary sequential dependency for Gallery.
- Start independent section preparation in parallel where dependencies allow it.
- Preserve any real Program -> Values -> Gallery handoff dependency explicitly rather than relying on incidental import order.
- A failure in Program/Values/Vision must not prevent Gallery from becoming independently usable.

### Proof

- Intentionally fail one earlier dynamic import and prove Gallery still prepares.
- No unhandled promise rejection.
- Existing section handoff remains correct.
- Homepage still degrades safely if any enhancement module fails.

---

## Packet G3 — Fast-scroll and pacing hardening

Priority: **P1**, execute only after G1/G2

### Current behavior

The Gallery currently uses native scroll. The scroll listener is passive and rendering is coalesced through `requestAnimationFrame`. There is no Gallery wheel hijacking, synthetic inertia, or old scroll clock.

This is good for state safety: final state is calculated from current element geometry rather than accumulated wheel deltas.

However, a very fast scroll can legitimately skip intermediate visual states. For example, a reveal can visually jump from mostly closed to open to closed without showing every intermediate frame. Background selection can also jump over an item because the nearest media to viewport center changes immediately.

### Target

- Keep native browser scrolling.
- Do not add global smooth-scroll/Lenis-style scroll hijacking.
- Make the Gallery visual timeline approximately 25–35% more relaxed if visual testing confirms the current pace still feels too fast.
- Tune Gallery-owned progress distances/section spacing, not the user's physical scroll delta.
- Fast-scroll must never leave stale opacity, clip-path, title, or background state behind.

Candidate values to test, not blindly lock:

- item vertical distance around `120–130svh` instead of `108svh` on large screens;
- reveal distance around `0.78–0.85 * viewportHeight` instead of `0.62`;
- center/open-close distance around `0.62–0.70 * viewportHeight` instead of `0.52`.

### Proof

Test slow wheel, aggressive wheel, trackpad fling, scrollbar drag, PageDown, Home/End, and reverse-direction scrolling.

---

## Packet G4 — Tablet/mobile viewport hardening

Priority: **P1**

### Observed risks

1. Compact Gallery layout currently starts at `max-width: 1023px`.
2. Fixed desktop handoff starts at `min-width: 1280px`.
3. The `1024–1279px` range therefore keeps the 12-column desktop Gallery composition without receiving the desktop fixed handoff. This is a specific tablet/compact-laptop zone that needs deliberate validation.
4. CSS vertical geometry uses stable viewport units such as `svh`, while JS animation math uses `window.innerHeight`. Mobile browser chrome expansion/collapse can change `innerHeight` during scroll and create small motion discontinuities.

### Target

- Define deliberate behavior for `1024–1279px`; do not leave it as an accidental breakpoint gap.
- Keep media/copy readable on portrait and landscape tablets.
- Avoid animation jumps when Safari/Chromium browser chrome changes height.
- Preserve 3-language and RTL behavior.

### Proof matrix

At minimum validate representative viewport classes:

- narrow phone portrait;
- large phone portrait;
- phone landscape;
- tablet portrait;
- tablet landscape around 1024–1279px;
- desktop >=1280px;
- large desktop.

Also test Safari/WebKit and Chromium behavior where available.

---

## Packet M1 — Current media ownership model

Status: **architecture verified from source**

Do not use the sentence "all media is on Cloudflare". The actual ownership model is intentionally split into three classes.

### A. First-party CRUD uploads

New first-party files uploaded through admin use `App\Support\Media\R2MediaStorage` and the configured media disk.

Production/default media configuration points to:

- `MEDIA_DISK=s3`
- `MEDIA_PUBLIC_URL=https://media.almustaqbal.sch.id`
- Cloudflare R2 through the S3-compatible `AWS_ENDPOINT`

Verified upload namespaces include:

- Hero media: `hero/slides/...`
- Hero posters: `hero/posters/...`
- Homepage Gallery photos: `gallery/homepage/...`
- Gallery page photos: `gallery/page-media/...`
- PPDB showcase photos: `ppdb/showcase/...`
- Testimonial upload photos/videos: `testimonials/photos/...` / `testimonials/videos/...`
- Article Canvas content: `articles/content/...`
- Article thumbnails: `articles/thumbnails/...`

The database stores the canonical public media URL returned by `MediaUrlResolver`, normally under `https://media.almustaqbal.sch.id/...`.

`R2MediaStorage` verifies the uploaded object exists after writing it. UUID-based object keys allow the current long immutable cache policy to remain safe.

### B. External provider media

Provider/embed URLs are not first-party R2 objects and must not be forced into R2 without a separate ownership/licensing decision. Examples include:

- YouTube;
- TikTok;
- Instagram;
- Vimeo;
- Facebook;
- Unsplash URLs used by Article Canvas search/provider flows.

### C. Release-bundled UI/static assets

Brand assets, UI chrome, presentation textures, semantic fallbacks, and source-controlled static assets may remain under `public/media/...` / built release assets.

Examples include the Gallery/vision/value Islamic geometry WebP textures and social/provider logo assets.

This is correct. The target is **not** to make `public/media` empty.

---

## Packet M2 — Audit production legacy media migration

Priority: **P0 before declaring R2 migration complete**

### Current source capability

`App\Support\Media\LegacyMediaMigrator` can migrate bounded first-party legacy media references from:

- `/storage/...`
- `/media/...`
- `media/...`

Owners include Hero, homepage Gallery, Gallery page, PPDB, Testimonials, and Articles.

Available command:

```bash
php artisan media:migrate-r2 {owner?} --dry-run
```

### Important distinction

Source code proves that **new admin uploads use R2**.

Source code alone does **not** prove that every existing production database row has already been migrated from legacy local paths.

### Execution order

Run dry-run owner by owner on the actual production dataset, inspect counts, then migrate only after the result is understood:

```bash
php artisan media:migrate-r2 hero --dry-run
php artisan media:migrate-r2 gallery-homepage --dry-run
php artisan media:migrate-r2 gallery-page --dry-run
php artisan media:migrate-r2 ppdb --dry-run
php artisan media:migrate-r2 testimonials --dry-run
php artisan media:migrate-r2 articles --dry-run
```

Do not remove legacy `storage/app/public` data until every relevant owner, public render, delete/restore lifecycle, and database reference has been verified.

---

## Packet M3 — Image delivery pipeline

Priority: **P0/P1**

### Observed risk

R2 currently stores the uploaded first-party image bytes as supplied after safety validation. `SafeImageUpload` validates file structure, MIME/extension consistency, dimensions, pixel count, and active payload markers, but it does not resize, recompress, or create responsive variants.

Gallery accepts image uploads up to 10 MB per file and homepage Gallery can show up to 6 items. Lazy loading protects initial page load, but a user who traverses the Gallery can still download unnecessarily large source images.

Current Gallery `<img>` uses lazy loading and async decode but does not provide `srcset`/`sizes` variants.

### Target

Introduce a bounded first-party image derivative pipeline. Exact format strategy should be measured before locking, but the desired capability is:

- responsive widths such as approximately 480 / 768 / 1280 / 1600;
- optimized WebP and/or AVIF where supported by the chosen server toolchain;
- preserve a suitable canonical/original object only if operationally useful;
- persist derivative metadata deterministically;
- emit `srcset` + `sizes` from public views;
- no request-time CPU-heavy image transformation on shared hosting;
- old/failed derivative objects must be cleaned transactionally.

### Proof

Measure transferred bytes and LCP/near-viewport Gallery behavior under slow network before and after.

---

## Packet M4 — Slow-media rendering state

Priority: **P1**

### Target

When a user fast-scrolls into Gallery on a slow network:

- layout dimensions are already reserved;
- no large CLS occurs;
- a deliberate neutral/low-cost media placeholder is visible until decode completes;
- failed media has a stable fallback instead of a permanently blank rectangle;
- animation state is independent from image decode success.

Do not make the whole Gallery wait for all images to preload.

---

## Packet M5 — Direct-to-R2 uploads for large media

Priority: **P2**

### Current upload path

The browser currently sends multipart upload data to Laravel/PHP first. Laravel validates it, then `R2MediaStorage` writes it to R2.

Therefore cPanel/PHP limits such as `upload_max_filesize`, `post_max_size`, request timeouts, memory pressure, and hosting bandwidth remain relevant even though final storage is R2.

### Target for large native video only when justified

Consider presigned/direct-to-R2 upload:

1. admin requests a bounded upload authorization/key from Laravel;
2. browser uploads binary directly to R2;
3. Laravel records/validates completion and metadata;
4. abandoned or invalid objects are cleaned.

Do not introduce this complexity merely for ordinary small images if the optimized image pipeline already solves the practical issue.

---

## Execution order for future sessions

Keep each item isolated. Do not combine them into one mega-refactor.

1. **Build proof for commit `50fc18ee...`**.
2. **G1 — Gallery fail-open**.
3. **G2 — independent/parallel Gallery preparation**.
4. **G3 — Gallery fast-scroll pacing**.
5. **G4 — tablet/mobile viewport hardening**.
6. **M2 — production legacy-media dry-run audit**.
7. **M3 — image derivative/responsive delivery pipeline**.
8. **M4 — slow-media placeholder/error/decode state**.
9. **M5 — direct-to-R2 large-media upload only if real upload limits justify it**.
10. **Program runtime cleanup/polish** after Gallery is stable.
11. Reopen **Testimonial** only after its existing runtime is audited and one owner is chosen.
12. Reopen **Article** last, after old debug/test presentation nodes are removed and its runtime ownership is simplified.

## Global rules for these packets

- Preserve native browser scrolling.
- Do not reintroduce the deleted homepage Three/WebGL Gallery engine.
- Keep `/galeri` page runtime separate from homepage Gallery.
- Keep static source-controlled UI assets release-bundled unless there is a concrete operational reason to move them.
- New first-party CRUD media must remain R2-owned.
- External/provider URLs must remain outside first-party R2 delete lifecycle.
- Every change needs a failure-mode proof, not only a happy-path screenshot.
- Prefer one commit per bounded packet so regressions remain attributable.
