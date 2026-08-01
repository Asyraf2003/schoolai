# SchoolAI UI/UX Architecture Index

Status: ACTIVE
Updated: 2026-08-01

## Purpose

This folder is the canonical UI/UX engineering package for SchoolAI. It keeps
design intent, architecture, workflow, proof, and continuation state stable
across Codex, Web AI, humans, and future agents.

Root `AGENTS.md` is the bootstrap router. This index owns document order and
authority.

## Mandatory read order

1. `UI_UX_DECISION_POLICY.md`
   - evidence hierarchy, conflict rules, and missing-data decisions.
2. `UI_UX_SESSION_PROTOCOL.md`
   - session start, scope packet, blueprint, active-step, channel, and handoff
     rules.
3. `UI_UX_CURRENT_STATE.md`
   - verified repo facts, active gaps, accepted decisions, progress, and NEXT.
4. `UI_UX_ENGINEERING.md`
   - ownership for Blade, CSS, JS, locale, browser, motion, and graphics.
5. `UI_UX_EXECUTION_FOUNDATION.md`
   - vocabulary, target ownership, fluid migration, labs, and promotion.
6. `UI_UX_RESPONSIVE_LOCALE_MATRIX.md`
   - six width tiers, 360px minimum, ID/EN/AR, LTR/RTL, and transition proof.
7. `UI_UX_DOD.md`
   - execution, automated proof, runtime matrix, and completion gates.

Read when relevant:

8. `UI_UX_PERFORMANCE_BROWSER_MATRIX.md`
   - PageSpeed/CWV, capability tiers, budgets, Chromium, and WebKit.
9. `UI_UX_WEBGL_3D_PIPELINE.md`
   - renderer, cinematic scenes, models, shaders, lifecycle, and fallback.
10. `UI_UX_LUSION_REFERENCE.md`
    - reference analysis and translation into Al Mustaqbal identity.
11. `UI_UX_BLUEPRINT_TEMPLATE.md`
    - required surface/scene blueprint before implementation.
12. `UI_UX_PROMPT_TEMPLATES.md`
    - bounded audit, edit, build, experiment, and continuation prompts.
13. `UI_UX_HANDOFF_TEMPLATE.md`
    - durable cross-session and cross-agent transfer.

## Live typography sources

The former Unified Text System milestone documents were intentionally removed.
When visible text or Arabic is in scope, use current source and runtime proof:

- `resources/css/text-system.css`
- `resources/css/public-latin-inter.css`
- `resources/css/arabic-typography.css`
- `resources/css/arabic-typography-base.css`
- `resources/css/arabic-type-scale.css`
- relevant Blade, lang files, and DB-rendered content.

No old milestone percentage or `PASS` status survives without new evidence.

## Document roles

- `*_DECISION_POLICY.md`: authority, conflicts, and stop/ask rules.
- `*_SESSION_PROTOCOL.md`: how a session starts, works, and hands off.
- `*_CURRENT_STATE.md`: active progress ledger and one valid NEXT.
- `*_ENGINEERING.md`: normative ownership and architecture.
- `*_EXECUTION_FOUNDATION.md`: source migration and experiment boundaries.
- `*_MATRIX.md`: declared support and required proof combinations.
- `*_PIPELINE.md`: advanced asset/runtime lifecycle.
- `*_DOD.md`: acceptance and completion gates.
- `*_REFERENCE.md`: inspiration and analysis, never binding copied code.
- `*_TEMPLATE.md`: copyable blueprint/scope structure.

Accepted surface blueprints belong under `docs/architecture/blueprints/`.
Blueprints are plans, not evidence or handoffs.

Durable continuation packets belong under `docs/architecture/handoffs/` and
start from `UI_UX_HANDOFF_TEMPLATE.md`.

## Authority

Use this order:

1. explicit current owner scope and decision;
2. root `AGENTS.md`;
3. `UI_UX_DECISION_POLICY.md`;
4. `UI_UX_CURRENT_STATE.md`;
5. owner-accepted active blueprint;
6. engineering, matrices, pipeline, and DOD;
7. source/runtime evidence for implementation facts;
8. reference documents;
9. historical observations.

Runtime/source evidence overrides stale descriptive claims. A user decision may
change product intent but cannot turn an unrun proof gate into `PASS`.

## Update discipline

- Do not leave a durable decision only in chat.
- Keep exactly one active next step in `UI_UX_CURRENT_STATE.md`.
- Progress changes only from explicit owner decisions or inspected proof.
- Update every impacted entrypoint when a mandatory rule/file changes.
- Do not use blueprints as session logs or proof stores.
- Archive/history cannot override active contracts.
- Do not recreate deleted milestone/error-log documents unless the owner asks.
- A new mandatory file must be linked by this index and root `AGENTS.md`.
- Before publish, verify all mandatory paths exist and no live document points
  to a deleted mandatory dependency.

## Retained incident report

The owner explicitly requested that one documentation-only record remain after
reconstructing `main` from the last commit before the rejected Akella Hero
transition work:

- `incidents/2026-08-01-home-hero-akella-scope-violation.md`

This incident file records an owner-rejected scope expansion. It is historical
evidence only, never an active blueprint or implementation source. Future Hero
work must use current `main` source and runtime evidence, not the retired commits.

## Current product direction

SchoolAI targets a distinctive Al Mustaqbal cinematic experience informed by
Lusion's design/motion/3D principles, with:

- WebGL for approved cinematic scenes;
- isolated experiments outside the production graph;
- semantic and static fallbacks;
- six responsive width tiers from 360px upward;
- ID/EN in Inter/LTR and AR in Cairo/RTL;
- Chromium plus Safari/WebKit;
- Lighthouse/PageSpeed `100/100/100/100`;
- field CWV `3/3` only when p75 field data proves it.

Visual ambition never waives accessibility, semantic content, lifecycle,
maintainability, or measured performance.
