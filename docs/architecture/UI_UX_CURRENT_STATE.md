# UI/UX Engineering — Current State and Progress Ledger

Status: `HARDENING_ACTIVE / HOMEPAGE-ENGINE-2026-08-23`
Updated: 2026-08-23
Repository: `Asyraf2003/schoolai`
Target branch: `main`
Inspected runtime-source HEAD: `04e3aa68dc06b0bb2777678538d4e65b01c656ee`
Durable handoff: `handoffs/2026-08-23-homepage-hardening.md`

## Current phase

Homepage visual polishing is PAUSED. The project is now in a bounded hardening
phase. The next sessions must stabilize engine/state/lifecycle behavior before
continuing Article composition, Gallery-to-Article art direction, Footer
transition work, or removing temporary visual rulers/markers.

The receiving agent must read the mandatory chain in `README.md`, then the
hardening handoff above. Chat history is not an implementation dependency.

## Current FACT

- Slow desktop scrolling through the closing Gallery -> Article handoff can
  expose the dark `.nav-shell` background, switch Gallery into its static
  fallback, and leave the downstream state visually broken. Faster scrolling
  can sometimes pass the same region. This is a reproduced runtime symptom,
  not a claimed final root cause.
- `renderDepthFrame()` currently calls `isDepthFrameHealthy()`. A frame is
  considered unhealthy when no Gallery plane has opacity above `.01` and the
  end CTA is not considered visible, even when the drawing buffer and WebGL
  context remain otherwise healthy.
- A false result from `renderDepthFrame()` stops `DepthGalleryEngine` and calls
  its failure path. `applyFallbackState()` then removes active/ready classes and
  exposes the static fallback. This is the primary hardening candidate for the
  slow-scroll failure.
- The dark color visible during the failure matches the homepage shell
  background `#071b18`; it becomes visible when Gallery/Article surfaces no
  longer cover the viewport during the failed state.
- Gallery uses an internal smoothed camera clock. The handoff has also been
  adapted to native document progress. These clocks must not be allowed to
  produce contradictory lifecycle states.
- `npm run build` has been observed to complete while warning about a chunk over
  `500 kB`. That warning is not currently proven to cause the slow-scroll
  fallback bug.
- Three.js ownership is inconsistent: Gallery runtime-loads `three@0.183.0`
  from jsDelivr while the package graph currently declares `three ^0.185.1` and
  other scenes import from the package. Unification is a later hardening step,
  not the active fix.
- Article visual work remains intentionally unfinished. Temporary X/Y viewport
  rulers and `tes...` markers remain useful for later owner feedback and must
  not be removed during the engine step unless they themselves are proven to
  cause a runtime defect.

## Owner-accepted durable direction

- Hardening comes before further visual polishing.
- Work proceeds slowly, one bounded capability per session, with proof before
  the next capability.
- Blade should ultimately become presentation-only: no inline `@php`, locale
  `match`, collection shaping, business/data preparation, or other view logic.
  Prepared view data belongs in controllers/application services/view models or
  equivalent existing Laravel owners.
- Media CRUD/index/display should ultimately use Cloudflare-backed delivery.
  R2 is the expected binary/object-storage direction through the S3-compatible
  filesystem path; database records remain the metadata/relationship source of
  truth. Video delivery may be evaluated separately when evidence requires it.
- Final product support remains six responsive width tiers, ID/EN/AR,
  LTR/RTL, Chromium and Safari/WebKit. This is a 36-combination certification
  matrix, not permission to create 36 implementations.
- Product target remains Lighthouse/PageSpeed `100/100/100/100` on declared
  lab profiles, while field CWV claims require real p75 field evidence.
- Media must reserve geometry and be responsive; storage migration must not
  sacrifice accessibility, locale behavior, WebKit support, or page-speed
  budgets.

## Hardening sequence

The sequence below is a backlog. Only the first unresolved item may become the
active step unless new evidence changes priority.

1. `H1 Gallery false-fallback hardening`
   - separate a genuinely unhealthy renderer/context from a temporarily empty
     visual frame during transition;
   - prove slow forward/reverse scrolling no longer enters static fallback.
2. `H2 Gallery lifecycle/state hardening`
   - make loading/active/ending/handoff/fallback semantics explicit and prevent
     contradictory class/state transitions.
3. `H3 Scroll-clock reconciliation`
   - keep smoothing for visual motion where useful, but make section lifecycle
     and handoff progress deterministic from one authoritative clock.
4. `H4 Graphics runtime/bundle hardening`
   - converge on one Three.js version/runtime strategy; then measure/code-split
     graphics without hiding bundle warnings by merely increasing limits.
5. `H5 Blade presentation-purity migration`
   - move inline data shaping and locale decisions out of Blade incrementally,
     one surface at a time, with unchanged rendered semantics as proof.
6. `H6 Cloudflare media migration`
   - define R2 disk/config, object-key and metadata contracts, upload/delete
     lifecycle, CDN/public URL strategy, image variants, cache policy, and CRUD
     migration in bounded steps.
7. `H7 Release certification`
   - certify six tiers x three locales x Chromium/WebKit, then Lighthouse/
     PageSpeed and accessibility/runtime gates.

## Protected / deferred during H1

- Do not redesign Gallery, Article, Program, Values, Vision/Mission, navigation,
  or Footer.
- Do not tune Article ruler coordinates during H1.
- Do not migrate media storage during H1.
- Do not remove Blade `@php` during H1.
- Do not change Three.js dependency/version during H1.
- Do not suppress the >500 kB warning by changing only the warning threshold.
- Do not claim Safari/WebKit, responsive, locale, Lighthouse, PageSpeed, CWV,
  or accessibility PASS without actually running the corresponding proof.

## Current proof status

- Owner screenshots: `FAIL` for slow-scroll Gallery -> Article stability.
- Fast-scroll behavior: sometimes visually passes; this does not qualify as
  deterministic proof.
- `npm run build`: observed PASS in owner terminal, with >500 kB chunk warning.
- No new Safari/WebKit, six-tier, three-locale, Lighthouse/PageSpeed, field CWV,
  or full automated release proof is claimed for this phase.
- The docs-only hardening checkpoint does not increase runtime progress.

## NEXT VALID STEP

`H1 Gallery false-fallback hardening` only.

Inspect and patch the Gallery frame-health/fallback boundary so a legitimate
transition frame with no currently visible plane cannot be mistaken for a WebGL
engine failure. Preserve current visual composition. Prove by repeated slow
forward and reverse scrolling through the Gallery ending/handoff in Chromium,
including a deliberate pause inside the transition region, with no static
fallback spawn, no exposed dark shell, and no console/runtime engine failure.
