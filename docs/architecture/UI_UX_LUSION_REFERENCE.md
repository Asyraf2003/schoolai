# Lusion Interaction Reference for SchoolAI

Status: REFERENCE, NON-NORMATIVE
Reviewed: 2026-07-31
Primary reference: `https://lusion.co/`

## 1. What was observed

Lusion publicly describes its work as combining design, motion, 3D, and
development into immersive visual storytelling.

Observable patterns on the current public site include:

- a cinematic, full-viewport opening statement;
- oversized editorial typography with short supporting copy;
- scroll used as narrative progression;
- staged section entrances and strong spatial transitions;
- project/media sequences that invite exploration;
- an immersive full-screen menu and cross-page continuity;
- reel/audio controls and explicit interaction prompts;
- 3D/WebGL as storytelling material rather than decoration alone.

Lusion's public project page for “Of The Oak” also describes a custom Houdini to
WebGL pipeline that reduced complex tree data to a 3.5 MB format and used
instancing. The transferable lesson is deliberate asset/pipeline optimization,
not that 3.5 MB is an approved SchoolAI budget.

## 2. SchoolAI translation

Use the principles, not a pixel clone.

| Lusion principle | SchoolAI translation |
|---|---|
| Strong opening world | School identity, student life, and educational purpose |
| Editorial scale | Unified semantic headings with locale-safe composition |
| Scroll narrative | Guided journey through vision, values, programs, gallery |
| Spatial transitions | Calm, clear educational motion with reduced-motion path |
| 3D focal object | Optional meaningful campus/science/learning artifact |
| Immersive project cards | Real school programs, articles, and gallery content |
| Full-screen navigation | Accessible unified navigation across public pages |
| Visual continuity | Shared motion grammar and color/type tokens |

The Al Mustaqbal result should feel confident, warm, exploratory, and credible.
It must not feel like an agency portfolio with school text inserted into it.

## 3. What must not be copied

- source code, shaders, models, images, video, audio, or typography assets;
- brand marks, compositions, copy, or distinctive scene identity;
- exact transition sequences used as a substitute for product discovery;
- unsupported-browser exclusion as the default product strategy;
- heavy initial payload merely to imitate visual fidelity;
- canvas-only content, navigation, or calls to action.

Lusion is inspiration, not a performance or accessibility waiver.

## 4. Reverse-engineering method

For each requested reference effect:

1. Capture the exact URL and viewport.
2. Identify the user trigger.
3. Record initial, intermediate, and final states.
4. Separate camera/3D motion from DOM/layout motion.
5. Measure duration, distance, easing, opacity, blur, scale, and layering where
   runtime tools allow it.
6. Identify loading, fallback, resize, reverse-scroll, and interruption states.
7. Translate the effect into SchoolAI content and semantic structure.
8. Design LTR/RTL and reduced-motion behavior before coding.
9. Assign a performance budget and lifecycle owner.
10. Implement one isolated prototype/surface and prove the full matrix.

A screen recording is useful for chronology. Screenshots are useful for
composition. DOM/computed styles/performance traces are required for ownership
and implementation facts.

## 5. Pattern card

Create one card before implementing a new cinematic pattern:

```text
PATTERN
- name and SchoolAI purpose

REFERENCE
- URL, viewport, recording/screenshot timestamp

STORYBOARD
- trigger
- enter
- active
- exit/reverse
- interruption/resize

OWNERSHIP
- semantic Blade
- component CSS
- controller
- optional renderer/assets

ADAPTERS
- 390/768/1440
- ID/EN/AR
- LTR/RTL
- Chromium/WebKit
- reduced motion

BUDGET
- critical/deferred bytes
- main-thread/rendering lifecycle
- fallback

PROOF
- automated, runtime, accessibility, performance
```

## 6. Recommended adoption order

Do not begin with a site-wide 3D runtime.

1. Establish the current performance and browser baseline.
2. Stabilize component/text ownership on the selected surface.
3. Implement the motion grammar with DOM/CSS.
4. Prove locale, direction, responsive, accessibility, and engine behavior.
5. Add one isolated 3D enhancement only if it improves the story.
6. Compare its measured value and cost before reuse.

## 7. Acceptance rule

A Lusion-inspired feature is accepted only when:

- the SchoolAI content purpose is explicit;
- the fallback is a complete, usable experience;
- it does not create a new locale/device/browser fork;
- it passes `UI_UX_DOD.md`;
- measured performance remains within the accepted target;
- the implementation is owned and removable without destabilizing unrelated
  sections.

## 8. Sources

- Lusion home: `https://lusion.co/`
- Lusion about: `https://lusion.co/about/`
- Lusion projects: `https://lusion.co/projects/`
- Of The Oak case study: `https://lusion.co/projects/of_the_oak/`
- Zero Tech case study: `https://lusion.co/projects/zero_tech/`
