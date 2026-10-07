# MAP-V2-05 — Fluid type balance / cursor-only mascot / directional shadow

STATUS: PROVEN for latest label/shadow refinement, 2026-10-06.

## OWNER_RAW / OWNER_CONFIRMED
"seluruh ukuran web ini itu dinamis sesuai skala layar"
"bisa bantu sesuaikan soal ukuran font? termasuk maskot dihapus yg sebelahnya
media yaa, maskot hanya di kursor kemudian bisa kasi shadow agar seakan media itu
timbul, shadow hanya di bawah kanan, jadi cahayanya kayak dari kiri atas"
Quoted web-AI values are proportional reference, not fixed viewport dimensions.
Retain large editorial heading/media presence, accepted geometry and behavior.
No push/merge; current local branch stays the execution channel.

## AI_TRANSLATION
Keep clamp/rem/viewport scaling: slightly reduce headline's fluid curve/weight,
slightly strengthen body, tighten leading/gap, neutral dark paragraph color.
Remove decorative mascot DOM/CSS/config/presenter field; keep global cursor.
Use positive physical X/Y shadows, negative spread and fluid shadow scale so
light reads from upper left even in RTL. No new asset, loop or media lifecycle.

## AI_ASSUMPTIONS
None for scope. Technical tuning uses~68px headline/~18.6px body at1920 as one
proof sample of fluid scaling, not fixed sizing rules. Arabic existing Cairo
line-composition adapter remains unchanged to protect glyphs.

## SCOPE / OUT_OF_SCOPE / LEGACY_REFERENCE
Editable: About CSS and mascot DOM/data removal, relevant existing tests,
docs2/current ledger/proof. Read-only: Header geometry/logic, Hero, all JS/media/
modal/observer/locale owners, lang, cursor, legacy, other sections and R2 objects.
Delete only newly obsolete about_mascot key after reference proof; old URLs intact.
Legacy reference: NONE. One channel: local Terminal Codex, feat/home-v2-about.

## FACT → GAP → GOAL → IMPACT → DECISION
Main fetched a44d484f; HEAD08039bba with ongoing uncommitted local delivery.
Current source hashes match MAP-V2-04 browser evidence.1920 typography headline76,
weight700/line1.12 vs body18/line1.65; body gap24px, color#35453f.
Current shadow has zero X offset/all-around treatment. Decorative mascot has
only About-owned DOM/config/presenter/CSS/test consumers; cursor is independent.
Goal: improve relative visual weight across six tiers, remove side mascot,
create upper-left light / bottom-right cast while preserving fixed media geometry.
Decision: replace current owners directly; no stronger selector or parallel owner.

## BLUEPRINT / ACTIVE STEP / PROOF
XS/SM/MD retain interleaved normal flow; LG/XL/2XL retain sticky centered stage
and shared Header usable-height token. All ID/EN/AR keep same content/DOM direction.
Implement one typography/shadow correction. Existing1900/1920 same-profile
before images preserved from the verified current source. Compare all3 stories
after; verify fluid scale across widths, reading measure, alignment/overflow,
Arabic fallback, no decorative mascot, global cursor unchanged.
Run focused PHP/Node/browser, Pint/build/structure/diff and required full PHP;
record actual results. No claimed browser/field result without evidence.

## GIT / NEXT VALID STEP
Local feat/home-v2-about only. Next: owner reviews the local typography/shadow result.

## PROOF / PROGRESS / STATUS
Headline/body both increase continuously across measured wide widths; screenshot
samples at1920:67.84/18.56px, headline680, body leading1.55, gap15.84px. These are
runtime samples of clamp/rem/vw sizing. Vision naturally wraps to two lines.
54 localized wide stories and18 menu cases PASS; centered media873.094x491.109
unchanged. Decorative mascot absent; cursor sources unchanged. Both shadow layers
have positive X/Y offsets; fluid scale, negative spread, no all-around halo.
PHP15/142,Node14, four composition andfive regression browser tests PASS;
build/structure262/Pint/diff PASS. Full PHP319/173 passed/71 failures/75 errors,
no new failure/error names.68 protected Header/Hero/JS/lang/Cursor/AR sources
byte-identical to entry; all media URLs unchanged except obsolete mascot key removed.
Evidence ../proof/about-type-status.md. Scoped STATUS: PASS; full repository suite
FAIL from existing legacy references. No push, merge or R2 object mutation.

## Latest OWNER_CONFIRMED — sharper shadow and story label
"buat agar lebih tajam shadow medinaya ... line ... vision ------ mision -----
about ----- ... agak besarin dikit, terlalu imut kayak catatan kaki"
Bounded patch: only existing About frame shadow and label CSS. Keep fluid type/
stage/locale/JS/media/cursor/Header unchanged. Reduce blur/increase shadow contrast
with the accepted right/down direction. Label slightly larger with fluid scale;
empty CSS pseudo-element draws a line after the text. Equal intrinsic grid tracks
make line length follow the translated label, with native RTL flow.
Assumption: the requested line matches the label's natural text width, rather
than spanning the entire copy column. This is a reversible visual tuning choice.
Proof: same1920 before/after Vision; all3 stories; label/line intrinsic width,
font growth, three locales/six tiers, centers/overflow and sharper positive shadow.
Proof PASS: matching label/line widths in54 wide ID/EN/AR story cells; label17px
at1920, fluid15.2–17px. Reduced contact/cast blur, stronger shadows preserve the
right/down light model. Geometry/type/body/media/Cursor/Header/JS/locale unchanged.
Nine browser tests,Node14,PHP15/142,build/structure262/diff PASS. Full PHP71 failures/
75 errors unchanged, no new names. Native mobile scroll test now centers its
target media: top alignment clipped Vision by.125px while Mission was fully
visible, so the accepted most-visible observer correctly preferred Mission.
No production observer changes. Evidence ../proof/about-label-status.md.
One next step: owner reviews local result. No push or merge.
