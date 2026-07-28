# Unified Text System — Session Handoff

Status: ACTIVE
Date: 2026-07-28
Branch: `main`
Repository: `Asyraf2003/schoolai`
Local checkout: `/home/asyraf/Code/laravel/school/schoolai`

This file is the primary continuation point for a new AI/session. Read it before doing new discovery. Do not repeat work already marked PASS unless a later code change invalidates the evidence.

## 1. Goal

Create one predictable semantic text presentation system for SchoolAI.

Target flow:

```text
DB / lang / Blade / JS content
            ↓
render location decides semantic role
            ↓
shared text-system tokens own typography
            ↓
locale adapter may adjust family/optical scale
            ↓
stable ID / EN / AR UI
```

`Seragam` means equivalent semantic hierarchy, not identical numeric font sizes.

Canonical roles:

- `display`
- `page-title`
- `section-title`
- `component-title`
- `subtitle`
- `body`
- `description`
- `label`
- `meta`
- `action`
- `longform`

Target marker later during implementation:

```html
<h2 class="section-title" data-text-role="section-title">...</h2>
```

Existing component classes stay for layout/behavior. `data-text-role` will own typography.

## 2. Hard workflow rules

Use this chain:

```text
FACT
→ GAP
→ GOAL
→ IMPACT
→ DECISION
→ EXECUTION
→ PROOF
→ STATUS
→ NEXT VALID STEP
```

Allowed STATUS values only:

- `PASS`
- `FAIL`
- `BLOCKED_BY_MISSING_EVIDENCE`

Rules:

- zero assumption;
- evidence first;
- one atomic surface/batch;
- one valid next action at a time when user CLI proof is required;
- no unrelated cleanup;
- no visual redesign during M00;
- no mass-delete legacy typography;
- no typography fields/classes in DB;
- no viewport-based typography logic in JS;
- admin remains intentionally desktop-only;
- use `rg` and `fd` rather than `grep` / `find` in CLI guidance;
- GitHub writes may go directly to `main` after proof;
- do not merge/audit old branches unless a concrete missing behavior requires it;
- do not install Playwright/Puppeteer merely for this audit;
- existing Brave CDP harness is already proven and must not be rediscovered.

## 3. Locked migration order

```text
M00 baseline text-role inventory
M01 shared text-system foundation
M02 shared navigation + hero
M03 homepage core
M04 homepage gallery/article/footer
M05 public gallery
M06 public article list + reader
M07 PPDB
M08 admin desktop
M09 article canvas/editor
M10 legacy cleanup + final audit
```

M00 is discovery/documentation only. No typography implementation yet.

## 4. Progress

### M00 public surfaces

PASS:

- homepage `/`, including navbar, Hero, About, core sections, homepage gallery/articles/footer;
- `/galeri`;
- `/artikel`;
- `/artikel/{article:slug}` native reader;
- `/ppdb`.

Therefore public M00 coverage corresponding to M02-M07 is **100%**.

### M08 admin desktop

PASS:

- protected admin route/provider tree discovered from current `main`;
- Dashboard, PPDB, non-canvas Articles, Gallery + nested section/media management, Statistics, Testimonials, and Hero admin entry views mapped;
- shared `layouts.admin` render tree mapped;
- major visible and accessibility-relevant text groups classified;
- Blade/lang/DB/controller/runtime/JS content sources separated;
- CSS ownership recorded, including the statistics `Buka`/`Tutup` CSS pseudo-content exception;
- admin chrome proven source-level to be forced Indonesian by `ForceAdminLocale`;
- desktop-only contract preserved;
- no M08 styling/implementation performed.

Authoritative evidence:

- `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_ADMIN.md`.

### Remaining M00 surface

Not yet baselined to PASS:

- M09 article canvas/editor UI.

For progress reporting, M00 is measured against the 8 implementation surface groups M02-M09. Seven of eight are baselined, so:

**M00 progress = 87.5%.**

### Whole Unified Text System project

