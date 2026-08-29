# UI/UX PageSpeed Baseline — 2026-08-29

Status: ACTIVE BASELINE
Captured: 2026-08-29 11:48 GMT+8
Target URL: `https://almustaqbal.sch.id/`
Tool: PageSpeed Insights / Lighthouse 13.4.1

## 1. Evidence boundary

This document records the production PageSpeed reports supplied by the owner on
2026-08-29 and correlates them with inspected repository source.

The PageSpeed capture predates the latest cursor/about-player commits by roughly
10 minutes. The inspected repository HEAD after those commits was
`c846daa01ccc428d015ab868ef2d187cb717f8d4`. Therefore:

- the PSI numbers are valid production baseline evidence for the 11:48 capture;
- the repository findings are valid source findings on near-current `main`;
- do not claim the PSI report measured `c846daa` exactly;
- every optimization batch requires a new comparable production run.

No CrUX/field data was available in the supplied reports. Lab evidence does not
prove field CWV `3/3`.

Repository file presence is not runtime proof. In particular, old JPG/PNG files
may remain as legacy/source artifacts. A performance decision may call a media
file "runtime" only when a current Blade/config/DB path or production network
trace proves that it is delivered.

## 2. Baseline scores and metrics

| Metric | Mobile | Desktop |
|---|---:|---:|
| Performance | 79 | 77 |
| Accessibility | 97 | 97 |
| Best Practices | 96 | 96 |
| SEO | 100 | 100 |
| Agentic Browsing | 1/2 | 1/2 |
| FCP | 2.7 s | 0.7 s |
| LCP | 4.5 s | 3.7 s |
| TBT | 40 ms | 50 ms |
| CLS | 0 | 0 |
| Speed Index | 3.7 s | 1.7 s |
| Total payload | ~1.33 MiB | ~7.18 MiB |

The LCP element in both profiles is the server-rendered hero heading:
`Begin a Meaningful Learning Journey` (`.hero-cinema__title`).

LCP breakdown:

- mobile element render delay: about 1.86 s;
- desktop element render delay: about 2.25 s;
- observed server response is already fast, around 4–5 ms in the report.

Conclusion: backend TTFB is not the current LCP bottleneck. The critical path
before the hero text can paint is the first priority.

## 3. Confirmed high-impact findings

### P0-A — Too many render-blocking stylesheet entries

`resources/views/welcome.blade.php` loads many independent Vite CSS entries in
the document head, including hero, vision, values, gallery, article, text and
locale typography styles. `partials/site-head-meta.blade.php` adds more home
entries for hero visual and scroll reveal.

`vite.config.js` also declares these files as independent Vite inputs. The
production result is many separate hashed stylesheets competing on the initial
critical path.

PSI reports about 53–54 KiB of first-party render-blocking CSS. Mobile estimates
about 1.21 s potential render-blocking savings. The general `welcome` bundle is
about 20 KiB and PSI estimates about 17–18 KiB unused during the initial view.

Decision:

- do not solve this by merging every stylesheet into one giant blocking file;
- define a small initial public shell/hero critical set;
- defer below-fold vision, values, gallery, testimonials/articles and other
  non-critical presentation CSS where runtime safety permits;
- preserve source modularity and import order contracts.

Important repository constraint: `scripts/verify-source-structure.mjs` enforces
source ownership and `docs/architecture/source-module-equivalence.json` locks
ordered CSS module equivalence/checksums for several legacy entries such as
`welcome.css` and `welcome-hero.css`. A performance refactor must respect or
explicitly update those contracts. Randomly concatenating/deleting CSS is not a
valid migration strategy.

### P0-B — External Inter is on the text-LCP dependency path

For ID/EN, `partials/site-head-meta.blade.php` loads Google Fonts Inter in the
head. `resources/css/public-latin-inter.css` only assigns `Inter`; it does not
self-host it.

The PSI dependency tree records the Google Fonts stylesheet followed by a
roughly 48 KiB Inter WOFF2 request. This is an external dependency directly
relevant to a text LCP.

Decision:

- self-host the required Inter variable font/weights on the first-party origin;
- remove the Google Fonts stylesheet/preconnect dependency after parity proof;
- only preload a font resource if measurement proves it remains critical;
- keep Cairo/Arabic ownership separate.

This is the safest first optimization batch because it is isolated, measurable,
and directly intersects the LCP text path.

