# Unified Text System — Workflow & Definition of Done

Status: workflow locked, implementation not started  
Target branch: `main`  
Baseline audited: `0a424e655401ba9c47c759d673e9dabcaa79108e`  
Scope owner: visible text presentation across SchoolAI UI  

## 1. Goal

Create one predictable text presentation system for SchoolAI so that visible text is styled by its semantic role, not by where the string came from or by an accidental component-specific CSS declaration.

Required result:

- a header/title remains a header/title role;
- normal body copy remains body copy;
- description copy remains description copy;
- labels, metadata, CTA text, long-form content, and statistics each keep their intended hierarchy;
- DB-sourced text, translation-sourced text, Blade/static text, and JS-populated text use the same role contract;
- public mobile, tablet, and desktop retain the same semantic hierarchy even when responsive numeric sizes differ;
- Indonesian, English, and Arabic use equivalent hierarchy while allowing language-specific typography;
- existing layout, animation, media behavior, business logic, routes, controllers, models, and DB behavior are not changed unless a proven text-normalization requirement makes it necessary.

`Seragam` means the same semantic contract and hierarchy. It does **not** mean every text node has the same font size.

## 2. Verified Current Facts

### 2.1 Public entry points

`resources/views/welcome.blade.php` loads:

- `resources/css/pages/welcome.css`;
- `resources/css/pages/welcome-hero.css`;
- `resources/css/pages/welcome-about-stats.css`;
- `resources/css/arabic-typography.css`;
- homepage JS entries.

`resources/views/layouts/public.blade.php` loads shared public CSS/JS and adds PPDB assets on the PPDB route.

Public routes currently include:

- `/`;
- `/ppdb`;
- `/artikel`;
- `/artikel/{article:slug}`;
- `/galeri`.

### 2.2 CSS typography is currently distributed through cascades

`resources/css/pages/welcome.css` imports 47 CSS modules.

`resources/css/pages/welcome-hero.css` imports 9 hero CSS modules.

Examples of current drift:

- `.section-title` has a base definition in the first welcome stylesheet;
- later scoped CSS overrides `.section-title` sizing again;
- hero mobile title/description sizes are defined separately;
- component-specific selectors independently assign font sizes, weights, line heights, and letter spacing.

Therefore the normalization problem is primarily a cascade/ownership problem, not a DB problem.

### 2.3 The same semantic role already appears through different HTML tags

Examples:

- section titles use `h2.section-title`;
- vision titles use `h3`;
- program card titles use `span.program-card__title` because the card itself is a button;
- article card titles also use spans in interactive structures;
- descriptions may be `p` or `span` depending on component structure.

Typography therefore cannot be mapped only by tag name (`h1`, `p`, `span`, etc.).

### 2.4 Text comes from multiple sources

Verified DB-backed text includes:

`Article`
- title ID/EN/AR;
- subtitle ID/EN/AR;
- description ID/EN/AR;
- content ID/EN/AR;
- tags and author-related presentation.

`GalleryItem`
- title ID/EN/AR;
- category ID/EN/AR;
- caption ID/EN/AR.

`HeroSlide`
- media alt ID/EN/AR;
- eyebrow ID/EN/AR;
- title ID/EN/AR;
- description ID/EN/AR;
- CTA label ID/EN/AR.

A hero slide can also derive title and description from an `Article`. The same DB record can therefore appear in multiple presentation contexts.

### 2.5 JS can populate visible text

Example: `resources/js/pages/welcome/public-content.js` copies gallery title/caption data into lightbox DOM nodes via `textContent`.

JS must not invent typography. The destination DOM node must already have a semantic text role, or JS must explicitly assign the approved semantic role when it creates a new visible text node.

### 2.6 Arabic already has a dedicated typography architecture

Existing files:

- `resources/css/arabic-typography.css`;
- `resources/css/arabic-typography-base.css`;
- `resources/css/arabic-type-scale.css`.

Existing architecture guide:

- `docs/architecture/ARABIC_TYPOGRAPHY_REFACTOR.md`.

That work already establishes an important rule: typography follows semantic role, not HTML tag, and equivalent hierarchy does not require identical numeric sizes.