M01-M10 implementation has not started. M00 itself is 87.5% complete.

Using milestone-level progress rather than pretending documentation equals implementation:

**overall project progress ≈ 8%.**

This is intentionally conservative.

## 5. Authoritative docs

Read these, in this order when needed:

1. `docs/architecture/UNIFIED_TEXT_SYSTEM_HANDOFF.md` — this continuation file.
2. `docs/architecture/UNIFIED_TEXT_SYSTEM_DOD.md` — workflow, roles, proof gates, migration order.
3. `docs/architecture/UNIFIED_TEXT_SYSTEM_RATIONALE.md` — architectural rationale.
4. `docs/architecture/UNIFIED_TEXT_SYSTEM_PREFLIGHT.md` — branch archaeology/preflight closure.
5. `docs/architecture/ARABIC_TYPOGRAPHY_REFACTOR.md` — Arabic adapter constraints.
6. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_HOME.md`.
7. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_HOME_HANDOFF.md`.
8. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_GALLERY.md`.
9. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_ARTICLES.md`.
10. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_PPDB.md`.
11. `docs/architecture/UNIFIED_TEXT_SYSTEM_M00_ADMIN.md`.

Do not create duplicate M00 docs for already-covered surfaces.

## 6. Important commits

- `e5f19131a481d979193588eaa03dcf97be16c599` — preflight closed, M00 allowed.
- `3a55341601c60a4b9468465797e19b6a93b0f195` — homepage M00 closure.
- `6307dd958934ed518fb19f3f81c5a71a38dc9791` — gallery M00 closure.
- `5aa76f9eb75c5a9e4da0ce9e9dd8c1a0116bda86` — articles/native reader M00 closure.
- `4bfc02e6c145b498e3520dbd53be90cc68b38af2` — PPDB M00 runtime closure.
- `111b8b5349e80e5eb292ef6eb08544ed360009cd` — M08 admin desktop M00 evidence closure.

Use current `main` as truth; commit list is historical orientation, not a reason to checkout old commits.

## 7. Browser/runtime proof infrastructure already established

No Python websocket packages are installed, but Node runtime provides native `WebSocket`.

Proven environment:

- Brave available at `/usr/local/bin/brave`;
- headless Chromium/CDP works on fixed port `9222`;
- CDP protocol reachable via Node native `WebSocket`;
- real language switch uses `POST /bahasa/{locale}` forms rendered in the navbar;
- temporary Brave profiles are used so user browser state is untouched.

Do not repeat capability detection.

Public proof widths:

- 390px;
- 768px;
- 1440px.

Locales:

- ID -> `ltr`;
- EN -> `ltr`;
- AR -> `rtl`.

For admin, respect current desktop-only product contract. Do not create mobile/tablet admin scope.

## 8. Baseline findings already proven

### Homepage

- ID and EN representative computed typography match numerically.
- Arabic adapter uses Cairo for heading/UI-like text and Lateef for prose/description.
- Arabic baseline has very large prose scales in several components, e.g. 36px descriptions and 44px subtitles in some contexts.
- These are later implementation targets, not M00 blockers.

### Gallery

- ID/EN parity proven at 390/768/1440.
- AR/RTL proven.
- Card partial does not currently render visual title/caption in the same way implied by some legacy CSS; do not invent dead UI from selectors alone.

### Articles / native reader

- Real native slug was discovered from rendered DOM, not guessed.
- ID/EN list and reader typography match numerically.
- Public page title has a non-monotonic responsive baseline: 60.48px at 1440, 33.6px at 768, 50.7px at 390.
- Native reader uses a distinct editorial/`longform` contract.
- AR reader uses Cairo for title/UI and Lateef for prose, with very large longform values: root 44px/83.6px and paragraphs 36px/63px in the sampled article.
- Some DB tags/categories remain Indonesian (`Pendidikan`, `Program`) in AR. This is content/localization evidence, not a typography role change.

### PPDB

Current runtime state:

- showcase exists for current DB data;
- desktop 1440 enables `ppdb-journey-native`;
- 768/390 do not, matching the existing `min-width: 901px` enhancement behavior;
- registration currently links directly to Google Forms in a new tab;
- guide currently links to `https://almustaqbal.sch.id/ppdb`;
- closed modal exists but is hidden in the current open-registration state.

