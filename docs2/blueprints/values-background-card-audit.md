# MAP-V2-13 — Alternating background and Values card audit

STATUS: PROVEN background (scoped PASS, baseline gates FAIL); cards audit complete.
Source main: 2663b32e5b7a9caf72b9ccfb534c25fe4b957864.
Branch: feat/values-background-alternating.

## OWNER_RAW

"bg ... banyak teks gerak ... geser ke kanan dan geser ke kiri ... selang
seling ... lebih cepat 2x lipat ... kartu mulai dianalisis dan kasi laporannya
... nanti dipetakan ke tablet, hp dan pc dan 3 bahasa ... beberapa browser".

## AI_TRANSLATION

Alternate adjacent decorative rows physically left/right, halve the existing
9s leg to4.5s with its current travel/easing. Audit Values cards before mapping
their new responsive/locale/browser implementation. Publish the background
under the owner's existing issue/PR/main authorization.

## AI_ASSUMPTIONS

"Kartu" refers to Values cards: the earlier sequence deferred those cards while
Program cards are already implemented. This interpretation is stated to owner.
Retain current travel and ease because only direction/speed were requested.

## OWNER_CONFIRMED

Background motion change and a card analysis report; card implementation is
not authorized by this request. Previous SVG path/heading/blue morph accepted.

## SCOPE

Existing program-detail.css drift owner; affected motion/lifecycle tests; proof.
Read-only audit of Values legacy DOM, CSS, motion/data/media and language owners.
One semantic background plane remains; CSS animates its existing rows without
new JS frame loops. All six width tiers / ID EN AR share the alternating motion.

## OUT_OF_SCOPE

Card implementation, new art direction, SVG path/timing, heading/details,
Header/Hero/About, dependency changes and legacy activation/imports.

## LEGACY_REFERENCE

resources_old/views/home/sections/school-values.blade.php — semantic/card source.
resources_old/css/pages/welcome-values-story.css and js/surfaces/home/values/**
— layout/motion/lifecycle reference. HomeValuesComposer, home data traits,
lang ID EN AR and config/media.php — data/media/locale provenance.
Old Values blueprints may explain card intent, not supersede current owner scope.

## FACT

Fresh main: one shared Program/Values type plane,20rows;9s parent drift,
ease-in-out alternate. Existing IO/visibility/reduced policy pauses it, and
reparenting the same plane into Program detail removes ambient drift.
Active Values only renders a heading and the five-screen decorative SVG.

## DECISION

Replace parent drift with4.5s CSS row translations; even rows use the reverse
direction. Preserve physical left/right meaning across locales; Arabic content
and typography remain unchanged. Preserve pause/fallback/detail ownership.
Card report separates proven source facts, gaps and proposed future test matrix.

## PROOF

Three Chromium tests PASS:21width/locale drift cells, ID heading sequence,
Values/full GSAP detail reuse and lifecycle. All20rows have4500ms duration and
alternate physical travel; one shared plane, offscreen/hidden/reduced policy.
Older Arabic lifecycle test FAIL on both current main and final, identical
scroll assertion2950 vs2925 before the row-count assertion. It remains baseline.
PHP Program/Values13/214 PASS; full PHP base/final336:190pass,71fail,75error,
1696assertions, exact failure/error names match. Build/structure/diff PASS.
Other engines/device performance unproven. Card report: ../reports/values-card-audit.md.

## GIT

Issue #73; branch feat/values-background-alternating. PR/main publication pending;
issue records final external SHAs/CI/CLOSED handoff. Card audit stays report-only.

## NEXT VALID STEP

Publish verified background patch/report under existing owner authorization.
Next card step after review: map four cards against the five accepted line screens.
