# Homepage Values Card Story Blueprint

Blueprint ID: `HOME-VALUES-001`
Status: `IMPLEMENTING`
Owner: Asyraf Mubarak
Date: 2026-08-03
Source main SHA: `7607f93abab66c9fb96bcdb313ce08371f8cdfe3`
Surface: homepage `#nilai`
Execution channel: Web AI with explicit direct-`main` authorization

## Owner goal and reference

Build a full-viewport Values scroll story informed by the owner-provided Lusion
About screenshots while using real Al Mustaqbal content and school-owned visual
language. Lusion code, assets, branding, card art, and exact composition remain
forbidden.

## Current owner refinement: heading and description

This atomic step changes only the Values heading and supporting copy:

1. Both heading lines begin invisible at one shared vertical center seam.
2. They separate smoothly into the final two-line heading.
3. The lower line shifts slightly inward only when the available field is wide.
4. Phone layouts show the heading only, without description or eyebrow.
5. Tablet layouts keep supporting copy but do not shift the lower heading line.
6. Desktop XL and 2XL keep supporting copy and use a bounded inward shift.
7. Heading exit reverses toward the shared seam while fading, without
   momentum-driven letter collisions.

Cards, flip timing, trail, transition, content, and following sections are
read-only in this step.

## FACT and GAP

- Values has one semantic heading, description, eyebrow, and four article cards.
- One controller owns progress, measurement, inertia, painting, and lifecycle.
- The latest 1920x1080 Brave screenshots prove the heading uses excessive
  opposing viewport travel and creates overlapping/ghosted exit states.
- The screenshots also prove supporting copy remains visible on phone behavior
  contrary to the owner's latest decision.
- Build, WebKit, all-tier, accessibility, and performance proof remain gaps.

## Scope

Editable for this refinement:

- `resources/css/surfaces/home/values/story-shell.css`
- `resources/css/surfaces/home/values/story-responsive.css`
- `resources/js/surfaces/home/values/controller.js`
- `resources/js/surfaces/home/values/layout.js`
- `resources/js/surfaces/home/values/paint.js`
- active blueprint/current-state records

Protected:

- Values Blade/content and all cards, backs, flip poses, trail, transition;
- Hero, Vision/Mission, Programs, Gallery, Articles, navigation, footer;
- About/Testimonial state, DB, routes, controller data, translations, media,
  WebGL, dependencies, and unrelated cleanup.

## Semantic and fallback contract

- One localized `h2` remains the semantic heading.
- Description and eyebrow remain in the single DOM and are visually suppressed
  on XS/SM by the accepted responsive contract.
- No-JS, unsupported motion, and reduced-motion results remain usable.
- Cards and content remain reachable and unchanged.

## Heading motion contract

The heading uses font-relative geometry:

```text
initial line one: +0.41em
initial line two: -0.41em
line-height: 0.82
result: both line centers share one seam
```

Opacity rises while both offsets approach zero. On exit the offsets return
toward the seam while opacity falls. Scroll momentum may affect supporting copy
slightly, but it must not displace individual heading lines.

Line-two inward shift begins after the vertical opening is nearly complete and
returns while the heading exits.

## Six-tier heading/copy contract

| Tier | Copy | Lower-line shift |
|---|---|---|
| XS 360–639 | hidden | none |
| SM 640–767 | hidden | none |
| MD 768–1023 | visible | none |
| LG 1024–1279 | visible | none |
| XL 1280–1535 | visible | `104px` logical inward |
| 2XL >=1536 | visible | `144px` logical inward |

CSS owns the tier target. The controller reads the resolved target during
measurement, and the timeline paints only its progress. RTL uses the opposite
physical sign so logical inward motion still approaches the visual center.

## Browser, performance, and accessibility

- Frequent heading animation remains transform/opacity only.
- No new asset, dependency, filter, layout animation, or continuous scheduler.
- Logical inset properties preserve the shared ID/EN/AR DOM.
- Reduced motion retains the static heading/card result.
- Chromium/WebKit, short-height, reverse scroll, 200% zoom, and locale runtime
  still require proof.

## Active execution and proof

1. `IMPLEMENTED_SOURCE`: publish the bounded heading/copy correction.
2. `PENDING`: run repository diff, structure, build, and PHP test gates.
3. `PENDING`: capture 390, 768, 1280, and 1920 Brave/Chromium heading/copy.
4. `PENDING`: expand to all six tiers, ID/EN/AR, WebKit, reduced motion, zoom,
   accessibility, and performance.

Commit publication proves source state only. Until pending gates run, final
status is `BLOCKED_BY_MISSING_EVIDENCE`, not `PASS`.