ID/EN computed typography matches numerically at equivalent widths.

PPDB page title has the same non-monotonic baseline:

- 1440: 60.48px;
- 768: 33.6px;
- 390: 50.7px.

Arabic PPDB baseline:

- Cairo for many headings/actions;
- Lateef for prose;
- many generic prose nodes resolve to 36px/63px;
- PPDB-specific showcase selectors can override that and remain much smaller (e.g. 17.28px or 16px);
- showcase heading/card title retain negative letter-spacing in Arabic from PPDB component CSS;
- closed-modal action currently resolves to Lateef rather than the usual Cairo action family.

These inconsistencies are intentionally preserved as baseline evidence for later M07 implementation.

### Admin desktop

Source baseline from current `main` proves:

- admin route tree includes the five `routes/admin/*.php` modules plus provider-backed Testimoni and Hero admin routes;
- active M08 entry views use `layouts.admin`;
- admin chrome is forced to locale `id` by `ForceAdminLocale`;
- English/Arabic are content-entry languages in selected admin forms, not current admin-shell locales;
- Dashboard statistic values map to `display`; page/section/card/form/action/meta groups are mapped in the dedicated evidence doc;
- topbar descriptions and several panel/card description selectors are source-present but currently hidden by scoped admin CSS and therefore are not counted as visible/a11y text groups;
- shared JS creates/replaces limited preview/toast/modal text but contains no typography logic;
- statistics currently emits `Buka` / `Tutup` as CSS pseudo-content;
- current CSS ownership is layered but materially resolvable from source specificity/order, so M08 did not require a redundant browser `getComputedStyle()` command;
- M09 canvas/editor internals were not audited as part of M08.

These are later M08 implementation targets/evidence, not reasons to change code during M00.

## 9. What is NOT yet done

Do not claim any of the following has started:

- no `resources/css/text-system.css` foundation;
- no `data-text-role` rollout;
- no Arabic selector cleanup;
- no legacy typography deletion;
- no M01 implementation;
- no article canvas/editor M00 baseline;
- no final build/test gates for implementation because implementation has not started.

M08 admin desktop M00 **is complete and PASS**. Do not rediscover it unless later code changes invalidate `UNIFIED_TEXT_SYSTEM_M00_ADMIN.md`.

## 10. Exact next scope

Continue **M00 with M09 article canvas/editor UI only**.

Goal for the next session:

1. start from the existing article canvas routes/controllers/views on current `main`;
2. map the canvas/editor render tree without re-auditing non-canvas Article admin surfaces already covered by M08;
3. classify every major visible/accessibility-relevant editor text group using the canonical roles;
4. distinguish editor document content (`longform` where applicable) from toolbar/action/label/meta UI;
5. identify Blade / lang / DB / controller/runtime / JS content sources;
6. identify CSS ownership and use runtime `getComputedStyle()` only where source cannot establish the winning behavior;
7. respect the current desktop admin product contract;
8. persist a dedicated M00 canvas/editor evidence doc;
9. mark M09 PASS only when no major visible editor text group remains unclassified.

Do not start M01 until M09 baseline is PASS.

## 11. First action for a new AI session

Read this handoff and the DOD from `main`, then inspect the current M09 article canvas/editor entry points using the GitHub connector.

Do not:

- repeat homepage, gallery, article list/native reader, PPDB, or M08 admin source/runtime audits;
- ask the user to repeat evidence already stored in the authoritative M00 docs;
- rediscover the Brave/CDP capability;
- start M01 styling before M09 PASS.

Use GitHub for repository facts first. Ask for one CLI/runtime proof only when GitHub/source evidence cannot establish the winning runtime behavior.
