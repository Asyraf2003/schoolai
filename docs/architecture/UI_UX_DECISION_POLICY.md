# UI/UX Decision Policy

Status: ACTIVE
Updated: 2026-07-31

## Purpose

This is the conflict and missing-data protocol for SchoolAI design work. It
allows creative exploration without allowing facts, performance, or architecture
to be guessed.

## Evidence hierarchy

For implementation facts:

1. inspected current source and rendered runtime;
2. reproducible command, test, trace, computed-style, or browser output;
3. explicit owner decision for product intent;
4. owner-provided screenshot/video for desired result or observed symptom;
5. current active documentation;
6. reference-site observation;
7. agent inference, labelled as DECISION or GAP.

A screenshot/video can define visual intent and chronology. It cannot prove CSS
ownership, browser parity, DOM state, memory cleanup, or performance.

## Rule priority

1. safety, semantic access, and truthful proof;
2. explicit owner scope and accepted product goal;
3. current public content and interaction contracts;
4. responsive, locale/direction, and browser support;
5. PageSpeed/CWV and runtime lifecycle;
6. component ownership and maintainability;
7. local implementation preference.

When goals conflict, preserve content and interaction meaning first. Reduce
render fidelity, resolution, simultaneous assets, or effect cost before removing
required content, navigation, locale access, or accessibility.

## P0 rules

- Do not invent repo state, browser behavior, test output, budgets, or status.
- Do not code a materially ambiguous visual or architectural decision.
- Do not make WebGL the only carrier of meaningful content or actions.
- Do not put renderer/model payload on the initial critical path without an
  accepted exception backed by comparative measurement.
- Do not fork complete implementations by locale, direction, width, or engine.
- Do not copy Lusion code, assets, shaders, branding, or exact compositions.
- Do not hide a conflict with override chains or duplicate state owners.
- Do not claim PageSpeed/CWV/browser/RTL success without declared proof.
- Do not let six viewport tiers create parallel DOM, controller, bundle,
  renderer, or scene implementations.
- Do not let production import, route to, preload, or bundle experiment code.

## P1 rules

- One semantic DOM and one content source by default.
- One component/state owner per surface.
- CSS-first responsive layout and logical direction.
- One page-level renderer/context/scheduler by default.
- Dynamic import and relevance-gated advanced assets.
- Progressive fidelity with complete static/reduced-motion fallback.
- Source files stay within the enforced 200-line limit.
- One atomic active step and one valid NEXT.

## Mandatory decision sequence

Before choosing an implementation:

1. Name active surface/cinematic scene and owner goal.
2. State FACT and GAP.
3. State scope in and scope out.
4. Identify semantic content and fallback.
5. Identify Blade, CSS, JS, Vite, locale, media, and graphics owners.
6. Define six-tier behavior.
7. Define ID/EN/AR and LTR/RTL behavior.
8. Define Chromium/WebKit capabilities and downgrade.
9. Define performance/accessibility budgets and proof.
10. Record the decision in an owner-accepted blueprint.

## GAP and data-request rule

When data is insufficient:

- assign a stable GAP ID;
- state exactly what is unknown;
- explain which decision it blocks;
- request the smallest file, screenshot, recording, model, metric, or owner
  choice needed;
- keep unrelated safe discovery separate;
- do not fill the gap from taste or memory.

For an owner decision, present two or three viable options. Include when to use
each, its benefit, its cost/risk, and put the recommendation first when clear.
A hybrid option is allowed when it is a real architecture, not a vague
compromise.

Example:

| Option | Choose when | Benefit | Cost/risk |
|---|---|---|---|
| A | current ownership and behavior are proven | fastest production path | unsafe when art/physics are uncertain |
| B | a visual or graphics hypothesis is unproven | isolated learning | promotion requires a fresh production patch |
| C | only composition intent is unknown | cheapest storyboard validation | no runtime-cost evidence |
| Hybrid | validate uncertainty in a lab, then implement from a production blueprint | controlled risk and clean ownership | two explicit stages |

Do not cross an unresolved owner-level art-direction, scene, engine, or data
decision.

## Blueprint acceptance

Blueprint states:

- `DRAFT`: agent proposal, implementation forbidden;
- `OWNER_ACCEPTED`: product/art direction accepted, implementation allowed;
- `IMPLEMENTING`: one atomic step active;
- `PROVEN`: all declared acceptance gates passed.

An exact, detailed user request may itself accept the matching blueprint facts.
Do not ask again for decisions the owner already made. Record them.

## Completion language

- `PASS`: every required gate in active scope has proof.
- `FAIL`: a required gate ran and did not meet the contract.
- `BLOCKED_BY_MISSING_EVIDENCE`: required proof or decision is absent.

Commit success proves publication only. It does not prove rendering, build,
browser behavior, PageSpeed, accessibility, or WebGL lifecycle.
