# Home Values + Media Startup Handoff — 2026-08-27

## Purpose

This handoff freezes the work completed around Homepage Pondasi Karakter responsive motion, About video ownership, Hero/media startup behavior, and the new sequential homepage preparation pipeline.

Use this document as the entry point for the next session. Do not reconstruct behavior from chat history unless this handoff explicitly marks a GAP.

Repository: `Asyraf2003/schoolai`

Main baseline at the start of this handoff document:

- `3c1c30c271ecfbf954493dc98108eec2630e2471` — `Keep hero readiness modular`

---

## 1. Pondasi Karakter — responsive fast-scroll correctness

### Original problem

Responsive Values/Pondasi Karakter had two related visual failures:

1. fast scroll could leave card/background state visually behind the real scroll position;
2. tablet/mobile card flip geometry became exaggerated and visually inconsistent when cards approached edge-on.

The old responsive path still inherited motion/spring behavior that made rendered state lag the authoritative scroll state. Tablet-specific rail motion amplified that lag.

### Current contract

For responsive modes (`geometry.mode < 4`):

- card state follows the current scroll target directly;
- responsive state does not wait for spring catch-up;
- shared handoff/background state is reconciled from current target progress;
- IntersectionObserver is an optimization/lifecycle signal, not a correctness gate;
- desktop mode may retain its richer spring/momentum choreography;
- responsive fast scroll must produce the same card pose for the same scroll offset regardless of how quickly the user arrived there.

Relevant commit:

- `8ddff7b06aade9dd915a3d1600591284af9d83ba` — `Make values cards deterministic on responsive scroll`

### Responsive flip geometry

The accepted mobile/tablet visual model is intentionally different from desktop 3D perspective.

Rules:

- every responsive card flips in the same direction;
- left/right vertical card edges remain visually upright;
- the horizontal/top edge may slope to sell the turning perspective;
- whole-card `rotateZ` is not used for the responsive perspective cue;
- `skewY` supplies the bounded horizontal-edge slope;
- `rotateY` supplies the actual front/back flip;
- maximum tilt remains bounded by `RESPONSIVE_MAX_TILT = 20` and by approximately 95% of measured row gap rise;
- the tilt side swaps after the card crosses the midpoint/edge-on state, so the flip reads as physically continuous rather than a face swap with unchanged perspective.

Relevant commits:

- `252d275fc34ed23b3ba07bd170e8c094d055fd19` — `Align responsive values flip direction`
- `f4448553a5c2b4d0a2de0dc203b9ba9c1a5c95a4` — `Swap responsive values tilt side through flip`

Current conceptual responsive frame:

```text
back face
  right-side perspective
        ↓
     edge-on
        ↓
  left-side perspective
front face
```

Do not reintroduce alternating card directions (`index % 2`) on mobile/tablet.

### Values test corrections

Two stale test assumptions were corrected during this work:

- the Values test no longer asserts homepage-global absence of `aria-pressed`, because Hero legitimately owns an accessible audio toggle;
- the tilt test now locks the midpoint side swap behavior rather than the previous single-expression implementation string.

Relevant commits:

- `bcbaa0c46e168dc8ed9e3e413806d371c97e070f` — `Scope values aria contract to its section`
- `5917d866ed977ead172bf374e40c49006de40bd9` — `Align values tilt test with flip side swap`

---

## 2. About video — final ownership model

### Rejected approach

Do not use the full About video as an autoplay preview and repeatedly seek between second 30 and second 50.

That design caused unnecessary video decode/network work and could generate additional byte-range seeks. Cloudflare/R2 being fast does not make repeated seeking through a full video an efficient thumbnail strategy.

### Accepted approach

The About visual is now a static image thumbnail with a centered play cue.

Initial homepage behavior:

```text
About thumbnail image
+ centered play cue
+ zero About video request
+ zero About video decoder
```

The full About video URL remains in `data-about-video-src` on the modal player with `preload="none"`.

Only after explicit click:

```text
open modal
→ attach full video source
→ load()
→ play()
→ native progressive playback / byte-range streaming
```

Current Blade owner:

- `resources/views/home/sections/vision-mission.blade.php`

Current JS owner:

- `resources/js/pages/welcome/about-video-modal.js`

The current thumbnail uses the existing first `$schoolImages` image. If a future session wants an exact frame exported from the video, replace the thumbnail asset only. Do not restore an autoplay preview just to obtain a moving thumbnail.

The full About media remains configured through:

