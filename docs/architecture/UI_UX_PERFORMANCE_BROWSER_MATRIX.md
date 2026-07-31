# UI/UX Performance and Browser Matrix

Status: ACTIVE
Updated: 2026-07-31

## 1. Target interpretation

The product target is:

```text
Lighthouse/PageSpeed
Performance 100
Accessibility 100
Best Practices 100
SEO 100

Field Core Web Vitals
LCP good
INP good
CLS good
```

These are two evidence systems:

- Lighthouse is controlled lab evidence and can run before production.
- PageSpeed field/CrUX or RUM is real-user evidence and requires sufficient
  production visits.

Therefore an agent may prove a lab 100/100/100/100 but must not call field CWV
`3/3` proven without p75 field data.

Current good field thresholds:

- LCP <= 2.5 seconds;
- INP <= 200 milliseconds;
- CLS <= 0.1.

## 2. Required profiles

Record exact versions and hardware for every proof.

| Profile | Minimum purpose |
|---|---|
| Chromium mobile lab | Lighthouse/PageSpeed and 390px runtime |
| Chromium desktop | 1440px, keyboard, performance trace |
| WebKit mobile | 390px touch, viewport, media, overflow |
| WebKit tablet | 768px touch/orientation |
| Safari desktop | 1440px keyboard, media, graphics |

Brave may represent Chromium runtime behavior, but PageSpeed/Lighthouse evidence
must still name the actual tool and profile used.

Support means the tested current stable versions pass. Do not infer older
version support.

## 3. Baseline before numeric budgets

Do not invent byte or frame budgets without a baseline.

Before the first cinematic/3D implementation, record:

- HTML transfer and TTFB;
- critical and total CSS;
- initial and deferred JS;
- font files and used weights/subsets;
- LCP media;
- below-fold image/video media;
- long tasks and total blocking time;
- CLS sources;
- peak canvas/rendering memory where measurable.

Then assign the feature a delta budget. If the baseline changes, update the
ledger rather than quietly relaxing the gate.

## 4. Critical path rules

- Primary copy, navigation, and CTA are server-rendered.
- The LCP resource is discoverable in initial HTML when it is media.
- Do not lazy-load the real LCP image.
- Preload only the proven LCP/font resources required for first paint.
- Reserve image/video/canvas dimensions.
- Fonts use only required scripts and weights and cannot cause invisible
  critical text.
- Non-critical section CSS/JS/media is deferred or loaded by relevance.
- Hidden slides and inactive sections do not preload full video/model payloads.
- Third-party embeds load from a local cover/consent or interaction path where
  product requirements allow it.

## 5. Capability tiers

Enhancement is selected by capability and evidence, not device name or user
agent.

### Tier 0 — semantic static

- HTML content and controls;
- still media/poster;
- no spatial motion;
- required for unsupported graphics, failure, and reduced-motion safety.

### Tier 1 — lightweight motion

- CSS transform/opacity;
- IntersectionObserver/state classes;
- no continuous renderer;
- default safe enhancement.

### Tier 2 — interactive graphics

- canvas/WebGL or equivalent;
- lazy engine/assets;
- measured DPR and lifecycle;
- static fallback remains mounted or recoverable.

### Tier 3 — high fidelity

- heavier geometry, textures, post-processing, or continuous effects;
- allowed only for profiles proven to meet the accepted feature budget;
- must downgrade cleanly without changing content meaning.

One heuristic such as screen width, DPR, memory, core count, connection type, or
battery state is not enough by itself to deny required content.

## 6. Animation/runtime rules

- Use one `requestAnimationFrame` scheduler per active motion/rendering system.
- Time-based animation uses callback timestamps, not assumed refresh rate.
- No loop runs for an offscreen, hidden, paused, or disposed experience.
- Batch layout reads before writes; do not repeatedly force synchronous layout.
- Avoid continuous large blur/filter/backdrop work unless profiling passes.
- Break up long interaction tasks and keep event handlers small.
- Repeated open/close and locale/viewport changes cannot accumulate listeners,
  observers, timers, canvases, or media playback.

## 7. WebKit/Safari verification

Explicitly verify:

- `100vh` fallback plus `svh`/`dvh` behavior;
- fixed descendants and ancestors using transform/filter/backdrop/contain;
- `overflow`, sticky/fixed layers, safe areas, and scroll locking;
- `-webkit-backdrop-filter` only with a readable non-filter fallback;
- muted autoplay policy and `playsinline`;
- touch, pointer, hover media queries, and passive scrolling;
- focus restoration, inert/hidden state, and keyboard navigation;
- font loading, Arabic glyph clipping, and RTL scroll/alignment;
- canvas sizing, context creation/loss, color, and memory disposal;
- feature-gated CSS rather than assuming Chromium parity.

## 8. Chromium verification

Explicitly verify:

- Lighthouse category and metric output;
- Performance trace, long tasks, layout shifts, and rendering cost;
- throttled mobile load and interaction;
- accessibility tree and keyboard focus;
- GPU/compositor use does not hide main-thread or memory cost;
- Brave-specific shields/extensions are not mistaken for application behavior.

## 9. Media and 3D asset record

Every advanced asset group records:

```text
purpose
owner surface
format
compressed transfer bytes
decoded dimensions/estimated memory
critical or deferred
activation condition
fallback
cache policy
dispose/unload behavior
```

Models and textures must be compressed and right-sized from measured visual
need. A smaller network file can still create excessive decoded/GPU memory.

## 10. Failure policy

If the enhancement fails:

- content and CTA remain available;
- no infinite loader remains;
- scroll/focus are restored;
- fallback is shown without layout shift;
- the error is observable for diagnosis;
- retry is bounded and user-driven where appropriate.

Performance regression is a product failure, not deferred polish.

## 11. Reference sources

- CWV thresholds: `https://web.dev/articles/vitals`
- Field/lab: `https://web.dev/articles/vitals-measurement-getting-started`
- Lighthouse: `https://developer.chrome.com/docs/lighthouse/performance/performance-scoring`
- Animation: `https://developer.mozilla.org/docs/Web/API/Window/requestAnimationFrame`
- WebKit: `https://webkit.org/blog/`
