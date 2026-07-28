# Unified Text System — Workflow & Definition of Done

Status: ACTIVE
Date: 2026-07-29
Target branch: `main`
Scope owner: visible text presentation across SchoolAI UI

## 1. Goal

Create one predictable text presentation system so visible text is styled by semantic role, not by content source, HTML tag, or accidental component CSS.

Required result:

- titles remain title roles;
- body copy remains body copy;
- descriptions remain descriptions;
- labels, metadata, actions, and long-form text keep stable hierarchy;
- DB/lang/Blade/JS content uses the same role contract;
- public 390/768/1440 layouts preserve hierarchy;
- ID/EN/AR preserve equivalent hierarchy;
- layout, animation, media, routes, controllers, models, DB behavior, and editor behavior stay unchanged unless typography proof requires a narrow change.

`Seragam` means equivalent semantic hierarchy, not identical numeric font sizes everywhere.

## 2. Core decision

```text
DB / lang / Blade / JS content
            ↓
render location decides semantic role
            ↓
shared text-system tokens own typography
            ↓
locale adapter may adapt family/tracking/direction
            ↓
component CSS owns layout, not arbitrary typography
```

A DB title is not automatically a page title.

Example:

- Article title in reader → `page-title`;
- same Article title in a card → `component-title`;
- same Article title in Hero → `display`.

The database stores content, not presentation.

## 3. Canonical semantic roles

Use only these roles unless a proven product requirement forces an addition:

- `display` — strongest display text, Hero title, major editorial display;
- `page-title` — primary standalone page/document title;
- `section-title` — primary heading introducing a section;
- `component-title` — card/panel/local component heading;
- `subtitle` — supporting lead attached to a display/page/section heading;
- `body` — ordinary explanatory prose;
- `description` — secondary descriptive copy attached to an entity/component;
- `label` — short identifying UI text, eyebrow, badge, chip, field label;
- `meta` — low-emphasis factual metadata;
- `action` — navigation/button/CTA wording;
- `longform` — primary reading/editor body content.

## 4. HTML contract

HTML semantics remain meaningful, but typography does not depend on tag name.

Target marker:

```html
<h2 data-text-role="section-title">...</h2>
<p data-text-role="subtitle">...</p>
<span data-text-role="component-title">...</span>
<a data-text-role="action">...</a>
```

Rules:

- existing component classes stay for layout/behavior;
- `data-text-role` expresses typography ownership;
- role is determined by render context;
- do not add typography fields/classes to DB content;
- do not rename unrelated component classes merely for consistency.

## 5. CSS ownership

Shared source:

```text
resources/css/text-system.css
```

The semantic system owns for migrated text:

- `font-family`;
- `font-size`;
- `font-weight`;
- `line-height`;
- `letter-spacing`.

Component CSS may still own:

- color;
- width/max-width;
- alignment;
- margin/padding;
- positioning;
- shadows/decoration;
- animation/transform;
- intentional truncation.

Do not mass-delete legacy typography.

For each migrated component:

1. identify the real computed winner when ambiguous;
2. assign the semantic role;
3. make the shared system win;
4. remove only conflicting/redundant typography declarations;
5. verify runtime again.

## 6. Locale contract

### Indonesian and English

Use the shared role hierarchy and common tokens unless measured evidence requires a narrow exception.

### Arabic

Arabic is a locale adapter, not a second typography system.

Current approved contract:

- Cairo is the only approved Arabic runtime family;
- both Arabic family aliases may remain, but both resolve to Cairo;
- semantic role tokens own size, weight, and line-height;
- Arabic may normalize `letter-spacing`, RTL behavior, and direction-sensitive details;
- Arabic must not maintain a parallel optical type scale;
- no Lateef/Naskhi compensation values may be used as implementation targets;
- no blanket Arabic body/lead/feature enlargement;
- any numeric Arabic exception must be narrow, measured, documented, and proven necessary.

Historical M00 references to Lateef or oversized Arabic values remain valid only as baseline evidence of the old state. They are non-normative.

Canonical Arabic contract:

`docs/architecture/ARABIC_TYPOGRAPHY_REFACTOR.md`

If any older document conflicts with the Cairo-only decision, this DOD and the Arabic contract win.

## 7. JS contract

JS must not invent typography.

- if JS replaces `textContent`, destination DOM must already have the correct role;
- if JS creates visible text nodes, it must assign the approved role;
- JS may toggle state classes;
- JS must not switch typography roles or sizes by viewport.

Responsive typography belongs to CSS.

## 8. DB contract

Forbidden:

- `font_size` columns;
- `font_family` columns;
- CSS class columns;
- breakpoint-specific presentation fields;
- per-record typography role fields for ordinary content.

Render context owns presentation role.

## 9. Responsive contract

Public proof widths:

- 390px;
- 768px;
- 1440px.

Same semantic role remains the same role at all widths.

Allowed:

- fluid/clamped shared sizes;
- responsive line-height when justified;
- component width/wrapping differences.

Not allowed:

- changing role just to fit mobile;
- arbitrary per-component sizes for the same migrated role;
- JS viewport typography;
- clipping/overflow caused by the role system.

Admin remains desktop-only unless separately approved.

## 10. Execution workflow

Every batch follows:

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

### FACT

Use repository/runtime evidence only.

### GAP

State only what is not proven. Do not infer cascade winners when runtime proof is needed.

### GOAL

Name the semantic role and intended consistency outcome.

### IMPACT

Consider:

- ID/EN/AR;
- LTR/RTL;
- 390/768/1440;
- wrapping/component dimensions;
- admin/editor surfaces if shared CSS is touched.

### DECISION

Choose the smallest change that makes the role obey the shared contract.

