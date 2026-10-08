# Alternating background #73 — 2026-10-08

Scoped PASS. Fresh main2663b32e; feat/values-background-alternating.
One production file: resources/css/sections/program-detail.css.
Existing20rows now alternate right/left via CSS;9s leg becomes4.5s, full cycle18s
becomes9s. Existing travel/ease, semantic tree,20rows, shared Program/Values plane,
heading/blue morph/white SVG and Program detail choreography are preserved.
No new JS loop/controller/dependency. CSS pauses with existing visibility/state
policy; detail reparenting removes ambient translations; reduced/noJS stays static.

Three Chromium tests PASS:21drawing cells (360/640/768/1024/1280/1440/1536 ×
ID/EN/AR), ID weighted heading sequence and full GSAP Values/Program lifecycle.
All20rows:4500ms, opposite adjacent travel, one plane, no horizontal overflow.
Offscreen/synthetic hidden pause, reduced motion and detail ambient isolation PASS.

Older Arabic lifecycle gate remains FAIL on both fresh main and final:
scroll restore2950 vs2925, same assertion before the row-count check. Its expected
idle-animation count is updated1→20; no production scroll fix added to this scope.
This is not a green full browser-suite claim.

PHP Program/Values13tests/214assertions PASS. Full PHP base/final336tests:
190pass,71fail,75error,1696assertions; exact failure/error names identical.
Build/structure288sources≤200lines/diff PASS. Full PHP remains baseline FAIL.

Measured data: [values-background-73.json](values-background-73.json).
Cards: [source audit and future proof matrix](../reports/values-card-audit.md).
Card report is complete; card V2 layout/implementation/browser certification pending.

Engine Chromium153.0.8010.0. Browser fixtures serve exact downloaded GSAP and
ScrollTrigger3.7.1 and eight canonical school-life WebP files, with hashes in data.
Live browser CDN/media delivery, other engines, real device/performance unproven.
Initial runner-launch failures did not execute product assertions. Motion sampling
sets currentTime without manual play/pause overrides, preserving CSS lifecycle.

Issue #73 is the final CI/publication/main SHA handoff after this proof snapshot.
