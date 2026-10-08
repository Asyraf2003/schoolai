# MAP-V2-12 — Program → Values white scroll line

STATUS: PROVEN wall-endpoint follow-up (scoped PASS; full PHP baseline FAIL).
Issue #71, branch fix/values-line-wall-ends; previous issue #69 / merged PR #70.
Source main: d408da28a3f9531ae954b249997497b2737e6e75.

## OWNER_RAW

Full-screen scroll-driven SVG path drawing, Canva shapes, organic Lusion feel.
White, smoother and seamless; future route must remain invisible. Each
composition fills100vw×100vh and may leave the viewport. GSAP ScrollTrigger
scrub:true; down draws, up reverses. Start at Program → Values; issue, PR, main.

Latest: "startnya harus dari dinding atau pinggir layar dan finisnya juga
berakhir di pinggir layar ... bebas kanan atau kiri ... bisa 2 tapi g boleh
saling memotong ... boleh balik ke dinding yg sama boleh berlawanan ...
sisanya beres".

## AI_TRANSLATION

One continuous white path across five viewport compositions. No guide stroke,
marker, repeated restart, scroll lock or automatic playback. Native scroll
scrubs the visible length. Existing Values heading and blue morph stay intact.
Follow-up: move only entry/exit to a side wall and guard against intersections.

## AI_ASSUMPTIONS

Canva raster centerlines are redrawn as smooth vectors, with mirrored/stretched
curves and connecting bends where needed. One five-viewport Values runway is
used while cards remain deferred. Stroke thickness adapts with CSS.
Follow-up implementation choice: retain one path, entering and leaving the
right wall. The owner allows one/two paths and same/opposite walls.

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
Wall follow-up: change the first/last B-spline controls so both caps are outside
the right boundary, with y inside the five-screen canvas. Keep all central
curves, C2 joins and animation owners. Test side clipping and curve crossings
in addition to existing forward/reverse and rendered stroke proof.

## PROOF

Actual Chromium PASS:21drawing cells, exact forward/reverse offsets, viewport
geometry, native scroll ownership,3raster pixel cases with invisible future
path, lifecycle/detail reuse, resize/reduced/dispose/network fallback, existing
Program/Values heading and background tests. Two geometry/speed tests PASS;
PHP V2:27/342assertions PASS. Build/structure/Pint/diff PASS. Full PHP/Node retain
the same baseline failures. Proof: ../proof/program-values-line-status.md.
Other engines/native hardware/performance scores remain unproven.

Follow-up #71: four geometry/speed tests PASS, including side-wall clipping and
non-intersection checks with <0.1px chord error. Seven Chromium tests PASS:
21drawing cells, native forward/reverse, lifecycle/fallback, existing Values
intro/background and three rendered pixel cases. Entry/exit wall pixels alpha255;
future stroke alpha0. End snapshot captured in the final pixel test rerun PASS.
PHP Values4/48 PASS; full PHP base/final336:190pass,71fail,75error,1696assertions,
with identical exact failure/error names. Build/structure/Pint/diff PASS.
Only production change: five coordinate commands in the existing SVG path.
Data: ../proof/values-line-wall-71.json.

## GIT

Original: issue #69 / PR #70 merged at d408da28.
Follow-up: issue #71 / fix/values-line-wall-ends; PR/main pending bounded proof.

## NEXT VALID STEP

Publish the verified follow-up PR/main under existing owner authorization;
the issue records the final remote SHAs and CLOSED handoff.
