# Arabic Typography Contract
Status: active, Cairo-only contract
Scope: public pages, article reader, admin Arabic fields, and article canvas
Source of truth: this document plus verified runtime output.

## 1. Goal

Arabic must use the same semantic text hierarchy as Indonesian and English without carrying a second optical scale that changes structure merely because the locale is Arabic.

Required result:

- Cairo is the only approved Arabic runtime family;
- semantic roles determine size, weight, line-height, and hierarchy;
- Arabic may normalize tracking and direction-sensitive behavior;
- Arabic must not use a separate body-size system merely to compensate for a different font family;
- no family choice based only on HTML tags;
- no synthetic font weights;
- RTL must not clip or overflow;
- source files remain within repository structure limits.

`Seragam` means equivalent semantic hierarchy. Arabic does not need numerically different type sizes unless runtime evidence proves a narrow exception is required.

## 2. Decision that supersedes the old plan

The previous architecture used:

- Cairo for display/UI;
- Lateef for prose/description/long-form;
- enlarged Arabic/Naskhi tokens to compensate for Lateef's smaller optical appearance.

That plan is retired.

Reason:

- Lateef at small sizes was difficult to read in this product;
- compensating for Lateef required very large Arabic-only sizes;
- those values distorted component structure and made the Arabic hierarchy diverge from ID/EN;
- once the body family changed to Cairo, the old Lateef compensation became actively harmful because the oversized values were applied to Cairo.

Therefore all Lateef-specific and Naskhi-specific scale tuning is non-normative and must not be used as a target for future milestones.

## 3. Current runtime architecture

Primary files:

```text
resources/css/text-system.css
resources/css/arabic-typography.css
resources/css/arabic-typography-base.css
resources/css/arabic-type-scale.css
```

Runtime order:

```text
legacy/component CSS
→ text-system.css
→ arabic-typography.css
```

`arabic-typography.css` imports Cairo Arabic faces and then the Arabic adapter.

`arabic-typography-base.css` currently maps both Arabic family aliases to Cairo:

```css
--font-ar-display: "Cairo", sans-serif;
--font-ar-body: "Cairo", sans-serif;
```

Keeping two aliases is intentional for now. They preserve semantic role separation without implying two different font families.

`arabic-type-scale.css` is now only a compatibility import. It must not define a second Arabic optical scale. Its previous body/lead/feature enlargement rules are retired.

## 4. Font ownership

All Arabic semantic roles resolve to Cairo.

Role groups:

- `display` → Cairo;
- `page-title` → Cairo;
- `section-title` → Cairo;
- `component-title` → Cairo;
- `subtitle` → Cairo;
- `body` → Cairo;
- `description` → Cairo;
- `label` → Cairo;
- `meta` → Cairo;
- `action` → Cairo;
- `longform` → Cairo.

Family is locale-specific. Scale is semantic-system-owned.

## 5. Size, weight, and line-height ownership

For migrated nodes, these properties must resolve from `resources/css/text-system.css` or a narrowly proven Arabic adapter exception:

- `font-size`;
- `font-weight`;
- `line-height`;
- `letter-spacing`.

Arabic must not maintain parallel variables such as oversized body/lead/feature scales merely because of locale.

Allowed Arabic adaptation:

- Cairo family assignment;
- `letter-spacing: normal` where Arabic shaping requires it;
- direction-sensitive behavior;
- a narrow measured exception when shared values demonstrably clip or break readability.

Not allowed:

- blanket Arabic body enlargement;
- Lateef/Naskhi compensation values;
- independent Arabic component scales that reproduce the old cascade problem;
- viewport-dependent typography in JS.

## 6. Legacy fallback behavior during migration

M02-M09 are incremental. Components not yet migrated may still use legacy component classes for size and line-height.

During each milestone:

1. assign the correct `data-text-role`;
2. inspect the actual computed winner;
3. let the shared text-system own typography;
4. remove only conflicting legacy declarations;
5. verify ID/EN/AR at the required surfaces.

Do not recreate the retired Arabic optical scale as a temporary shortcut.

## 7. Historical M00 evidence

M00 documents record the old runtime truth. Some of them mention Lateef and unusually large Arabic values.

Those records remain valid historical evidence of the pre-refactor state. They are not implementation targets.

When a M00 statement conflicts with this document about the desired Arabic family or optical scaling:

- M00 describes what existed;
- this document describes what must exist now.

Do not rewrite baseline measurements merely to make history look cleaner. Humans already do enough of that elsewhere.

## 8. Dependency note

Runtime CSS must not import Lateef.

If `@fontsource/lateef` remains temporarily in package metadata, it is an unused dependency only and must not influence runtime output. Dead dependency cleanup belongs to the final legacy-cleanup pass unless removing it is required earlier by build/package validation.

## 9. Verification order

Verify by milestone rather than by maintaining a separate Arabic refactor sequence.

For every migrated public surface verify:

- ID LTR;
- EN LTR;
- AR RTL;
- 390px;
- 768px;
- 1440px.

Admin remains desktop-only under the existing product constraint.

Use browser `getComputedStyle()` as final proof for:

- family;
- size;
- weight;
- line-height;
- letter-spacing.

Source search with `rg`/`fd` locates declarations but does not prove the winning cascade.

## 10. M02 Arabic acceptance

For shared navigation and Hero:

- every migrated Arabic text role resolves to Cairo;
- semantic sizes match the shared role contract unless a measured narrow exception is documented;
- nav/action text is not enlarged by a retired Arabic-only scale;
- Hero description is not enlarged by old body compensation;
- RTL does not clip or overflow;
- ID/EN remain unchanged by Arabic adapter work.

Lateef is not an acceptance target.

## 11. Global acceptance criteria

The Arabic typography migration is complete only when:

- all visible Arabic text resolves to Cairo or an explicitly approved exceptional family introduced by a later product decision;
- no active runtime rule depends on Lateef/Naskhi optical compensation;
- equivalent semantic roles use the shared role tokens;
- Arabic tracking and direction remain valid;
- no unsupported weight is requested by the final Arabic adapter;
- RTL has no text clipping or overflow;
- source structure, Vite build, PHP tests, and runtime proof pass;
- legacy Arabic scale cleanup is complete by M10.

## 12. Working rule

Every Arabic change follows:

```text
fact
→ resolved selector
→ semantic role
→ shared token
→ Cairo adapter
→ build/test
→ runtime proof
```

No change is accepted merely because it looks approximately right on one viewport.