### EXECUTION

One atomic surface/batch. No unrelated redesign or cleanup.

### PROOF

Automated and runtime proof are required before PASS.

### STATUS

Only:

- `PASS`;
- `FAIL`;
- `BLOCKED_BY_MISSING_EVIDENCE`.

### NEXT VALID STEP

Do not start the next milestone before current milestone PASS.

## 11. Locked migration order

### M00 — Baseline inventory

Status: PASS.

Historical measurements describe the old runtime and are not automatically target values.

### M01 — Shared text-system foundation

Status: PASS.

Shared semantic tokens and `data-text-role` rules exist and load before the Arabic adapter.

### M02 — Shared navigation + Hero

Status: ACTIVE.

Migrate and prove:

- navbar/mega-menu visible text;
- language modal text;
- Hero eyebrow;
- Hero title;
- Hero description;
- Hero CTA where present.

Required ID/EN/AR proof at 390/768/1440.

### M03 — Homepage core

- about/statistics;
- vision/mission;
- school values;
- featured programs.

### M04 — Homepage gallery/article/footer

- gallery headings/captions/meta/lightbox;
- article digest headings/titles/descriptions/meta;
- footer headings/body/labels/actions.

### M05 — Public gallery

All visible roles on `/galeri`.

### M06 — Public article list + reader

`/artikel` and `/artikel/{article:slug}`, including long-form hierarchy.

### M07 — PPDB

Public PPDB text roles without changing journey/layout behavior.

### M08 — Admin desktop

Same semantic role contract at supported desktop surface only.

### M09 — Article canvas/editor

Editor/canvas roles while preserving document-language behavior.

### M10 — Legacy cleanup + final audit

Only after M02-M09 pass:

- remove proven redundant typography declarations;
- remove obsolete selector duplication;
- remove dead typography dependencies/imports;
- remove compatibility shims no longer needed;
- retain documented exceptions;
- perform repository-wide final audit.

## 12. Required proof for every batch

### Source/diff

```bash
git diff --check
git status --short
```

No unrelated changes are accepted.

### Structure/build

```bash
npm run check:structure
npm run build
```

### Application tests

```bash
php artisan test
```

Focused tests may be used during iteration, but final milestone closure requires the relevant/full suite to be green.

### Runtime typography

Use browser `getComputedStyle()` for representative changed roles.

Record:

- role;
- selector/node;
- locale;
- viewport;
- `font-family`;
- `font-size`;
- `font-weight`;
- `line-height`;
- `letter-spacing`.

`rg`/`fd` locate source declarations but do not prove cascade winners.

### Visual behavior

For changed public surfaces verify:

- 390/768/1440;
- ID/EN/AR;
- no clipping;
- no horizontal text overflow;
- no unintended layout jump;
- buttons/cards remain usable;
- intentional clamps still behave correctly.

## 13. Definition of Done — per surface

A surface is DONE only when:

1. visible text groups are classified;
2. migrated nodes have explicit auditable roles;
3. DB/lang/Blade/JS source does not alter role styling;
4. typography resolves from shared tokens or approved locale adapter;
5. no DB presentation fields were added;
6. no JS viewport typography was added;
7. conflicting legacy declarations are removed or documented;
8. ID/EN hierarchy is equivalent;
9. AR hierarchy is equivalent and resolves to Cairo under the current contract;
10. RTL has no new clipping/overflow;
11. public surface passes 390/768/1440 proof;
12. layout/animation/media/business behavior is preserved;
13. structure check passes;
14. Vite build passes;
15. relevant tests pass;
16. diff check passes;
17. diff has no unrelated cleanup;
18. runtime computed-style evidence is recorded;
19. user feedback for the batch is resolved.

## 14. Definition of Done — project final

Project is DONE only when:

- M00-M10 are PASS;
- all public routes and shared navigation/footer are covered;
- homepage sections are covered;
- DB-backed Article/GalleryItem/HeroSlide paths are covered;
- JS-populated visible text paths are covered;
- admin supported desktop UI is covered;
- article reader/canvas is covered;
- ID/EN/AR hierarchy is consistent;
- Arabic runtime no longer depends on Lateef/Naskhi compensation;
- public width evidence is complete;
- no migrated role depends on accidental component CSS order;
- no presentation styling leaked into DB fields;
- exceptions are few, intentional, documented, and measured;
- full PHP tests pass;
- Vite build passes;
- structure check passes.

## 15. Anti-overengineering rules

Forbidden unless a later proven problem requires them:

- rewriting all Blade components at once;
- replacing the CSS architecture wholesale;
- Tailwind migration merely for typography;
- adding a design-system package;
- typography tables/DB configuration;
- moving content between DB/lang merely for styling;
- renaming every component class;
- changing animation/media/layout while touching text;
- introducing a new visual-test framework without need;
- unrelated CSS archaeology;
- controller/model refactors where content behavior is already correct.

Goal: consistent text-role behavior, not architectural purity.

## 16. Batch report template

```text
BATCH: Mxx / surface

FACT
- ...

GAP
- ...

GOAL
- ...

IMPACT
- ...

DECISION
- ...

EXECUTION
- exact files changed
- exact semantic roles migrated

PROOF
- structure: PASS/FAIL
- build: PASS/FAIL
- tests: PASS/FAIL
- diff-check: PASS/FAIL
- locale proof: PASS/FAIL
- responsive proof: PASS/FAIL
- computed-style proof: PASS/FAIL

STATUS
- PASS / FAIL / BLOCKED_BY_MISSING_EVIDENCE

NEXT VALID STEP
- exactly one next batch/action
```

No next implementation milestone starts while the current milestone is not PASS.
