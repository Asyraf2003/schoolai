# MAP-V2-17 — Program/Values autonomous headings and three white Values strokes

STATUS: FAIL full WebKit regression; implementation MERGED; focused Chromium/WebKit motion CI PASS

## OWNER_RAW

“bagian program itu our program nah teks judul yg bawah setelah animasi muncul ... setelah sempurna baru geser ke agak tengah ... kecepatannya sama kayak animasi muncul yg awal”; “buat nilai mahasiswa juga pake gaya yg sama”; “untuk ar rtl itu klo ada 2 baris buat sama klo sebaris cukup muncul biasa dari bawah ke atas”; “garis belakang card value ... 3 garis ... masing-masing jalur ... 1x pemotongan”.

## AI_TRANSLATION

Program and Values headings: complete the reveal animation on each line, then trigger a single independent inward slide of the second line for the same duration as reveal. Two-line Arabic mirrors direction. One-line Arabic reveals upward without shifting. Three geometrically distinct Values strokes share the existing five-screen field, are scroll-drawn/reversed separately and cross exactly once between first and second strokes.

## AI_ASSUMPTIONS

- Program uses 760ms for both stages; Values uses existing 900ms reveal and is aligned to a 900ms slide, replacing its previous 880ms slide.
- Keep current relative shift distances where possible; EN Program now has the same inward movement as ID, RTL mirrors.
- Stroke trajectories originate/terminate at side walls and use a single scrubbed ScrollTrigger, with three stroke-specific draw intervals.

## OWNER_CONFIRMED

Three paths, one crossing; two-stage heading sequence; Arabic one-line special case. Issue/PR/merge main authorized.

## SCOPE

Program reveal ownership and heading CSS/JS, Values heading CSS/JS, Values SVG and GSAP timeline owner, relevant UI/browser tests, docs2.

## OUT_OF_SCOPE

Content/locale copy, Program cards/detail animation, Values cards/deck, marquee 4.5s, transition colors, Header/Hero/About, PPDB, dependencies.

## LEGACY_REFERENCE

NONE. Previous path #69/#71 is superseded by the owner's new three-path request, not accidentally changed.

## FACT

Program heading previously used scroll-dependent CSS transitions and only ID shifted by a different 1200ms duration. Values already had an autonomous 900ms reveal / 880ms slide; current Arabic Program has one line, Arabic Values has two. Values had one long SVG path and single stroke dash controller.

## DECISION

Share a small one-shot Web Animations state machine between Program and Values. The shared module gates the second line on actual completion; scroll cannot reset or control progress. Use one SVG with three path elements and one ScrollTrigger timeline, with each stroke's dash interpolated in its own vertical subrange. Keep original 500svh layout/scene height.

## PROOF

Source geometry sample: path1/path2 cross once at approximately x1658/y2103 in 1920×5400 coordinates; other pairs cross zero times. [Focused Chromium/WebKit CI](https://github.com/Asyraf2003/schoolai/actions/runs/38009783546) SUCCESS on merged source head c761d14c: heading lifecycle and 760ms/900ms sequence, three-stroke draw/reverse, reduced-motion, responsive locale cases. Source structure, Vite build and three-path geometry PASS. The full Values regression run38009783595 subsequently finished: Chromium PASS; WebKit13PASS/1FAIL in the900ms heading lifecycle assertion. Its root cause remains unresolved; full regression status is FAIL. Earlier full Chromium passed; WebKit exposed a flaky short-phase polling assertion, replaced by a MutationObserver-based proof that passed in the focused CI. Existing repository-wide dependency security-audit baseline still FAIL and was not changed.

## GIT

Issue #82 CLOSED; branch fix/v2-headings-three-values-lines; [PR #83](https://github.com/Asyraf2003/schoolai/pull/83) MERGED to main at f4d4a636f19d111dabb36dc98e0f0d5fa81e746e.

## NEXT VALID STEP

Terminal Codex: reconcile the full WebKit900ms heading lifecycle assertion failure with the passing focused CI before overall acceptance PASS. Implementation and publication are complete; full regression proof remains FAIL.

## Owner correction — 2026-10-10 / OWNER_ACCEPTED

FACT: current cubic joins reverse their horizontal tangent abruptly, producing
zigzags despite round stroke joins. Owner rejects this visual result.
GOAL: three flowing hair-like strands, gentle downward bends, one crossing.
DECISION/BLUEPRINT: replace only Values SVG geometry and draw intervals. Three
long descending curves with continuous tangent at every join; first/second cross
once, third remains separate. Natural rounded lower tips replace side-wall exits;
this supersedes the previous side-wall assumption. No heading/card/controller change.
Six tiers360/640/768/1024/1280/1536 share the scaled SVG; ID/EN/AR share decorative
geometry (no directional meaning). Existing static/reduced-motion fallback,
hidden/BFCache/dispose and one scrubbed trigger remain. No new assets/dependencies.
ACTIVE STEP: change curves; prove tangent continuity, downward progress and exactly
one crossing, inspect rendered SVG; diff/structure/build/PHP gates. Chromium/WebKit
page proof and performance claims require actual runtime evidence.