### P0-C — Hero payload is high, but runtime ownership must be proven first

The repository contains old local files under `public/media/hero/`, including
multi-megabyte JPG files and an MP4. Their presence alone does **not** prove the
current homepage renders or downloads them.

Current source evidence instead shows that the opening hero is injected from
`config('media.homepage_hero_video_url')` with poster/fallback handling, while
promoted article hero slides use article `thumbnail_url` values from the
database. Source search does not currently prove direct runtime references to
`activity.jpg`, `library.jpg`, or `teaching.jpg`.

The desktop PSI report still records a much larger total payload than mobile,
about 7.18 MiB, so hero/media transfer remains a real investigation target. The
next valid evidence is the production network request list mapped back to
current config/DB/source ownership.

The Blade markup correctly leaves non-first image slides as `data-src`, but
`resources/js/pages/welcome-hero/slider-playback.js` explicitly hydrates the
next non-video slide during `showSlide()`. This confirms at least one hidden
hero image can be fetched before it is visible, but it does not prove which
specific production asset it is.

Decision:

- do not convert, delete, or optimize legacy JPG/PNG merely because files exist;
- first identify the exact production hero/poster/article requests and owners;
- keep non-active media unhydrated until justified by idle proximity, autoplay
  proximity, or user intent;
- keep non-active video sources unhydrated until required;
- only right-size/encode assets that are proven runtime resources;
- preserve current visual quality while bounding payload per viewport.

### Runtime media-format contract

Owner decision for public runtime media:

- photographic/raster media: WebP by default;
- video media: MP4 using the existing conditioned/optimized delivery path;
- SVG: appropriate for logos, icons, marks, ornaments, or other genuinely
  vector artwork;
- JPG/PNG may exist as legacy/source artifacts, but must not silently become
  public runtime media without an explicit reason and evidence.

Performance work must verify the delivered URL/format rather than infer it from
repository extensions.

### P0-D — Decorative repeated textures are oversized

The two repeated Islamic geometry textures are repository-confirmed at:

- `gallery-ornament-32.webp`: 384,108 bytes;
- `gallery-ornament-33.webp`: 219,100 bytes.

They are used as repeating CSS patterns in vision/mission and gallery surfaces,
with tile sizes around 22–40 rem. PSI flags these among the largest first-party
resources and estimates meaningful image-delivery savings.

Decision:

- generate much smaller texture-source dimensions/quality for repetition only
  if visual/runtime proof confirms the current files are materially costly;
- keep WebP unless measured format/decode evidence justifies another choice;
- do not use hundreds of KiB for a repeating decorative tile unless visual
  comparison proves the cost necessary.

### P0-E — Static cache lifetime is only about four hours

PSI reports a roughly 4-hour browser cache TTL for Vite build assets and most
first-party media. `public/.htaccess` currently contains rewrite rules but no
static asset cache policy.

Decision:

- hashed Vite files under `/build/assets/` should use a long immutable browser
  cache, normally `public, max-age=31536000, immutable`;
- UUID/content-versioned media can also use long cache lifetimes;
- fixed-name media must not receive immutable one-year caching until a safe
  versioning/invalidation strategy exists;
- Cloudflare/browser cache configuration must be checked together with origin
  headers. Do not assume `.htaccess` alone owns the observed 4-hour TTL.

## 4. Confirmed secondary findings

### P1-A — Google Analytics is the dominant JavaScript cost

The application loads `gtag.js` asynchronously in the document head when a GA
ID is configured. PSI reports roughly 166 KiB transfer, about 68–70 KiB unused
JavaScript, and multiple 68–97 ms long tasks.

By contrast, first-party JS is small and overall TBT is only 40–50 ms.

Decision:

- do not start this optimization project by rewriting first-party JavaScript;
- preserve analytics requirements but evaluate delayed/idle/consent-aware GA
  loading after critical rendering;
- verify page-view semantics before accepting a delay strategy.

### P1-B — Hero media effect has measurable rendering risk

The hero media CSS uses large `filter: blur(12px)`, transform scaling,
`will-change: transform, filter`, and long transitions. Navbar/other public
surfaces also use backdrop filters and decorative blending.

PSI does not prove these rules alone cause current LCP, so they are not to be
removed blindly. They are trace candidates because mobile reports substantial
Style & Layout and Rendering time.

