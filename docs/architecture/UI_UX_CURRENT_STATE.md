# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Source main before correction: `d954427cfee91575e32540a3c7fb74bca00718a5`
Gallery correction source before ledger: `4d22732e3d59c15a194d39ead6711b6bd5e47b39`

Commit publication proves source state only. It does not prove rendering,
responsive parity, accessibility, performance, or browser lifecycle.

## Active production batch

Blueprint: `blueprints/2026-08-03-home-depth-gallery.md`

- ID: `HOME-GALLERY-003`
- State: `IMPLEMENTING`
- Surface: homepage Gallery only
- Reference: Houmahani Kane / Codrops Atmospheric Depth Gallery
- Runtime evidence: two owner desktop Chromium screenshots, 2026-08-03
- Protected: Hero, Vision/Mission, Values, Programs, Articles, navigation,
  footer, `/galeri`, translations, DB/admin, dependencies, and existing media.

## Runtime FACT

The owner screenshots prove the previous Gallery result was still visually
incorrect:

- oversized white copy remained a competing focal object;
- active composition occupied too much viewport width;
- alternating/even items were displaced too far;
- a straight progress bar appeared instead of the reference-like thin trail;
- media-first reference hierarchy was not preserved.

## Owner correction

- Media is the focal object.
- Visible copy is title plus optional description only.
- Copy is small, black, ordinary body typography, and adjacent to media.
- Visible number/type/date/category treatment is removed.
- Image corners remain square and intrinsic ratio remains unchanged.
- Alternating placement is subtle rather than pushed across the viewport.
- A thin curved path reveals with scroll progress.
- Palette changes remain synchronized between DOM fallback and WebGL.
- The same semantic component must adapt across all six tiers and RTL/LTR.

## Corrected source contract

- Blade retains one anchor per media item with semantic image/title/caption.
- The old eyebrow block is removed from rendered Gallery markup.
- Media and copy remain siblings; copy never overlays image.
- CSS uses auto image dimensions, max width/height, contain fit, and zero radius.
- Label title is fluid around 14–17px; caption remains smaller.
- Scene horizontal displacement is `viewportWidth * 0.032`, clamped to 12–54px.
- Card/item width is reduced at each tier instead of approaching full viewport
  width on desktop.
- SVG trail uses one normalized path and `stroke-dashoffset` driven by existing
  scene progress.
- Former bottom meter markup and CSS are removed.
- One RAF still owns scene, palette, and trail updates.
- Reduced motion keeps a static sequence and complete trail.

## Six-tier source contract

| Tier | Source rule |
|---|---|
| XS 360–639 | stacked media/copy; card viewport-bounded |
| SM 640–767 | adjacent composition; item <=780px |
| MD 768–1023 | adjacent composition; item <=840px |
| LG 1024–1279 | item <=900px |
| XL 1280–1535 | item <=960px |
| 2XL >=1536 | item <=1020px |

One DOM/controller/canvas/trail serves every tier. RTL mirrors only the
media/copy order and logical alignment.

## Source proof status

| Gate | Status | Evidence |
|---|---|---|
| Screenshot diagnosis | `PASS` | owner runtime screenshots supplied |
| Reference hierarchy | `PASS_SOURCE` | media dominant; only small adjacent title/caption |
| Visible metadata cleanup | `PASS_SOURCE` | no eyebrow/number/type/date markup in scene |
| Intrinsic media | `PASS_SOURCE` | auto dimensions, contain fit, max bounds, square corners |
| Even-item spacing | `PASS_SOURCE` | horizontal displacement capped at 54px |
| Thin trail | `PASS_SOURCE` | SVG curve plus normalized scroll reveal |
| Palette progression | `PASS_SOURCE` | DOM and WebGL share blended camera palette |
| Six tiers | `PASS_SOURCE` | fluid XS plus 640/768/1024/1280/1536 boundaries |
| RTL architecture | `PASS_SOURCE` | one DOM and logical order mirroring |
| Lifecycle | `PASS_SOURCE` | one RAF, proximity activation, hidden/offscreen suspension |
| Dependency scope | `PASS_SOURCE` | no package or lockfile changes |
| Focused PHP test | `BLOCKED_BY_MISSING_EVIDENCE` | contract updated but not executed locally |
| `npm run check:structure` | `FAIL_PRE_EXISTING` | prior Hero checksum mismatch remains unrelated |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | not run after this correction |
| Full PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | not run after this correction |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | corrected source not yet visually tested |
| Lighthouse/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | no comparable run |

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G01 screenshot/root-cause audit | `PASS` | visual hierarchy and spacing defect identified |
| G02 reference source audit | `PASS` | labels, planes, trail, and mood behavior inspected |
| G03 owner correction | `PASS` | media-first, small black copy, trail explicitly required |
| G04 semantic markup | `IMPLEMENTED_SOURCE` | title/caption only; metadata removed from view |
| G05 compact composition | `IMPLEMENTED_SOURCE` | reduced tier widths and 54px max displacement |
| G06 trail | `IMPLEMENTED_SOURCE` | responsive SVG path bound to scene progress |
| G07 palette/lifecycle | `IMPLEMENTED_SOURCE` | existing bounded renderer retained |
| G08 focused contract test | `IMPLEMENTED_SOURCE` | source assertions updated; execution pending |
| G09 automated proof | `BLOCKED_BY_MISSING_EVIDENCE` | local commands unavailable in connector |
| G10 runtime matrix | `BLOCKED_BY_MISSING_EVIDENCE` | corrected six-tier/browser proof pending |

## STATUS

The requested media-first correction is implemented in source and prepared for a
non-force fast-forward to `main`. It is not yet runtime PASS. Browser proof must
confirm that label scale, even-item spacing, trail visibility, palette changes,
and all six tier compositions match the accepted direction.

## NEXT VALID STEP

After publication, pull current `main` and run only the focused Gallery test.
Then provide one desktop screenshot at the same viewport used for the current
runtime evidence before testing the remaining five tier boundaries.
