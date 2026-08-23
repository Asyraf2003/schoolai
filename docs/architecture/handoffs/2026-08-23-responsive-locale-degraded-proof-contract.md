# Responsive / Locale / Degraded Runtime Proof Contract — 2026-08-23

Status: `PASS / DURABLE / PROOF-CONTRACT`
Repository: `Asyraf2003/schoolai`

## Purpose

Freeze what H7 must prove after H2-H6 implementation. This is a test contract,
not a claim that the combinations already pass.

Canonical sources remain:

- `UI_UX_RESPONSIVE_LOCALE_MATRIX.md`;
- `UI_UX_PERFORMANCE_BROWSER_MATRIX.md`;
- `UI_UX_DOD.md`;
- `UI_UX_RELEASE_READINESS_CHECKLIST.md`;
- `handoffs/2026-08-23-functional-interaction-matrix.md`.

## 1. Base certification matrix

The minimum logical release matrix is:

```text
6 width tiers x 3 locales x 2 browser engines = 36 base cells
```

Width tiers:

- XS: 360-639;
- SM: 640-767;
- MD: 768-1023;
- LG: 1024-1279;
- XL: 1280-1535;
- 2XL: >=1536.

Locales/direction:

- ID / LTR / Inter;
- EN / LTR / Inter;
- AR / RTL / Cairo.

Engines:

- current Chromium-family runtime;
- Safari/WebKit family.

WebKit automation may support Safari confidence but must never be mislabeled as
physical Safari proof. If physical Safari/iPhone/iPad/macOS is unavailable, that
specific proof remains `BLOCKED_BY_MISSING_EVIDENCE`, not PASS.

## 2. Required representative widths

Every public release proves:

```text
360
390
640
768
1024
1280
1440
1536
1920
```

Affected global boundary pairs:

```text
639 / 640
767 / 768
1023 / 1024
1279 / 1280
1535 / 1536
```

Navigation-sensitive work additionally proves:

```text
1180 / 1181
```

Use 359/360 only when the minimum-width/root contract is changed. A boundary
pair does not replace an interior representative width.

## 3. Fidelity contract by space/input/capability

Tier names describe layout space, not hardware identity.

### Phone-class composition

Default product intent for XS/SM touch contexts:

- complete SchoolAI content, typography, hierarchy and art direction;
- polished static/light motion is an official final composition;
- no hover dependency;
- primary actions remain native/semantic without WebGL;
- continuous spatial rendering is optional, not a requirement for visual
  completeness.

### Tablet-class composition

Default intent for MD and touch-oriented LG contexts:

- same story and content hierarchy;
- semi-interactive/touch-driven choreography;
- more spatial depth than phone where capability supports it;
- no assumption that hover/pointer exists;
- orientation change preserves state or intentionally resets to a documented
  stable state.

### Desktop/laptop composition

Default intent for pointer-capable XL/2XL and suitable LG laptop contexts:

- richest approved cinematic behavior;
- pointer/hover enhancement may exist;
- full spatial/WebGL behavior is allowed when capability and performance gates
  pass;
- downgrade is valid when capability/performance fails, but content/actions may
  not disappear.

A width alone must not force a richer capability tier. Conversely, a strong
phone/tablet does not require desktop hover mechanics.

## 4. Static-first readiness contract

Every section has a valid semantic/static state before enhancement.

Accepted preparation direction:

```text
critical semantic/static viewport
-> Hero enhancement
-> Program preparation
-> Values preparation
-> Vision/Mission preparation
-> Gallery preparation
-> Article preparation
-> Footer preparation
```

Preparation is aggressive, sequential, persistent, and may continue while the
user remains near the top or the tab is hidden. Runtime execution remains
selective: no useless invisible continuous RAF/WebGL/video loop.

If a user reaches a section before enhancement preparation finishes:

- no blank section;
- no loader-only section;
- no dark/default shell replacing the intended design;
- no missing primary content/action;
- polished `STATIC_READY` remains visible;
- later enhancement may attach without losing scroll/focus/semantic state.

Prepared assets/state persist for reverse scroll rather than re-downloading or
reinitializing from zero unless disposal is explicitly required.

## 5. Degraded-mode matrix

### JavaScript delayed or failed

Required result:

- server-rendered content/navigation/locale/CTA remain usable;
- ordinary anchors/forms remain functional;
- no permanent scroll lock, inert shell, or hidden primary content;
- enhancement failure is diagnosable without exposing internal details.

### Reduced motion

Required result:

- all information/actions remain;
- Program uses functional reduced detail behavior;
- Gallery remains semantic/static rather than requiring WebGL;
- PPDB uses normal document flow rather than pinned wheel capture;
- Hero autoplay/motion respects preference;
- no essential meaning exists only in animation.

### WebGL unavailable / context creation failure / context loss

Required result:

- Gallery uses its semantic/static fallback;
- content/actions and Gallery route CTA survive;
- no repeated automatic context recreation loop;
- Values remains valid because semantic/static state is independent of spatial
  enhancement;
- layout dimensions remain reserved to prevent disruptive shifts.

### Third-party enhancement unavailable

GSAP/CDN or approved remote enhancement failure must not remove Program controls
or content. Functional reduced behavior is the required fallback.

Remote media failure must preserve layout and copy; H6 later replaces content
media ownership with the approved Cloudflare path.

### Delayed network

Prove at minimum:

- initial semantic/static viewport does not wait for lower-page graphics/media;
- sequential background preparation does not block input;
- fast scrolling ahead of preparation still lands on a complete static state;
- delayed enhancement attaches once and does not replay a destructive page
  initialization.

### Hidden tab

