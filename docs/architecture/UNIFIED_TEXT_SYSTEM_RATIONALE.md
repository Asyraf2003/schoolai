# Unified Text System — Problem Statement & Final Product Goal

Status: authoritative rationale for `UNIFIED_TEXT_SYSTEM_DOD.md`  
Scope: locale-safe visible text presentation across SchoolAI  

## 1. Why This Project Exists

This project exists because language switching currently exposes styling leakage, especially when switching to Arabic.

The observed failure pattern is not simply "Arabic typography needs different fonts". The deeper problem is that typography and text presentation have accumulated through hardcoded component rules, locale-specific overrides, and later corrective patches in the CSS cascade.

A typical failure chain is:

```text
base component style
        ↓
Arabic-specific override
        ↓
component-specific correction
        ↓
responsive override
        ↓
new patch added later
        ↓
winning selector/order changes
        ↓
UI visibly changes or breaks when locale changes
```

This creates a system where a language switch can reveal differences that were hidden in another locale:

- a title suddenly has a different visual hierarchy;
- a description becomes too large or too small;
- font weight/family changes unexpectedly;
- line height causes clipping or excessive spacing;
- RTL exposes overflow or wrapping bugs;
- mobile/tablet rules override Arabic rules differently from desktop;
- a newly added component looks correct in ID/EN but requires another special AR patch;
- fixing one surface can unintentionally alter another surface because both depend on selector specificity and import order.

The problem is therefore **style ownership and cascade predictability across locales**, not merely choosing better Arabic font sizes.

## 2. Root Cause to Eliminate

The system must stop depending on this pattern:

```text
component class + locale selector + breakpoint + later patch
```

for basic typography hierarchy.

Hardcoded exceptions may still exist when they represent a real visual requirement, but they must be narrow, measured, documented, and must not become the normal mechanism for keeping the UI usable.

The project must eliminate the need to keep "fixing Arabic" by adding another selector that compensates for a previous selector.

## 3. Product-Level Final Goal

The final product behavior must be:

```text
same content hierarchy
        ↓
same semantic text roles
        ↓
shared typography system
        ↓
locale adapter resolves language-specific optical needs
        ↓
stable UI in ID / EN / AR
```

Language switching must behave like a content/locale change, **not like loading a different styling architecture**.

A user should be able to switch:

```text
ID → EN → AR → ID → AR → EN
```

without exposing accidental visual drift caused by CSS override order.

The intended outcome is not pixel-identical typography between languages. Arabic can legitimately require different font family, font size, line height, weight availability, and RTL behavior. What must remain stable is the semantic hierarchy and component usability.

Examples:

- a hero title remains the strongest display role in every locale;
- a section heading remains a section heading in every locale;
- a description remains supporting prose rather than accidentally becoming headline-sized;
- metadata remains metadata;
- CTA/navigation text remains usable UI text;
- long-form article content keeps readable rhythm;
- responsive changes do not change the semantic role itself.

## 4. Development-Level Final Goal

The system is successful only if future development becomes easier, not merely if the current screenshots look correct.

When a developer adds a new text node, the normal workflow should be:

```text
choose semantic role
        ↓
render content from Blade / lang / DB / JS
        ↓
shared system resolves typography
        ↓
Arabic adapter handles Arabic-specific optical behavior automatically
```

It should **not** normally be:

```text
make ID look right
→ patch EN if needed
→ add Arabic selector
→ add mobile Arabic selector
→ add stronger selector because another file wins
→ repeat when another component is developed
```

That patch chain is the behavior this project is explicitly intended to remove.

## 5. Acceptance Principle for Language Switching

A migrated surface is not considered complete merely because ID, EN, and AR each look acceptable when inspected independently.

It must also satisfy **locale-switch stability**:

1. Load the surface in Indonesian.
2. Switch to English.
3. Switch to Arabic.
4. Switch back to Indonesian.
5. Repeat at representative mobile, tablet, and desktop widths.
6. Verify that resolved typography is determined by semantic role + locale adapter, not stale or accidental component overrides.

Required checks after each locale:

- hierarchy remains equivalent;
- no clipping;
- no horizontal overflow;
- no unexpected font-family change;
- no unexpected font-size/line-height jump outside approved locale tokens;
- no button/card/nav geometry break caused by text styling;
- no RTL-only collision;
- DB-backed and static/translated text with the same rendered role resolve through the same role contract.

## 6. Definition of Success for Future Features

After the migration is complete, adding a new ordinary section/card/content item should not require language-specific typography CSS merely because the content can be Arabic.

A new feature may require:

- layout rules;
- component colors;
- width/alignment decisions;
- explicitly justified visual exceptions.

But basic typography hierarchy should come from the semantic text system.

If every new Arabic-facing component still needs custom `html[lang="ar"] .some-component ...` typography patches, this project has failed even if the existing pages currently look good.

## 7. Guardrail

Do not optimize for architectural purity or for deleting every existing CSS rule.

Optimize for this measurable result:

> Switching languages and developing new content/components must no longer expose typography bugs caused by hardcoded, overlapping, locale-specific cascade patches.

The implementation remains governed by:

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

`UNIFIED_TEXT_SYSTEM_DOD.md` defines the execution workflow and completion gates. This document defines **why** those gates exist and what product behavior they must ultimately protect.