Decision: profile paint/compositor cost after the critical-path/network fixes.

### P1-C — Production hero video request fails in Lighthouse

Best Practices reports `net::ERR_CONNECTION_FAILED` for a hero MP4 under
`media.almustaqbal.sch.id`.

Decision:

- treat this as an independent production media-origin/R2/CDN reliability issue;
- keep poster/static fallback working;
- do not make the initial page depend on successful hero video transport;
- verify the exact media URL and response separately before changing code.

## 5. Structural/quality findings

### P2-A — DOM is large

PSI reports 1,695 elements, maximum depth 16 and 20 children in the program
kinetic type element.

`featured-programs.blade.php` renders decorative kinetic text, 11 handoff spans,
program cards, and a second detail DOM tree containing duplicated program media.

Decision:

- optimize DOM after LCP/network P0 work;
- investigate rendering detail content on demand or reducing duplicate markup;
- reduce decorative DOM only while preserving the accepted interaction.

### P2-B — Accessibility tree has invalid listitem roles

`school-values.blade.php` and `gallery-depth.blade.php` put `role="listitem"` on
`<article>` elements. Lighthouse flags this as incompatible. It also causes the
Agentic Browsing accessibility-tree failure.

Preferred direction: use native list semantics such as `<ul>/<li>` with an
article inside when list semantics are required, or remove the redundant ARIA
roles when article semantics are sufficient.

### P2-C — Hero campaign links fail touch-target sizing

The hero eyebrow campaign link and linked hero title are reported below the
required touch target size/spacing. Fix must preserve current typography while
expanding the interactive hit area without causing layout shift.

### P2-D — Non-composited nav color transition

Lighthouse flags the nav-link `color` transition as non-composited. It is a
small issue compared with LCP/media and should not displace P0 work.

## 6. Security/Best Practices findings kept separate from performance

Current middleware already uses CSP nonces, X-Frame-Options, referrer policy,
permissions policy and production HSTS. Lighthouse still reports:

- CSP host-allowlist hardening opportunity / `strict-dynamic` direction;
- no COOP header;
- no Trusted Types requirement;
- HSTS lacks `includeSubDomains` and `preload`.

Do not add HSTS preload or `includeSubDomains` merely to improve a Lighthouse
number. The media subdomain currently has a reported connection failure, so all
required HTTPS subdomains must be proven safe first.

Likewise, `strict-dynamic`/Trusted Types changes need compatibility and GTM/runtime
proof. Security score work is a separate controlled batch.

## 7. What not to optimize first

Do NOT:

- chase first-party JS micro-optimizations while TBT is already 40–50 ms;
- merge all CSS into one render-blocking mega-file;
- remove cinematic effects without a trace proving they are the bottleneck;
- use `preload` for many below-fold assets;
- preload hidden carousel media merely to make transitions instant;
- convert/delete JPG/PNG only because they exist in the repository;
- set one-year immutable caching on fixed-name mutable media;
- enable HSTS preload before subdomain readiness is proven;
- treat a single Lighthouse score fluctuation as proof.

## 8. Execution order

1. Self-host Inter and remove Google Fonts from the public ID/EN critical path.
2. Re-run comparable PageSpeed/Lighthouse and record delta.
3. Restructure critical vs deferred home CSS entries.
4. Capture exact production media requests and map each to config/DB/source.
5. Fix only proven runtime media payload/hydration problems.
6. Apply safe cache policy for hashed/versioned assets.
7. Evaluate delayed analytics loading.
8. Resolve media-origin video failure.
9. Reduce DOM/style/rendering cost using trace evidence.
10. Repair accessibility/agentic semantics and touch targets.
11. Handle security hardening as its own verified batch.

Only one numbered batch should be changed between comparable measurements when
practical, so attribution remains credible.

## 9. Proof gate after every performance batch

Use the same production URL/profile and record at least three comparable cold
runs where possible. Record median and worst, not only the best run.

Minimum proof:

- build passes;
- relevant feature tests pass;
- initial visual/interaction parity checked at mobile and desktop;
- PageSpeed raw metrics recorded, especially FCP, LCP, TBT, CLS and payload;
- render-blocking request count/bytes compared;
- new console/network errors checked;
- no regression in ID/EN/AR or WebKit-facing semantics introduced.

Current product target remains Lighthouse/PageSpeed `100/100/100/100`. The
2026-08-29 report is a baseline, not an accepted final score.

