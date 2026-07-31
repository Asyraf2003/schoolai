# Responsive, Locale, and Direction Matrix

Status: ACTIVE
Updated: 2026-07-31
Certified minimum width: `360px`

## 1. Principle

Responsive architecture answers available space, content, input, orientation,
and capability. Tier names are convenient labels, not device detection.

One semantic DOM and controller are shared by default. CSS owns layout changes;
component container queries may add measured local boundaries.

## 2. Six global tiers

| Tier | Width contract | Typical context, not detection | Default composition intent |
|---|---:|---|---|
| XS | 360–639px | phone portrait/small foldable | one column, touch-first, concise spatial travel |
| SM | 640–767px | wide/landscape phone | one wide column; split only when content proves fit |
| MD | 768–1023px | tablet portrait/square | one or two columns; balanced touch targets |
| LG | 1024–1279px | tablet landscape/small laptop | expanded composition; nav changes inside tier |
| XL | 1280–1535px | standard laptop/desktop | full desktop art direction |
| 2XL | >=1536px | large/ultra-wide display | bounded content with expandable cinematic field |

Below 360px must not be intentionally broken, but it is outside certified
release proof until the owner expands support.

CSS global boundaries:

```text
base: 360+
@media (min-width: 640px)
@media (min-width: 768px)
@media (min-width: 1024px)
@media (min-width: 1280px)
@media (min-width: 1536px)
```

Do not add all six queries when fluid/base CSS already satisfies a tier.
Contracts and proof are mandatory; redundant rules are not.

## 3. Navigation sub-boundary

Navigation has an existing product contract:

```text
<=1180px: hamburger/cinematic mobile-tablet interaction
>=1181px: desktop navigation
```

This boundary lives inside LG. Any navigation or containing-layout change must
prove 1180px and 1181px in every locale and both required engines.

## 4. Required visual proof widths

Every public surface release proves at least one representative per tier:

```text
XS   360 and 390
SM   640
MD   768
LG   1024
XL   1280 and 1440
2XL  1536 and 1920
```

Also prove both sides of every affected boundary:

```text
359/360 minimum support when the root contract changes
639/640
767/768
1023/1024
1180/1181 when navigation or its container changes
1279/1280
1535/1536
```

Boundary proof may be automated screenshot/layout assertions. It must still be
reviewed for wrapping, overlap, crop, focus, motion, and overflow.

## 5. Surface tier contract

Each accepted blueprint fills this for all six tiers:

| Concern | Required decision |
|---|---|
| content order | semantic reading/focus order |
| grid | columns, gaps, max width, alignment |
| typography | role/token and wrapping behavior |
| media | aspect, crop, focal point, poster |
| controls | placement, target size, keyboard/touch |
| navigation | mode and focus/scroll lock |
| motion | distance, trigger, pinning, reduced result |
| WebGL | canvas bounds, camera, quality tier, fallback |
| short height | clipping/scroll behavior |
| orientation | portrait/landscape reflow and state |

Do not encode full layout decisions in tier names alone.

## 6. Fluid and local behavior

- Prefer `min()`, `max()`, `clamp()`, grid/flex, logical properties, and
  intrinsic sizing.
- Use container queries when a component's own width—not viewport width—causes
  the change.
- Record local boundary and test one pixel below/at it.
- DOM order follows semantic reading order; visual reordering cannot corrupt
  keyboard/screen-reader order.
- 2XL cinematic backgrounds may widen, but copy and actions use readable max
  widths and cannot drift into empty ultra-wide space.
- Test short viewports, browser chrome, notches/safe areas, orientation change,
  virtual keyboard where relevant, and 200% zoom.

## 7. Locale matrix

| Locale | Direction | Public family | Shared system | Required risk focus |
|---|---|---|---|---|
| ID | LTR | Inter | Latin layout/motion | long Indonesian wrapping |
| EN | LTR | Inter | Latin layout/motion | different word lengths |
| AR | RTL | Cairo | semantic roles + narrow adapter | glyph clipping, RTL alignment, joining |

All six tiers must be proven in all three locales. ID-only tier proof does not
prove EN/AR.

## 8. Direction rules

Mirror when meaning is directional:

- start/end entrances;
- previous/next arrows;
- menu origin and reveal path;
- reading progress and directional connectors;
- camera framing tied to text start/end.

Do not mirror automatically:

- time;
- vertical scroll;
- play/pause;
- neutral rotation/orbit;
- logos, photographs, maps, charts, or real-world orientation;
- numbers unless localization semantics require it.

Use logical design tokens such as `--inline-sign: 1` and `-1` only when their
meaning is documented. Do not scatter negative RTL overrides.

## 9. Locale-switch lifecycle

Current switching is a server POST + redirect. Required behavior:

```text
idle
-> submit requested
-> duplicate submit blocked
-> motion/media/renderer suspended
-> server response emits new lang + dir
-> CSS/font/layout ready
-> camera/anchors/text geometry recomputed
-> focus and scroll intentionally restored
-> active
```

Without JS, the form redirect remains fully functional.

Test:

- ID -> EN and EN -> ID;
- ID -> AR and EN -> AR;
- AR -> ID and AR -> EN;
- switch during animation, open navigation, loaded WebGL, resize, and reduced
  motion;
- refresh/back-forward after switching;
- no mixed direction, stale canvas alignment, duplicate listeners, or focus
  loss.

If future client-side transitions replace reloads, they must preserve the same
state contract and atomically update content, `lang`, and `dir`.

## 10. Browser/input matrix

At every representative tier, use the applicable input:

- keyboard/pointer on desktop and touch/orientation on mobile/tablet;
- hover-none/coarse-pointer behavior;
- Safari/WebKit viewport, overflow, fixed/sticky, font, and canvas behavior;
- Chromium layout, accessibility tree, trace, and compositor behavior.
Brave may prove Chromium runtime behavior, but reports must name Brave and must
not treat extension/shield behavior as application behavior.

## 11. Acceptance

A surface is responsive/locale `PASS` only when:

- all six tiers, representatives, and affected boundary pairs are proven;
- ID/EN/AR have no unintended overflow, clipping, or hierarchy drift;
- LTR/RTL motion is intentional and orientation/resize preserves state;
- keyboard, touch, focus, zoom, and reduced motion work;
- Chromium and WebKit proof is recorded.