- `config('media.homepage_about_video_url')`
- public media host: `https://media.almustaqbal.sch.id`

Earlier intermediate commit that introduced lazy progressive video behavior before the static-thumbnail decision:

- `6d8f94fca6d17a30a28435480b41fb0f4d15f80b` — `Stream homepage videos on demand`

The final static-thumbnail behavior is included in the startup-priority work described below.

---

## 3. Hero media — current streaming rule

The first Hero video remains autoplay, muted, and playsinline.

Its preload was reduced from aggressive `auto` to:

```html
preload="metadata"
```

Other Hero slides continue to avoid eager hydration where possible.

Important limitation:

Native HTML video does not provide a contract such as "download exactly 2–3 seconds and stop". The browser controls buffer size. The intended behavior is therefore:

- allow metadata / startup range work;
- begin playback as early as possible;
- continue progressive/range fetching as playback advances;
- do not eagerly download unrelated homepage video media.

Do not introduce HLS/DASH/MSE solely to enforce a literal 2–3 second buffer unless measured requirements later justify that complexity.

Test-only correction after the preload change:

- `af85b2e78154ed282455061e00b71317a684e8bf` — `Fix literal hero preload assertion`

---

## 4. Homepage startup — accepted priority pipeline

### Original problem

`welcome.js` previously scheduled homepage preparation immediately after DOM readiness. The browser could therefore be decoding/fetching Hero media while also importing and mounting Program, Values, Vision and Gallery work.

This made a fast Cloudflare media path still feel slow because network, parsing, CPU, observers, animation setup and external GSAP work competed during Hero startup.

### Critical product correction

The desired model is **not scroll-triggered lazy loading**.

The user does not need to approach a section before its level starts preparing.

The accepted model is an automatic sequential priority pipeline:

```text
P0 Hero
↓ Hero reports ready / begins presentation
P1 Vision + About
↓ level resolves
P2 Program
↓ level resolves, including GSAP success or fallback readiness
P3 Values
↓ level resolves
P4 Gallery
↓ level resolves
Footer
```

Current source order in `resources/js/pages/welcome/preparation.js`:

```js
[
  'hero',
  'vision',
  'program',
  'values',
  'gallery',
  'footer',
]
```

Each stage is awaited before the next stage begins. A short browser yield occurs between levels so rendering/input is not starved by a continuous import/mount chain.

### Hero readiness gate

Homepage background preparation starts only after Hero readiness is reported through:

- event: `schoolai:hero-ready`
- document marker: `data-hero-ready="true"`

Hero readiness logic lives in the small dedicated module:

- `resources/js/pages/welcome-hero/readiness.js`

`resources/js/pages/welcome-hero.js` imports the helper rather than absorbing the readiness implementation. This keeps Hero source bounded and avoids crossing the repository source-size guard.

A defensive fallback exists so a broken/slow Hero media event cannot permanently block the rest of the homepage.

### Program level readiness

Program is not considered prepared merely because its JS module import completed.

The Program preparation contract waits until the Program journey is genuinely ready, including:

- GSAP loaded and mounted; or
- the reduced/failure fallback mounted.

This prevents hidden parallel network work where Values starts while Program is still fetching GSAP from jsDelivr.

Relevant owners:

- `resources/js/pages/welcome/program-cards.js`
- `resources/js/surfaces/home/program-journey/controller.js`

### Relevant commits

- `3c6a6d98e7d8e2f15a9dcf741e4c51d73c2287d7` — `Prioritize homepage startup after hero readiness`
- `3c1c30c271ecfbf954493dc98108eec2630e2471` — `Keep hero readiness modular`

---

## 5. Current startup ownership graph

Initial synchronous homepage JS entry remains:

- `resources/js/pages/welcome.js`
- `resources/js/pages/welcome-hero.js`
- `resources/js/pages/welcome-editorial-headings.js`

`welcome.js` owns lightweight page utilities and schedules the sequential preparation graph.

The heavy section controllers are dynamically imported by their preparation level rather than synchronously owned by the primary Homepage entry.

Do not revert the pipeline back to a proximity-only/IntersectionObserver model. Proximity may still be useful inside a mounted section for expensive subfeatures, but it is not the trigger for advancing the homepage preparation levels.

---

## 6. Known remaining performance GAP — CSS critical path

This session intentionally did **not** perform a broad CSS ownership/cascade rewrite.

`resources/views/welcome.blade.php` still includes multiple Homepage CSS entries in the initial `@vite([...])` list, including section CSS below Hero such as:

