# MAP-V2-06 — Desktop Sound alignment + water tap

STATUS: PROVEN for declared Sound visual/interaction cases, 2026-10-06.

## OWNER_RAW / OWNER_CONFIRMED
"sound on di pc ... berbeda dari struktur dan gaya menu lainnya ... g harus line
kuning ... sejajar dan ukuran dan bold nya ... klo di klik ada efek riak air
transparan yg ringan ... khusus di sound ... kayak efek speaker"
Keep actual audio toggle, localized labels, Hero and compact/mobile wave intact.
No push or merge; preserve prior local About work.

## AI_TRANSLATION / AI_ASSUMPTIONS
Share desktop nav control type/height/label baseline metrics with Sound without
enabling menu underline/highlight/roll handlers. Transparent border reserves the
same label geometry. Remove desktop horizontal padding that makes Sound separate.
Native CSS transform/opacity animates one transient decorative ripple with two
concentric rings. Pointer origin follows click; keyboard uses button center.
Technical tuning:~680ms quiet current-color rings. No new animation loop/asset/
dependency or audio owner. Reduced motion skips animation; hidden/concealed/
resize/pagehide/dispose cancels it. One transient node, repeated tap replaces it.

## SCOPE / OUT_OF_SCOPE / LEGACY_REFERENCE
Editable: existing Header/Sound CSS, Sound visual module, focused browser proof,
docs2/current ledger. Read-only: Header state/adapter/DOM, Hero audio logic, About,
lang, media config/R2, Cursor, compact wave math/lifecycle, unrelated surfaces.
Legacy reference: NONE. Channel: local Terminal Codex, feat/home-v2-about.

## FACT → GAP → GOAL → IMPACT → DECISION
Main fetched a44d484f; local HEAD08039bba with uncommitted previous delivery.
1920 Sound font15.2px matches menu size but weight400 vs600; horizontal padding8px,
span lacks menu's baseline reservation. Sound span y25.59 vs menu y21.70.
Goal: shared visual rhythm and a brief transparent tap feedback specific to Sound.
Decision: extend current Sound visual owner, preserve existing requestAudio listener.
Canvas failure must not block desktop ripple. Full navigation capacity stays
>=1181 or>=1024 landscape; other tiers keep compact wave. ID/EN/AR same DOM and
current-color direction-neutral rings. No locale-specific JS or media loads.

## ACTIVE STEP / PROOF / GIT
Implement one bounded Sound visual change. Browser: font/weight/baseline/height
parity, white and dark Header, three locales, keyboard/pointer/repeated clicks,
cleanup/reduced motion/compact/canvas fallback, actual audio on/off and real
video progress. Verify only Sound responds; required build/structure/diff/full
PHP, focused PHP/Node, existing About/Header regressions. No result inferred.
Next: owner reviews local delivery. No publication requested.

## PROOF / PROGRESS / STATUS
33 locale/viewport cells PASS: Sound and menu font/weight/line/label y/height match
exactly on desktop, compact wave unchanged. At1920 both15.2px/600/y21.703/h28.594;
Sound border remains transparent. Native pointer and Enter toggle real Hero audio;
one transient CSS ripple, repeated clicks replace,680ms/one iteration then remove.
Hidden/reduced-motion/compact/resize/pagehide/dispose cleanup and no-canvas fallback
PASS. Three new browser tests plusnine existing regression tests PASS. Node14,
PHP15/142,build/structure262/diff PASS. Full PHP319/173 passed/71 failures/75 errors,
no new names.68 protected sources unchanged; compact wave body matches baseline
byte-for-byte after excluding the ripple wiring. Existing requestAudio unchanged.
Scoped STATUS: PASS; full repository suite FAIL from pre-existing legacy debt.
Evidence ../proof/header-sound-status.md. No push, merge, R2 or dependency mutation.