The unified text system must extend that principle to all locales. It must not create a second competing Arabic system.

### 2.7 Admin is intentionally desktop-only today

`resources/views/layouts/admin.blade.php` uses `viewport width=1200` and an explicit PC-only state.

Therefore:

- public UI must be proven at mobile, tablet, and desktop widths;
- admin UI must use the same text-role contract at its currently supported desktop surface;
- making admin responsive is a separate product/layout change and is outside this typography normalization unless explicitly approved later.

### 2.8 Existing validation commands

The repository already provides:

- `npm run check:structure`;
- `npm run build`;
- `composer test` / `php artisan test`;
- source-structure verification before Vite build.

These become mandatory proof gates, not optional cleanup steps.

## 3. Scope

### Included

All human-visible or accessibility-relevant UI text rendered by the application, including text sourced from:

- Blade templates;
- translation files under `lang/`;
- database models;
- controller/provider presentation arrays;
- JS `textContent`, generated labels, modal/lightbox content, and other visible JS-created nodes;
- public pages;
- article reader;
- admin UI at its supported desktop surface;
- article canvas/editor UI where text presentation is part of the product.

### Excluded unless separately approved

- route names;
- PHP/JS variable names;
- database column names;
- log text not presented to users;
- comments and developer-only strings;
- media/layout/animation redesign;
- DB schema additions for typography;
- storing font names, font sizes, CSS classes, or presentation roles inside content tables;
- converting the current admin product into a responsive mobile/tablet admin.

## 4. Core Decision

The source of truth will be a **semantic text role contract**.

Content source and presentation role are separate concerns:

```text
DB / lang / Blade / JS content
            ↓
render location decides semantic text role
            ↓
shared text-system tokens resolve typography
            ↓
locale adapter may adjust family/optical scale
            ↓
component layout may position the text, but may not redefine its typography arbitrarily
```

A DB title does not automatically mean `page-title`.

Example:

- Article title on article reader → `page-title`;
- same Article title in homepage card → `component-title`;
- same Article title in hero → `display`.

This prevents styling from leaking into the data layer.

## 5. Canonical Semantic Roles

Use the smallest role set that describes actual product hierarchy.

### `display`

Use for the strongest visual display text.

Examples:

- homepage hero title;
- major campaign/editorial display;
- large statistic value when used as display typography.

### `page-title`

Primary title of a standalone page/document.

Examples:

- article reader title;
- gallery page title;
- PPDB page title when structurally applicable.

### `section-title`

Primary heading introducing a page section.

Examples:

- vision/mission section heading;
- featured-program section heading;
- gallery section heading;
- article section heading.

### `component-title`

Heading/title inside a card, panel, item, or local component.

Examples:

- program card title;
- vision card title;
- article digest card title;
- gallery story title;
- footer group heading where it functions as a local heading.

### `subtitle`

Supporting lead text immediately attached to a display/page/section heading.

Examples:

- section subtitle;
- page lead/subtitle;
- program spotlight subtitle when it behaves as supporting lead.

### `body`

Ordinary explanatory prose.

Examples:

- normal paragraphs;
- ordinary card copy that is not intentionally secondary description metadata.

### `description`

Secondary descriptive/explanatory copy attached to an entity or component.

Examples:

- hero description;
- article card description;
- program description;
- gallery caption when used as descriptive prose;
- footer brand description.

### `label`

Short identifying UI text.

Examples:

- eyebrow/kicker;
- badge;
- chip;
- field label;
- compact interface label.

### `meta`

Low-emphasis factual metadata.

Examples:

- date;
- category metadata;
- reading time;
- indexes/issue numbers;
- byline metadata.

### `action`

Interactive action wording.

Examples:

- buttons;
- CTA links;
- navigation action text.

### `longform`

Primary reading/editor content requiring long-form rhythm.

Examples:

- native article body;
- article canvas body content.

## 6. HTML Contract

### 6.1 HTML semantics remain meaningful

Use the correct HTML element whenever the structure permits it:

- one meaningful `h1` for page/document primary title;
- `h2` for sections;
- `h3`/lower levels for nested headings;
- `p` for prose;
- `button`/`a` for actions.