- `resources/css/pages/welcome-vision-waapi.css`
- `resources/css/pages/welcome-values-story.css`
- `resources/css/pages/welcome-depth-gallery.css`
- editorial CSS entries

Therefore JS startup contention and About preview decode have been reduced, but the initial CSS critical path still deserves measurement.

### Next recommended performance packet

Priority: **P1 after current browser validation**

1. Measure first-load network and main-thread behavior before changing CSS ownership.
2. Identify which Homepage CSS is truly required for the first Hero viewport.
3. Preserve no-JS/fail-open readability for lower sections.
4. Only then consider splitting lower-section CSS into dynamic section-owned chunks.
5. Do not blindly delete or reorder `welcome.css` cascade modules; the existing CSS ownership inventory already marks that aggregate as high cascade risk.

Proof should compare at least:

- transferred JS/CSS before Hero `playing`;
- number of requests before Hero readiness;
- Hero start time / LCP;
- long tasks during first presentation;
- Safari/WebKit and Chromium behavior;
- normal and throttled network.

---

## 7. Media / Cloudflare diagnostic rule

Do not conclude "Cloudflare is slow" solely because the page feels slow.

For media delivery, distinguish:

1. server/edge delivery;
2. HTTP Range behavior;
3. browser buffering;
4. video decode;
5. competing JS/CSS/main-thread work.

Useful live proof for future investigation:

```bash
curl -I https://media.almustaqbal.sch.id/<object>
curl -I -H 'Range: bytes=0-1048575' https://media.almustaqbal.sch.id/<object>
```

Browser Network proof should inspect:

- status `200` vs `206`;
- `Accept-Ranges`;
- transferred bytes;
- TTFB;
- request start relative to Hero readiness;
- Cloudflare cache headers when present.

R2/custom-domain presence proves media ownership, not that every browser workload around the media is efficient.

---

## 8. Validation state

Source/commit/diff validation for the changes above: **PASS on `main`**.

The owner subsequently reported the local step as completed (`ok sudah`), but the exact final terminal output for the latest combined test/build run is not archived in this handoff. Treat detailed test counts as unrecorded rather than inventing them.

Recommended quick re-proof before the next structural performance change:

```bash
git pull --ff-only origin main &&
php artisan test \
  tests/Feature/HomeGraphicsLoadingGraphTest.php \
  tests/Feature/HomeAboutVideoTest.php \
  tests/Feature/HomeHeroInteractionTest.php \
  tests/Feature/HomeValuesStoryTest.php &&
npm run build
```

Browser acceptance should include:

- Hero begins without lower-level startup contention;
- background preparation starts automatically after Hero readiness even if the user does not scroll;
- preparation order progresses Vision → Program → Values → Gallery;
- About thumbnail causes no video request before click;
- About modal starts full video only on intent;
- Program is ready before Values preparation advances;
- Values responsive fast-scroll remains deterministic;
- Values mobile/tablet flip changes perspective side after edge-on;
- desktop Values choreography does not regress.

---

## 9. Commit trail for this handoff

Ordered relevant commits:

1. `8ddff7b06aade9dd915a3d1600591284af9d83ba` — `Make values cards deterministic on responsive scroll`
2. `bcbaa0c46e168dc8ed9e3e413806d371c97e070f` — `Scope values aria contract to its section`
3. `252d275fc34ed23b3ba07bd170e8c094d055fd19` — `Align responsive values flip direction`
4. `f4448553a5c2b4d0a2de0dc203b9ba9c1a5c95a4` — `Swap responsive values tilt side through flip`
5. `5917d866ed977ead172bf374e40c49006de40bd9` — `Align values tilt test with flip side swap`
6. `6d8f94fca6d17a30a28435480b41fb0f4d15f80b` — `Stream homepage videos on demand`
7. `af85b2e78154ed282455061e00b71317a684e8bf` — `Fix literal hero preload assertion`
8. `3c6a6d98e7d8e2f15a9dcf741e4c51d73c2287d7` — `Prioritize homepage startup after hero readiness`
9. `3c1c30c271ecfbf954493dc98108eec2630e2471` — `Keep hero readiness modular`

---

## 10. Next-session starting point

Start from current `main` and first verify the priority pipeline in browser/network tooling.

If the Hero still feels disproportionately slow after the JS/media fixes, investigate the **initial CSS critical path** next. Do not reopen Pondasi motion geometry or About autoplay-preview experiments unless browser proof shows a regression in those exact contracts.
