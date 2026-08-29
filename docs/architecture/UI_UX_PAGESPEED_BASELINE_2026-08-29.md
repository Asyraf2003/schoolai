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

### P0-C — Hero/local media payload is far above budget

Repository-confirmed local files:

- `public/media/hero/activity.jpg`: 2,636,109 bytes;
- `public/media/hero/library.jpg`: 2,090,143 bytes;
- `public/media/hero/teaching.jpg`: 1,311,473 bytes;
- `public/media/hero/mainvideo.mp4`: 19,602,803 bytes.

Desktop PSI downloaded the three JPEGs at approximately 2.58 MiB, 2.04 MiB and
1.28 MiB respectively. Total desktop payload reached about 7.18 MiB.

The Blade markup correctly leaves non-first image slides as `data-src`, but
`resources/js/pages/welcome-hero/slider-playback.js` explicitly hydrates the
next non-video slide during `showSlide()`. This confirms at least one hidden
hero image is fetched before it is needed.

Decision:

- create right-sized modern hero variants rather than shipping multi-megabyte
  JPEG originals to normal viewports;
- use responsive source selection where appropriate;
- keep initial media/poster quality high but bounded;
- remove automatic next-slide image hydration from the initial critical window;
  hydrate on safe idle proximity, autoplay proximity or user intent instead;
- keep non-active video sources unhydrated until required;
- do not infer from current source alone that all three desktop JPEG requests
  come from the same hydration function. That requires a network trace.

### P0-D — Decorative repeated textures are oversized

The two repeated Islamic geometry textures are repository-confirmed at:

- `gallery-ornament-32.webp`: 384,108 bytes;
- `gallery-ornament-33.webp`: 219,100 bytes.

They are used as repeating CSS patterns in vision/mission and gallery surfaces,
with tile sizes around 22–40 rem. PSI flags these among the largest first-party
resources and estimates meaningful image-delivery savings.

Decision:

- generate much smaller texture-source dimensions/quality for repetition;
- evaluate AVIF/WebP based on actual decode/visual result;
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
- set one-year immutable caching on fixed-name mutable media;
- enable HSTS preload before subdomain readiness is proven;
- treat a single Lighthouse score fluctuation as proof.

## 8. Execution order

1. Self-host Inter and remove Google Fonts from the public ID/EN critical path.
2. Re-run comparable PageSpeed/Lighthouse and record delta.
3. Restructure critical vs deferred home CSS entries.
4. Compress/right-size hero and decorative media; stop premature hero hydration.
5. Apply safe cache policy for hashed/versioned assets.
6. Evaluate delayed analytics loading.
7. Resolve media-origin video failure.
8. Reduce DOM/style/rendering cost using trace evidence.
9. Repair accessibility/agentic semantics and touch targets.
10. Handle security hardening as its own verified batch.

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
