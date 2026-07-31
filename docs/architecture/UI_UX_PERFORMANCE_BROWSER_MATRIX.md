# UI/UX Performance and Browser Matrix

Status: ACTIVE
Updated: 2026-07-31

## 1. Target interpretation

Lighthouse/PageSpeed lab target: Performance 100, Accessibility 100, Best
Practices 100, and SEO 100. Field target: good LCP, INP, and CLS.

Lab and field are separate evidence. Field `3/3` requires sufficient p75
RUM/CrUX data:

- LCP <= 2.5 seconds;
- INP <= 200 milliseconds;
- CLS <= 0.1.

An agent may prove lab 100s but must not infer field `3/3`.

## 2. Required profiles

Record exact browser/OS/hardware/input/capability tier.

| Profile | Minimum purpose |
|---|---|
| Chromium mobile lab | Lighthouse, 360/390, throttled load/input |
| Chromium tablet | touch/orientation and MD/LG transition |
| Chromium desktop | XL/2XL, keyboard, trace, graphics |
| WebKit mobile | 360/390, touch, viewport/media/canvas |
| WebKit tablet | MD/LG, orientation, memory/lifecycle |
| Safari desktop | XL/2XL, keyboard, media/graphics |

Use `UI_UX_RESPONSIVE_LOCALE_MATRIX.md` for the full six-tier/locale proof.
Brave may prove Chromium behavior but must be named; shields/extensions cannot
be mistaken for application behavior.

## 3. Baseline before feature budgets

Resolve `BASELINE-GAP-001` before selecting the first WebGL engine/frame.

Record:

- HTML transfer and TTFB;
- critical/total CSS;
- initial/deferred JS and chunk graph;
- font scripts/weights;
- LCP resource/element;
- below-fold image/video;
- long tasks, TBT, INP proxy, and CLS sources;
- main/compositor/rendering time;
- canvas/renderer CPU/GPU memory where measurable;
- PageSpeed/Lighthouse repeatability.

Assign each feature a delta budget against this baseline. Updating the baseline
requires evidence; do not quietly relax a failed budget.

## 4. Critical path

- Primary copy, navigation, locale control, and CTA are server-rendered.
- The true LCP media is discoverable in initial HTML and not lazy-loaded.
- Reserve image/video/canvas dimensions.
- Preload only measured critical resources.
- Load only required script subsets/weights; avoid invisible critical text.
- Defer non-critical section code/media by relevance.
- Hidden slides/frames do not preload full media/models.
- Renderer, models, textures, shaders, and decoders are not critical-path
  resources by default.
- Third-party embeds use a local cover/intent path when product permits.

## 5. Capability tiers

| Tier | Contract |
|---|---|
| 0 semantic static | HTML, controls, poster, no spatial/continuous renderer |
| 1 lightweight motion | CSS transform/opacity and bounded JS orchestration |
| 2 efficient graphics | lazy WebGL, lower LOD/effects, bounded DPR/update |
| 3 high fidelity | richer assets/effects only on proven target profiles |

Selection uses combined capabilities and measured results, not one user-agent,
width, DPR, memory, core, connection, or battery hint.

Every downgrade preserves content meaning and controls.

## 6. PageSpeed strategy with WebGL

The initial PageSpeed path must complete semantic/LCP work without downloading
or executing inactive WebGL.

After activation, WebGL still owns real-user INP, long-task, memory, and
lifecycle impact. “Deferred” does not mean “free.”

Before accepting a frame:

1. compare baseline and feature runs under the same profile;
2. attribute transfer/chunks, long tasks, CLS, and LCP change;
3. prove no offscreen/hidden loop;
4. prove quality downgrade;
5. prove cleanup after locale/page/frame exit;
6. reduce fidelity or redesign when the gate fails.

## 7. Runtime rules

- One RAF scheduler per active system.
- Use callback timestamps, not assumed refresh rate.
- Batch layout reads before writes.
- No continuous loop offscreen, hidden, suspended, or disposed.
- Avoid continuous large blur/filter/backdrop/mask cost unless profiling passes.
- Break up heavy activation work; do not parse/compile assets in input handlers.
- Repeated interaction, resize, locale change, and BFCache cannot accumulate
  listeners, observers, canvases, contexts, media, or resources.
- Use provisional WebGL DPR caps from `UI_UX_WEBGL_3D_PIPELINE.md`.

## 8. Lab method

For a performance-sensitive batch:

- use at least three comparable cold runs;
- optionally record warm/cache runs separately;
- report median and worst, not only best;
- keep URL, commit, profile, throttling, and tool version stable;
- record scores and raw metrics;
- retain trace/report references where the execution channel permits.

A score fluctuation does not authorize changing the target. Diagnose variance
and feature cost.

## 9. WebKit/Safari verification

Explicitly verify:

- `vh` fallback plus `svh`/`dvh`, safe areas, and browser chrome;
- fixed/sticky descendants of transform/filter/backdrop/contain;
- overflow, scroll lock, and stacking contexts;
- `-webkit-backdrop-filter` only with readable fallback;
- muted autoplay, `playsinline`, video texture lifecycle;
- touch/pointer/hover queries and passive scrolling;
- focus restoration, inert/hidden, keyboard;
- Inter/Cairo loading, Arabic glyph clipping, RTL alignment/scroll;
- canvas size/color, shader precision, context creation/loss, memory/disposal;
- page hide/show and BFCache.

## 10. Chromium verification

Explicitly verify:

- Lighthouse output and trace;
- LCP discovery/priority;
- long tasks, layout shifts, rendering/compositor/GPU cost;
- throttled load and interaction;
- accessibility tree and keyboard focus;
- WebGL context/resource lifecycle;
- Brave shields/extensions excluded as app variables.

## 11. Media/3D asset record

Every advanced group records purpose/owner, format/provenance/license, transfer,
decoded CPU/GPU cost, critical/deferred status, activation/LOD, fallback,
cache/version policy, and suspend/dispose behavior.

Small network files may still create excessive decode/GPU cost.

## 12. Failure policy

When an enhancement fails:

- content, navigation, locale control, and CTA remain;
- no infinite loader, trapped focus, or scroll lock remains;
- fallback appears without layout shift;
- error is observable for diagnosis;
- retry is bounded/user-driven where appropriate;
- resources are released.

Performance regression is a product failure, not deferred polish.

## 13. Proof status

Until baseline and runtime evidence exist:

- target: accepted;
- architecture: defined;
- current PageSpeed/CWV/browser parity: `BLOCKED_BY_MISSING_EVIDENCE`.

## 14. Primary references

- CWV: `https://web.dev/articles/vitals`
- Lab and field: `https://web.dev/articles/vitals-measurement-getting-started`
- Lighthouse: `https://developer.chrome.com/docs/lighthouse/performance/performance-scoring`
- RAF: `https://developer.mozilla.org/docs/Web/API/Window/requestAnimationFrame`
- WebGL: `https://developer.mozilla.org/docs/Web/API/WebGL_API`
- WebKit: `https://webkit.org/blog/`