## 10. Post-optimization checkpoint — 2026-08-29 15:13 GMT+8

Production was measured again after commit `404cd455`
(`perf: harden homepage critical path`).

### Scores

| Metric | Original baseline | Post-optimization |
|---|---:|---:|
| Performance | 79 | 91 |
| Accessibility | 97 | 100 |
| Best Practices | 96 | 96 |
| SEO | 100 | 100 |
| Agentic Browsing | 1/2 | 2/2 |
| FCP | 2.7 s | 1.8 s |
| LCP | 4.5 s | 3.0 s |
| TBT | 40 ms | 0 ms |
| CLS | 0 | 0 |
| Speed Index | 3.7 s | 3.8 s |

This confirms the critical-path batch produced a material mobile improvement
without introducing layout shift or main-thread blocking.

### Completed in this batch

- Inter is self-hosted and Google Fonts were removed from the public critical
  path.
- Google Analytics / `gtag.js` was removed from the application.
- Cloudflare Web Analytics is now the analytics owner.
- obsolete Google Analytics and Google Fonts CSP hosts were removed.
- `cdn.jsdelivr.net` remains intentionally allowed because the Program journey
  still lazy-loads GSAP from that origin.
- hashed Vite assets receive
  `Cache-Control: public, max-age=31536000, immutable`.
- seven clearly below-fold home CSS entries are deferred after initial paint.
- invalid accessibility list roles were removed.
- hero campaign touch targets were enlarged.
- non-composited nav-link color transition was removed.
- Agentic Browsing improved from 1/2 to 2/2.
- Accessibility improved from 97 to 100.

### Production proof

Hashed Vite CSS returned:

- HTTP 200;
- one-year immutable browser cache;
- Cloudflare `HIT`.

Production CSP after deployment no longer contains Google Analytics or Google
Fonts hosts. It allows:

- `https://static.cloudflareinsights.com/beacon.min.js`;
- `https://cdn.jsdelivr.net` for the current GSAP runtime.

### Correctness regression found after media cleanup

The following files were incorrectly classified as unused during the media
cleanup and produced production 404 responses:

- `public/media/home/vision-paper-01.webp`
- `public/media/home/vision-paper-02.webp`
- `public/media/home/vision-paper-03.webp`

They are runtime assets of the Vision/Mission presentation and were restored
from the local quarantine on 2026-08-29.

Do not delete them again based only on repository-reference heuristics.
Production network/runtime ownership is authoritative.

## 11. Handoff debt for next performance session

The following work is intentionally deferred to a subsequent session. Do not
re-open already-completed batches unless a new measurement proves regression.

### P0 — Hero video UX and transport

Current opening hero video:

- URL:
  `https://media.almustaqbal.sch.id/hero/slides/main/5d918256-de1e-4eae-ad27-3d031acc202d.mp4`
- codec: H.264;
- resolution: 1920x1080;
- frame rate: 30 fps;
- duration: approximately 363.47 seconds;
- size: 93,956,351 bytes (~89.6 MiB);
- video bitrate: approximately 1.93 Mbps;
- AAC audio bitrate: approximately 129 kbps;
- total bitrate: approximately 2.07 Mbps.

The first hero `<video>` currently renders with `preload="metadata"` while its
source URL is present immediately. Active-slide hydration later promotes the
video preload to `auto`.

Hero readiness currently waits for video `playing` or `error`, with a 5 second
fallback signal.

User-visible symptom: the opening hero can feel lazy/dark before the video is
visually ready. The owner explicitly dislikes this behavior.

Do NOT solve this merely by changing the existing ~90 MiB video to unconditional
`preload="auto"`.

Recommended next-session direction:

1. create a dedicated short/conditioned hero delivery asset rather than using
   the full six-minute source as an eager homepage asset;
2. retain the full-quality/full-duration source separately if required;
3. provide an immediate first-party poster so the hero never presents an ugly
   dark/unloaded state;
4. evaluate replacing the current Unsplash poster with an owned/R2 hero poster;
5. only then decide whether the conditioned hero asset can safely use eager or
   `preload="auto"` behavior;
6. verify mobile Safari/Chromium autoplay behavior;
7. re-run Lighthouse after the transport change.