But typography role must not depend on the tag alone.

Interactive components may legitimately contain title text in a `span`; that title still receives the `component-title` role.

### 6.2 Canonical role marker

Target markup contract:

```html
<h2 class="section-title" data-text-role="section-title">...</h2>
<p class="section-subtitle" data-text-role="subtitle">...</p>
<span class="program-card__title" data-text-role="component-title">...</span>
<span class="artikel-digest__meta" data-text-role="meta">...</span>
```

Rules:

- existing component classes remain for layout/behavior;
- `data-text-role` expresses typography ownership;
- a visible text node gets the role of how it is used at that render location;
- do not put styling semantics into DB columns;
- do not rename unrelated component classes merely for typography consistency.

This marker is intentionally small and auditable. It avoids creating another parallel component framework.

## 7. CSS Contract

### 7.1 One shared text-system layer

Create one common typography ownership layer during implementation, with a focused source file such as:

```text
resources/css/text-system.css
```

Its responsibility is limited to:

- shared semantic typography tokens;
- `data-text-role` role rules;
- responsive type-scale values;
- no component layout;
- no media positioning;
- no animation;
- no component-specific colors unless the role itself requires an approved global behavior.

Keep source files within the repository structure/size rules.

### 7.2 Typography properties owned by the semantic system

After a component has been migrated, these properties for its text role must resolve from the shared role system or an approved locale adapter:

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
- text shadow;
- decoration;
- truncation only when explicitly required by the component behavior.

### 7.3 Legacy declarations are removed only after proof

Do not mass-delete all old font declarations.

For each migrated component:

1. identify the computed declaration currently winning;
2. map the text to a semantic role;
3. add the role marker;
4. make the shared role layer resolve the intended typography;
5. verify runtime output;
6. remove only now-redundant/conflicting legacy typography declarations;
7. verify again.

No archaeological CSS cleanup without direct relation to the text-system goal.

## 8. Locale Contract

### Indonesian and English

Use the shared role hierarchy and common base tokens unless a measured language-specific exception is required.

### Arabic

Reuse the existing Cairo/Lateef architecture.

The existing Arabic files become the Arabic adapter for the same semantic roles, rather than a competing selector map.

Required behavior:

- display/heading/UI-like roles use the approved Arabic display family;
- prose/description/long-form roles use the approved Arabic body family;
- Arabic may use optically different numeric sizes and line heights;
- semantic hierarchy must remain equivalent to ID/EN;
- RTL must not clip or overflow;
- do not silently rewrite Arabic content.

`docs/architecture/ARABIC_TYPOGRAPHY_REFACTOR.md` remains valid for Arabic-specific evidence where it does not conflict with this unified contract.

## 9. JS Contract

JS must not contain component-specific font sizing logic.

For visible dynamic text:

- if JS only replaces `textContent`, the destination node must already have the correct `data-text-role`;
- if JS creates a visible node, it must create/assign the approved role marker;
- JS may toggle state classes but must not switch semantic typography roles merely because viewport size changes;
- responsive typography belongs to CSS tokens, not JS viewport branching.

## 10. DB Contract

The database stores content, not presentation.

Forbidden as part of this normalization:

- new `font_size` columns;
- `font_family` columns;
- CSS class columns;
- breakpoint-specific content styling fields;
- per-record typography role fields for ordinary content.

The render context owns the role.

A DB value must be able to appear in multiple components without carrying stale styling assumptions from another surface.

## 11. Responsive Contract

Public validation widths:

- mobile: 390px;
- tablet: 768px;
- desktop: 1440px.

The same role must remain the same role at all widths.

Allowed:

- fluid/clamped size changes;
- responsive line-height adjustments when justified;
- component width/line-wrap differences.

Not allowed:

- a `description` becoming `meta` on mobile simply to make it fit;
- independent per-component arbitrary font-size overrides for the same semantic role;
- JS changing typography based on viewport;
- clipping/overflow caused by the role system.

Existing intentional truncation, such as a component-specific line clamp, must be treated as a documented component behavior and verified rather than silently removed or expanded.

