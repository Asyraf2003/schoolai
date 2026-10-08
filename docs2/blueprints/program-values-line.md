# MAP-V2-12 — Program → Values white scroll line

STATUS: PROVEN (scoped PASS; full repository gates retain baseline FAIL).
Issue #69, branch feat/program-values-line.
Source main: d069d029db6556d2657ec3512bd045cbb128e251.

## OWNER_RAW

Full-screen scroll-driven SVG path drawing, Canva shapes, organic Lusion feel.
White, smoother and seamless; future route must remain invisible. Each
composition fills100vw×100vh and may leave the viewport. GSAP ScrollTrigger
scrub:true; down draws, up reverses. Start at Program → Values; issue, PR, main.

## AI_TRANSLATION

One continuous white path across five viewport compositions. No guide stroke,
marker, repeated restart, scroll lock or automatic playback. Native scroll
scrubs the visible length. Existing Values heading and blue morph stay intact.

## AI_ASSUMPTIONS

Canva raster centerlines are redrawn as smooth vectors, with mirrored/stretched
curves and connecting bends where needed. One five-viewport Values runway is
used while cards remain deferred. Stroke thickness adapts with CSS.

## OWNER_CONFIRMED

Start from Program entering Values. Owner accepted the five-composition reading
and authorized issue/PR/main publication in this session.

## SCOPE

Values SVG/view/style, its scroll adapter, shared GSAP loading only as needed,
composition lifecycle, focused tests, docs2 proof and publication.

## OUT_OF_SCOPE

Cards/cartoon, new copy, Header paint patch, Hero/About/nav, locale typography,
Program heading/detail choreography, other sections, legacy imports.

## LEGACY_REFERENCE

NONE. Visual reference: owner attachment Ashraf website.zip, PNG34–38,1920×1080.
Technical reference: official GSAP ScrollTrigger docs; existing Program3.7.1
loader is retained as the version authority. No Lusion code/assets are copied.

## FACT

Current Values is a title-only viewport. One decorative Program plane spans the
shared world; Program detail temporarily moves that same plane into its dialog.
Base GSAP3.7.1 loads on detail intent; base has no ScrollTrigger.
The implementation adds the matching3.7.1 plugin. Rendered pixel coordinates
are used for dash lengths with non-scaling stroke; resize rebuilds only this
timeline. Public plugin refresh avoids retaining an old higher scroll position.

## DECISION

One SVG with a continuous cubic B-spline conversion (C2 joins), spanning five
screen heights and starting20svh before Values. Clip only its decorative viewport
to prevent horizontal page overflow. SVG has no fill and only one white stroke.
Use the existing exact GSAP version and matching ScrollTrigger, lazily loaded
near Values with a shared in-flight loader. Arc-length checkpoints at viewport
seams give every composition equal scroll time; monotone arc-length interpolation
keeps speed continuous between them. There is no time lag; scrub is true.
All six width tiers and ID/EN/AR share the same decorative path/semantic tree.
No-JS/reduced/missing IO/load failure keep the existing title-only fallback.
Hidden/bfcache states suspend and restore only this owned trigger. Offscreen
progress is clamped by ScrollTrigger; no autoplay or extra scroll layout loop.

## PROOF

Actual Chromium PASS:21drawing cells, exact forward/reverse offsets, viewport
geometry, native scroll ownership,3raster pixel cases with invisible future
path, lifecycle/detail reuse, resize/reduced/dispose/network fallback, existing
Program/Values heading and background tests. Two geometry/speed tests PASS;
PHP V2:27/342assertions PASS. Build/structure/Pint/diff PASS. Full PHP/Node retain
the same baseline failures. Proof: ../proof/program-values-line-status.md.
Other engines/native hardware/performance scores remain unproven.

## GIT

Issue #69; feat/program-values-line; PR/main pending bounded proof.

## NEXT VALID STEP

Publish verified PR/main under owner authorization; owner pulls and builds.
