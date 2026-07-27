# Arabic Typography Refactor Guide
Status: planning approved, implementation pending  
Scope: public pages, article reader, admin Arabic fields, and article canvas  
Source of truth: this document plus verified runtime output.

## 1. Goal
Build one predictable Arabic typography system without changing Indonesian or English typography, content, layout logic, routes, controllers, models, database behavior, or editor behavior.

Required result:
- Cairo for display, headings, navigation, buttons, labels, metadata, chips, indexes, and statistics;
- Lateef for paragraphs, descriptions, subtitles, article body, textarea, and long-form text;
- consistent visual hierarchy across components;
- no family choice based only on HTML tags;
- no synthetic font weights;
- every source file at or below 200 lines;
- valid RTL behavior in public, admin, article reader, and canvas pages.

Visual consistency means equivalent hierarchy, not one identical `font-size` for every element.

## 2. Verified current state
Pinned packages:
```json
"@fontsource/cairo": "5.3.0",
"@fontsource/lateef": "5.3.0"
```

Loaded faces:
- Cairo 500, 600, 700;
- Lateef 400, 700.

The Vite build emits all required font assets.

`resources/css/arabic-typography.css` is loaded by:
- `resources/views/welcome.blade.php`;
- `resources/views/layouts/public.blade.php`;
- `resources/views/layouts/admin.blade.php`;
- `resources/views/layouts/article-canvas.blade.php`.

The current entry mixes imports, family rules, generic tag selectors, article rules, canvas rules, and vision/mission exceptions.

## 3. Runtime evidence
Computed-style audit of the Arabic homepage found:
- 95 visible Lateef elements;
- 68 below 16px;
- sizes from 10.88px to 103.2px;
- common undersized values: 10.88px, 13.12px, 13.44px, 14.4px, 15.68px.

The 103.2px values are statistics display numbers, not body text.

Elements incorrectly resolving to Lateef include:
- navigation and interface labels;
- buttons and CTA links;
- metadata and chips;
- statistics numbers and indexes;
- article-card titles;
- footer headings and channel labels.

The problem is a combination of wrong family assignment, hardcoded component sizes, and cascade order.

## 4. Root causes
### 4.1 Tag-based mapping
Broad rules such as these are unreliable:
```css
html[lang="ar"] button { font-family: Cairo; }
html[lang="ar"] p { font-family: Lateef; }
```
Body text may live inside `span` or `button`; UI text may live inside `p`, `strong`, or `small`. Typography must follow semantic role and component class.

### 4.2 Hardcoded legacy sizes
Existing component CSS contains independent values such as:
```css
font-size: 0.68rem;
font-size: 0.82rem;
font-size: 0.98rem;
font-size: clamp(...);
```
Changing `--font-body` to Lateef does not remove these values.

### 4.3 Cascade order
A scale imported before later Arabic overrides can be overwritten. The semantic scale must be the final Arabic typography layer.

### 4.4 Unsupported weights
Existing CSS requests 720, 750, 850, 900, and 950. Only Cairo 500/600/700 and Lateef 400/700 are loaded. Arabic overrides must request only loaded weights.

### 4.5 Optical difference
Lateef looks smaller than Cairo at the same numeric size. Equivalent visual hierarchy requires larger Lateef body values than Cairo UI values.

## 5. Target architecture
Use exactly three focused files:
```text
resources/css/arabic-typography.css
resources/css/arabic-typography-base.css
resources/css/arabic-type-scale.css
```

Required import order:
```css
@import "@fontsource/cairo/arabic-500.css";
@import "@fontsource/cairo/arabic-600.css";
@import "@fontsource/cairo/arabic-700.css";
@import "@fontsource/lateef/arabic-400.css";
@import "@fontsource/lateef/arabic-700.css";
@import "./arabic-typography-base.css";
@import "./arabic-type-scale.css";
```

Responsibilities:
- `arabic-typography.css`: imports only;
- `arabic-typography-base.css`: family, weight, direction-sensitive rules, letter-spacing normalization;
- `arabic-type-scale.css`: font-size and line-height only, loaded last.

No file may exceed 200 lines.

## 6. Semantic roles
- Display: Cairo 700. Hero title, section title, statistics, major editorial title.
- Heading: Cairo 600/700. Card title, panel title, article heading, footer column title.
- UI: Cairo 500/600/700, visually about 16px. Navigation, button, chip, label, metadata.
- Micro: Cairo for UI metadata or Lateef for explanatory microcopy, visually about 16px.
- Body: Lateef 400, visually about 18px, line-height 1.7 to 1.8.
- Lead: Lateef 400/700, visually 20px to 24px. Section subtitle, vision, introduction.
- Long-form: Lateef 400, visually about 20px, line-height 1.85 to 1.95.

## 7. Implementation strategy
Use global refactor with local verification, not a fresh CSS audit for every section.

1. Save current screenshots as visual baseline.
2. Move existing family and weight rules into `arabic-typography-base.css`.
3. Replace broad tag-based rules with component-role selectors.
4. Define semantic size tokens in `arabic-type-scale.css`.
5. Map known component classes to semantic roles.
6. Ensure `arabic-type-scale.css` is imported last.
7. Run structure, build, Blade, and diff checks.
8. Verify each visual group.
9. Add only narrow exceptions backed by computed-style evidence.

Do not modify Indonesian or English selectors.

## 8. Verification groups
Verify in this order:
1. navbar and mega menu;
2. hero;
3. about and statistics;
4. vision, mission, and values;
5. featured programs;
6. gallery;
7. article digest and footer;
8. public article reader;
9. admin Arabic inputs and textarea;
10. Arabic article canvas.

Check desktop, tablet, and mobile widths.

## 9. Evidence rules
Use browser `getComputedStyle()` as final proof for resolved family, size, line-height, and weight.

Use `rg` and `fd` to locate declarations and component classes. They do not prove which declaration wins.

Ignore hidden nodes, scripts, styles, SVG internals, and screen-reader-only text when evaluating visible consistency.

## 10. Required validation
Before every typography commit:
```bash
npm run check:structure
npm run build
php artisan view:clear
git diff --check
git status --short
```
Then hard refresh and repeat the computed-style audit.

## 11. Acceptance criteria
The refactor is complete only when:
- display, heading, and UI Arabic text resolves to Cairo;
- paragraph and long-form Arabic text resolves to Lateef;
- no Arabic rule requests an unavailable weight;
- ordinary visible Lateef body text is not below the approved micro scale;
- equivalent semantic roles use the same token;
- exceptions are minimal and documented;
- Indonesian and English typography is unchanged;
- RTL has no text clipping or overflow;
- structure and Vite build pass;
- each typography file is at or below 200 lines.

## 12. Special case: `ﷺ`
`ﷺ` is one ligature with unusually tall bounds in some fonts. Handle it separately from the global scale.

Approved options:
- wrap it with a dedicated class and reduce its relative size;
- replace it with the full phrase only after explicit content approval.

Do not silently rewrite Arabic content.

## 13. Working rule
No change is accepted because it merely “looks about right.”

Every change must follow:
```text
fact -> resolved selector -> semantic role -> change -> build -> runtime proof
```
This document remains the implementation guide until final measured values and completed verification are recorded here.