## 12. Execution Workflow

Every implementation batch must follow this exact evidence chain:

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

Only repository/runtime evidence.

Record:

- route/surface;
- exact file(s);
- exact selectors/markup;
- current source of visible text;
- current computed typography where relevant;
- current responsive/locale behavior.

### GAP

State only what is not yet proven.

Do not infer missing runtime behavior from CSS source when cascade order can change the result.

### GOAL

Name the semantic role and the exact intended consistency outcome.

### IMPACT

Before changing code, list what can be affected:

- ID/EN/AR;
- LTR/RTL;
- mobile/tablet/desktop;
- layout wrapping;
- interactive component sizing;
- admin/editor surfaces if shared CSS is touched.

### DECISION

Choose the smallest change that makes the role obey the shared contract.

### EXECUTION

One atomic surface/batch only.

Do not combine unrelated layout redesign, animation changes, DB changes, or cleanup.

### PROOF

Run automated and runtime proof before accepting the batch.

### STATUS

Mark only:

- `PASS`;
- `FAIL`;
- `BLOCKED_BY_MISSING_EVIDENCE`.

### NEXT VALID STEP

Only after current batch is PASS.

## 13. Migration Order

The order is designed to reduce blast radius and avoid re-auditing the whole repository after every tiny edit.

### M00 — Baseline inventory

No styling changes.

Produce a text-role inventory for rendered surfaces:

- source path;
- route/surface;
- selector/element;
- content source: Blade / lang / DB / JS;
- proposed semantic role;
- locale applicability;
- responsive applicability;
- current winning computed typography where needed.

M00 is complete only when no major visible text group is unclassified.

### M01 — Shared text-system foundation

Create the shared semantic role/token layer and load it without migrating unrelated components.

Goal: foundation exists with no accidental visual rewrite.

### M02 — Shared navigation + hero

Migrate:

- navbar/mega-menu visible text;
- hero eyebrow;
- hero title;
- hero description;
- hero CTA/control labels where applicable.

Verify ID/EN/AR and public 390/768/1440 widths.

### M03 — Homepage core content

Migrate:

- about/statistics;
- vision/mission;
- school values;
- featured programs.

### M04 — Homepage gallery + article digest + footer

Migrate:

- gallery headings/captions/meta/lightbox text;
- article digest headings/titles/descriptions/meta;
- footer headings/body/labels/actions.

JS-populated lightbox text must be covered here.

### M05 — Public gallery page

Migrate all visible title/description/caption/meta/action roles on `/galeri`.

### M06 — Public article list + native reader

Migrate:

- `/artikel`;
- `/artikel/{article:slug}`;
- article long-form hierarchy.

DB article content must prove context-specific roles rather than DB-bound styling.

### M07 — PPDB public page

Migrate PPDB visible text roles without changing journey/layout behavior.

### M08 — Admin desktop UI

Migrate admin visible text to the same semantic role contract while preserving current desktop-only product behavior.

Do not introduce mobile/tablet admin redesign.

### M09 — Article canvas/editor

Migrate editor/canvas text roles and preserve document-language behavior.

### M10 — Legacy typography cleanup + final audit

Only after all surfaces pass:

- remove proven redundant component typography declarations;
- remove obsolete role selector duplication;
- retain documented exceptions;
- perform repository-wide final role audit.

## 14. Proof Required for Every Batch

### Source proof

Show exact changed files and diff scope.

Required:

```bash
git diff --check
git status --short
```

No unrelated file changes are accepted.

### Structure/build proof

Required:

```bash
npm run check:structure
npm run build
```

### Application test proof

Required:

```bash
composer test
```

If a batch touches only a narrowly testable area, focused tests may be run during iteration, but the batch is not final PASS until the required relevant suite and final full suite are green.

### Runtime typography proof

Use browser `getComputedStyle()` on representative nodes for each role changed in the batch.

Record at minimum:

- semantic role;
- selector/node;
- locale;
- viewport;
- resolved `font-family`;
- resolved `font-size`;
- resolved `font-weight`;
- resolved `line-height`;
- resolved `letter-spacing`.