Current Lighthouse still logs
`net::ERR_CONNECTION_FAILED` for the hero MP4 under Slow 4G despite direct HTTP
checks returning 200. Treat this as a payload/delivery-path issue until proven
otherwise.

### P0/P1 — Hero LCP render delay

The new LCP is the hero title:

`Begin a Meaningful Learning Journey`

New LCP breakdown:

- TTFB: ~10 ms;
- element render delay: ~2.18 s;
- total LCP: 3.0 s.

Backend response is therefore not the current bottleneck.

Investigate hero opening/readiness/opacity/transform choreography. The first SSR
hero title should ideally be paintable immediately while preserving cinematic
transitions for subsequent slide changes.

Do not remove hero effects blindly. Trace exact visibility/render gating first.

### P1 — Remaining general `welcome.css`

Lighthouse reports:

- `welcome-DLGuNsCf.css`: about 20.9 KiB transfer;
- estimated unused CSS saving: about 17.2 KiB.

This is now the dominant remaining first-party CSS opportunity.

Do not randomly split the 48-module legacy cascade. Respect:

- `scripts/verify-source-structure.mjs`;
- `docs/architecture/source-module-equivalence.json`;
- established module ordering and checksum contracts.

A future refactor should identify the genuinely above-fold shell/nav/base
modules and preserve source-equivalence guarantees.

Current render-blocking opportunity is only about 190 ms, so this should not
displace the hero render-delay work.

### P1 — Remaining external hero poster

Lighthouse currently sees an Unsplash hero poster of approximately 215 KiB and
estimates roughly 33.5 KiB image-delivery savings.

Prefer an owned first-party/R2 poster if visual parity is acceptable. Avoid
adding speculative preconnects; Lighthouse currently reports no useful
preconnect candidate.

### P1/P2 — Fixed-name media cache lifetime

Lighthouse still reports approximately four-hour cache lifetime for fixed-name
media, especially:

- `gallery-ornament-32.webp` (~376 KiB transfer);
- `gallery-ornament-33.webp` (~215 KiB transfer);
- `logo-nav.webp`.

Do not apply one-year immutable caching to mutable fixed-name media without a
versioning/invalidation contract.

A future session may move stable decorative assets to content-hashed/versioned
URLs before assigning long immutable cache lifetime.

### P2 — Decorative ornaments

The same two geometry ornaments remain relatively large.

Earlier audit showed their rendered/tile dimensions make the source dimensions
plausible, so do not recompress them merely because Lighthouse lists them.

Only optimize after visual comparison and actual transfer/cache evidence.

### P2 — DOM size

Latest Lighthouse:

- total elements: 1,727;
- maximum depth: 16;
- most children: 20 in `.program-kinetic__type`.

TBT is now 0 ms, so DOM reduction is not a P0 regression blocker. Investigate
only after LCP/media work.

### P2 — Cloudflare utility requests

Production now includes:

- Cloudflare Web Analytics beacon (~10 KiB);
- `/cdn-cgi/rum`;
- Cloudflare email decode utility.

Web Analytics is intentional.

Review Cloudflare Email Address Obfuscation separately. Disable it only if the
site does not require the protection and after verifying rendered contact
addresses.

### Security debt kept separate

Still intentionally deferred:

- COOP;
- Trusted Types;
- CSP `strict-dynamic`;
- HSTS `includeSubDomains`;
- HSTS preload.

Do not enable these for Lighthouse score alone. Validate OAuth/popup behavior,
all HTTPS subdomains, embedded third parties and admin/public compatibility
first.

## 12. Next-session starting point

Start from production commit `404cd455` plus the Vision/Mission runtime asset
restore commit that follows this checkpoint.

Do not repeat the completed Inter/media-cleanup/GA/accessibility work.

Priority order:

1. prove Vision/Mission restored assets return HTTP 200;
2. condition the six-minute hero video into an appropriate homepage delivery
   asset and remove the ugly unloaded state;
3. reduce hero-title LCP render delay;
4. re-run comparable mobile PageSpeed;
5. only then consider the remaining `welcome.css`, fixed-media caching,
   ornaments and DOM work.

Latest accepted mobile reference before the Vision/Mission restore:

- Performance 91;
- Accessibility 100;
- Best Practices 96;
- SEO 100;
- Agentic Browsing 2/2;
- FCP 1.8 s;
- LCP 3.0 s;
- TBT 0 ms;
- CLS 0;
- Speed Index 3.8 s.