Preparation/download/cache work may continue where the browser permits.
Continuous render/playback work must suspend when it has no visible value.
Returning visible must reconcile state/geometry once without duplicate owners.

### BFCache

`pagehide/pageshow` must preserve or restore the documented state without
listener/observer/canvas/context duplication. A non-persisted exit may dispose;
a persisted BFCache exit must not create a second owner on return.

## 6. Locale-switch proof

Server POST + redirect remains the baseline contract.

Required transitions:

```text
ID -> EN
EN -> ID
ID -> AR
EN -> AR
AR -> ID
AR -> EN
```

Prove switching from at least:

- Home;
- Gallery page;
- Article index/detail as applicable;
- PPDB when open;
- open navigation/language UI;
- active or prepared cinematic section where feasible.

Required result:

- new document `lang` and `dir` agree with locale;
- ID/EN remain LTR and AR remains RTL;
- no stale canvas/text alignment from the previous direction;
- no mixed-language chrome/content caused by stale client state;
- focus/scroll restoration is intentional;
- back/forward/refresh preserve the server/session/cookie contract.

## 7. Input proof

### Keyboard

Prove applicable paths from D4:

- logical Tab order and visible focus;
- Enter/Space activation for control-like Gallery cards;
- Escape close for dialogs/menus;
- Program Tab containment and trigger-focus restoration;
- mega-menu ArrowDown behavior;
- Hero ArrowLeft/ArrowRight;
- PPDB pinned vertical keyboard journey where enhancement is enabled.

H7 must explicitly inspect two known source gaps:

- Gallery lightbox focus containment;
- PPDB ARIA-tab ArrowLeft/ArrowRight/roving-focus behavior.

A correction, if required, is bounded accessibility work rather than a visual
redesign.

### Touch/coarse pointer

Prove:

- mobile navigation;
- Hero swipe without stealing interactive-control gestures;
- Program open/back;
- Gallery card/lightbox;
- PPDB normal touch scrolling and audience switching;
- no interaction requires hover.

### Pointer/hover

Desktop hover effects are enhancement only. Click/focus remains the semantic
action and must work without prior hover.

## 8. Scroll/lifecycle stress proof

For stateful Home surfaces:

- slow forward;
- fast forward;
- reverse;
- rapid forward/reverse alternation;
- deliberate pause near handoff/state boundaries;
- repeated leave/re-enter;
- resize/orientation while idle/loading/active/ending/fallback where applicable;
- hidden/visible tab;
- BFCache back/forward;
- reduced-motion switch when the platform permits dynamic preference change.

Required invariant:

- no blank/dark unintended shell;
- no contradictory state classes;
- no stuck `inert` or scroll lock;
- no duplicate listener/observer/timer/canvas/context accumulation;
- semantic content remains available throughout.

H1 specifically still requires owner/browser slow forward + reverse Gallery proof
before H1 is marked complete.

## 9. Short height, orientation, zoom

At least one representative case per phone/tablet/desktop family must include a
short block-size condition in addition to standard portrait/landscape proof.

Verify:

- sticky/pinned stages remain escapable;
- dialogs/menus can scroll internally if needed;
- controls are not placed outside reachable viewport;
- safe-area/browser chrome does not hide primary controls;
- 200% zoom/text expansion does not create horizontal overflow or clipped
  essential content.

Exact device pixels used in H7 must be recorded with the run rather than
pretending a generic device label proves geometry.

## 10. Browser-specific proof

### Chromium family

Record exact browser/version/OS. Brave is acceptable Chromium evidence when
named explicitly and Shields/extensions are not treated as application state.

Verify:

- Lighthouse/trace;
- sticky/fixed/overflow/scroll lock;
- accessibility tree/focus;
- compositor/long tasks;
- WebGL lifecycle;
- media/autoplay behavior.

### Safari/WebKit family

Verify:

- `vh`/`svh`/`dvh` and browser chrome;
- sticky/fixed descendants of transform/filter/backdrop/contain;
- overflow and scroll lock;
- `inert`, focus restoration and keyboard;
- muted autoplay/playsinline;
- Arabic glyph clipping/RTL;
- canvas resolution/context loss/disposal;
- pagehide/pageshow and BFCache.

Physical Safari proof and automated WebKit proof must be labeled separately.

## 11. Performance evidence contract

H7 performance-sensitive proof uses at least three comparable cold lab runs per
declared performance profile and reports median + worst.

Record:

- source/deploy SHA;
- URL;
- browser/tool/version;
- OS/hardware/input;
- viewport;
- throttling;
- cold/warm state;
- scores and raw metrics;
- HTML/CSS/JS/media transfer;
- LCP resource/element;
- CLS sources;
- long tasks/TBT/INP proxy;
- relevant canvas/renderer evidence.

Lab target remains:

```text
Performance 100
Accessibility 100
Best Practices 100
SEO 100
```

Field `3/3` remains a separate claim requiring p75 RUM/CrUX evidence:

- LCP <= 2.5s;
- INP <= 200ms;
- CLS <= 0.1.

No agent may infer field PASS from Lighthouse.

## 12. PASS / FAIL / BLOCKED policy

A cell may be `PASS` only from actual evidence on the named source/deploy SHA.

Use:

- `PASS`: required evidence was executed and met contract;
- `FAIL`: evidence executed and violated contract;
- `BLOCKED_BY_MISSING_EVIDENCE`: required environment/tool/device/proof was not
  available or was not executed.

Do not convert unavailable physical Safari, field CWV, or unrun runtime stress
into PASS by analogy.

## D5 conclusion

Responsive/device fidelity, locale/direction, degraded runtime, browser/input,
lifecycle and performance proof are now specified strongly enough for H7 without
additional owner architecture decisions. D6 may now freeze the baseline and H2-
H7 execution packets.