Source search (`rg`/`fd`) can find declarations but does not prove which declaration wins.

### Visual behavior proof

For public surfaces changed in the batch, verify:

- 390px;
- 768px;
- 1440px;
- Indonesian;
- English;
- Arabic/RTL.

Verify:

- hierarchy is preserved;
- no clipping;
- no horizontal text overflow;
- no accidental layout jump caused by typography;
- buttons/cards remain usable;
- intentional line clamps behave as documented;
- content from DB has the same role styling as equivalent non-DB content.

## 15. Automated Regression Expectations

Add/adjust focused tests when the migration makes a contract testable.

High-value tests include:

- public page contains the expected `data-text-role` marker for section/page/component roles;
- DB-backed article title renders under the expected role on hero/card/reader contexts;
- DB-backed gallery title/caption renders under the expected role;
- JS lightbox target nodes carry stable role markers before text injection;
- Arabic page continues to render `lang="ar"` and RTL contract;
- no controller/model test changes merely to satisfy presentation refactor.

Do not build a large new visual-regression framework unless existing proof becomes insufficient.

## 16. Definition of Done — Per Surface

A surface is DONE only when all are true:

1. Every visible text group on that surface is classified into an approved semantic role.
2. Every migrated text node has an explicit auditable role contract.
3. DB/lang/Blade/JS source does not change the role styling rules.
4. Typography properties resolve from the shared role system or approved locale adapter.
5. No new DB presentation fields were added.
6. No JS viewport-based typography logic was added.
7. Legacy component typography declarations that conflict with the role are removed or documented as required exceptions.
8. ID and EN preserve equivalent hierarchy.
9. AR preserves equivalent hierarchy with approved Arabic font/optical behavior.
10. RTL has no new clipping/overflow.
11. Public surface passes 390/768/1440 proof.
12. Layout, animation, media, and business behavior are unchanged unless explicitly recorded as required impact.
13. `npm run check:structure` passes.
14. `npm run build` passes.
15. relevant tests pass.
16. `git diff --check` passes.
17. diff contains no unrelated cleanup.
18. runtime computed-style evidence is recorded.
19. user feedback for the batch is resolved before moving to the next surface.

## 17. Definition of Done — Project Final

The unified text normalization project is DONE only when:

- M00 through M10 are PASS;
- all public routes are covered;
- shared navbar/footer are covered;
- homepage sections are covered;
- DB-backed Article, GalleryItem, and HeroSlide text paths are covered;
- JS-populated visible text paths are covered;
- admin supported desktop UI is covered;
- article reader/canvas is covered;
- ID/EN/AR semantic hierarchy is consistent;
- public mobile/tablet/desktop evidence is complete;
- no migrated semantic role is still dependent on accidental component CSS order;
- no presentation styling has leaked into DB content fields;
- remaining typography exceptions are few, intentional, documented, and backed by runtime evidence;
- full test suite passes;
- Vite build passes;
- source-structure check passes;
- final diff is free from unrelated refactor.

## 18. Anti-Overengineering Rules

The following are explicitly forbidden unless a later verified problem requires them:

- rewriting all Blade components at once;
- replacing the existing CSS architecture wholesale;
- introducing Tailwind migration merely for typography;
- adding a design-system package;
- adding typography tables/DB configuration;
- moving content between DB and translation files just to normalize styling;
- renaming every existing component class;
- changing animation/media/layout while touching text;
- adding a visual-test framework before manual/computed-style proof demonstrates a real need;
- cleaning unrelated CSS because it is ugly;
- refactoring controllers/models that already provide correct content.

The project goal is **consistent text-role behavior**, not architectural purity.

## 19. Required Batch Report Template

Every implementation response/report must use this shape:

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

No next implementation batch starts while the current batch is not PASS.

## 20. First Valid Implementation Step

After this workflow document is accepted, the first implementation action is **M00 only**:

> Inventory all rendered visible text groups and map them to the canonical semantic roles, with source paths and current computed-style evidence where the winning cascade is ambiguous.

M00 must not modify visual styling.

Only after M00 PASS may M01 create the shared text-system foundation.
