# Program → Values wall endpoints #71 — 2026-10-08

Scoped PASS. Fresh main d408da28; branch fix/values-line-wall-ends.
Owner accepts the rest and requires side-wall entry/exit with no intersections.
One continuous path now enters and exits the right wall; caps are offscreen.
Only five SVG coordinate commands changed. Central curves, C2 joins, shared
scroll animation, heading/background, lifecycle and fallback are preserved.

Four geometry/speed tests and seven Chromium tests PASS (21width/locale cells,
forward/reverse, lifecycle, fallback, Values intro/blue morph, rendered pixels).
The final pixel test rerun also PASS, with a completed-scroll exit snapshot.
At360/768/1440px, entry/exit wall alpha255; future stroke alpha0.
Values PHP4tests/48assertions PASS; build/structure288sources/Pint/diff PASS.
Full PHP base/final336tests:190pass,71fail,75error,1696assertions. Exact failure
and error names are identical. Full PHP remains baseline FAIL, not green.
Fixture/engine/delivery limits from the original proof below still apply.

Data: [wall endpoints](values-line-wall-71.json). Actual1440×900 snapshots:
[entry](values-line-wall-entry-71.png), [completed exit](values-line-wall-end-71.png).
Issue #71 is the canonical publication/CI handoff after this verification snapshot.

## Previous #69 proof snapshot

Scoped PASS. Source main d069d029; branch feat/program-values-line.
Owner authorized issue/PR/main, then pulls. Cards/cartoon remain deferred.

One white SVG path spans five viewport compositions and starts20svh before
Values. Canva34–38 centerline forms are adapted into smooth cubic B-spline
curves; every join has continuous tangent/curvature. Monotone arc-length
interpolation keeps speed continuous between screen checkpoints. Native scroll
drives stroke-dashoffset through GSAP/ScrollTrigger3.7.1, scrub:true, with no lag,
pinning or autoplay. Down draws; up restores the same drawn length.

SVG host pixels are used for dash lengths with non-scaling stroke. Resize
rebuilds one owned timeline; public plugin refresh clears old scroll records.
One in-flight GSAP loader serves both Values and the existing Program detail.
No-JS/reduced/missing IO/CDN failure retain the original title-only fallback.
Hidden/bfcache suspend/resume and disposal clear the owned trigger/listeners.

11 Chromium browser tests PASS across the final runs:21drawing cells across
360/640/768/1024/1280/1536/1920×ID/EN/AR; exact reverse offsets; native scroll
ownership; lifecycle/detail reuse; unavailable plugin;3raster pixel cases;
4Program heading tests;3Values intro tests with21layout/3sequence/27fallback
cells. Drawn pixels have alpha255; future path samples have alpha0 at360/768/1440.
The resize harness waits for pixel-rounded plugin geometry before scroll input.
Two geometry/speed tests PASS; PHP V2:27tests/342assertions PASS.

Full PHP base/final336tests:190pass,71fail,75error. Assertions1687→1696.
Failure/error names match exactly. Node Runtime base/final38tests:19pass,7fail,
12skip; same failures. These full repository gates remain baseline FAIL.
Build/structure288sources<200lines/Pint/diff PASS;619protected sources unchanged.
No package/dependency update, legacy import, heading/style/detail choreography,
Header paint patch or content change.

Measured data: [program-values-line-69.json](program-values-line-69.json).
Actual build snapshots1440×900 (AR; one path is identical in every locale):
[entry](values-line-entry.png), [one](values-line-one.png),
[two](values-line-two.png), [three](values-line-three.png),
[four](values-line-four.png), [five](values-line-five.png).

Browser external CDN/media access is unavailable here. Exact3.7.1 libraries
and the canonical64×64logo were fulfilled from downloaded fixtures; hashes are
in the measured data. Other media appear unavailable in snapshots. Live delivery,
Firefox/WebKit/native Safari, device hardware and CWV scores are unproven.

Git: Issue #69 closed / PR #70 merged at main d408da28. Follow-up #71 publication
uses the existing owner authorization. Cards/cartoon remain deferred.
