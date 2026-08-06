# SchoolAI UI/UX Architecture Index

Status: ACTIVE
Updated: 2026-08-07

## Purpose

This folder is the canonical UI/UX engineering package for SchoolAI. It keeps
design intent, architecture, workflow, proof, incidents, and continuation state
stable across Codex, Web AI, humans, and future agents.

Root `AGENTS.md` is the hard-stop bootstrap router. This index owns document
order and authority.

## Mandatory read order

No analysis or execution may begin until all mandatory files are read in full
and the read attestation from `UI_UX_EXECUTION_HARDENING.md` is recorded.

1. `UI_UX_DECISION_POLICY.md`
   - evidence hierarchy, conflict rules, and missing-data decisions.
2. `UI_UX_SESSION_PROTOCOL.md`
   - session start, scope packet, blueprint, active-step, channel, and handoff
     rules.
3. `UI_UX_EXECUTION_INCIDENTS.md`
   - live failed approaches, process violations, and mandatory prevention.
4. `UI_UX_EXECUTION_HARDENING.md`
   - absolute read gate, claim/evidence rules, runtime freeze, critic, and
     candidate-branch promotion.
5. `UI_UX_CURRENT_STATE.md`
   - verified repo facts, active gaps, accepted decisions, progress, and NEXT.
6. `UI_UX_ENGINEERING.md`
   - ownership for Blade, CSS, JS, locale, browser, motion, and graphics.
7. `UI_UX_EXECUTION_FOUNDATION.md`
   - vocabulary, target ownership, fluid migration, labs, and promotion.
8. `UI_UX_RESPONSIVE_LOCALE_MATRIX.md`
   - six width tiers, 360px minimum, ID/EN/AR, LTR/RTL, and transition proof.
9. `UI_UX_DOD.md`
   - execution, automated proof, runtime matrix, and completion gates.

Read when relevant:

10. `UI_UX_PERFORMANCE_BROWSER_MATRIX.md`
    - PageSpeed/CWV, capability tiers, budgets, Chromium, and WebKit.
11. `UI_UX_WEBGL_3D_PIPELINE.md`
    - renderer, cinematic scenes, models, shaders, lifecycle, and fallback.
12. `UI_UX_LUSION_REFERENCE.md`
    - reference analysis and translation into Al Mustaqbal identity.
13. `UI_UX_BLUEPRINT_TEMPLATE.md`
    - required surface/scene blueprint before implementation.
14. `UI_UX_PROMPT_TEMPLATES.md`
    - bounded audit, edit, build, experiment, and continuation prompts.
15. `UI_UX_HANDOFF_TEMPLATE.md`
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
- `*_EXECUTION_INCIDENTS.md`: live execution failures and failed approaches.
- `*_EXECUTION_HARDENING.md`: non-bypassable execution and proof guardrails.
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

1. safety, truthful proof, and explicit current owner scope and decision;
2. root `AGENTS.md`;
3. `UI_UX_DECISION_POLICY.md`;
4. current source/runtime evidence;
5. `UI_UX_EXECUTION_INCIDENTS.md`;
6. `UI_UX_EXECUTION_HARDENING.md`;
7. `UI_UX_CURRENT_STATE.md`;
8. owner-accepted active blueprint with acceptance evidence;
9. engineering, foundation, matrices, pipeline, and DOD;
10. reference documents;
11. historical observations.

Runtime/source evidence overrides stale descriptive claims. A user decision may
change product intent but cannot turn an unrun proof gate into `PASS`.

## Hard execution rule

If mandatory reading, ownership, root cause, acceptance evidence, or required
runtime proof is missing, stop with `BLOCKED_BY_MISSING_EVIDENCE`.

Runtime-critical production work cannot be published from a channel that cannot
run its browser proof. Owner-visible failure freezes source mutation and requires
incident, current-state, and blueprint correction before another patch.

## Update discipline

- Do not leave a durable decision or execution failure only in chat.
- Keep exactly one active next step in `UI_UX_CURRENT_STATE.md`.
- Progress changes only from explicit owner decisions or inspected proof.
- Update every impacted entrypoint when a mandatory rule/file changes.
- Do not use blueprints as session logs or proof stores.
- Archive/history cannot override active contracts.
- Do not recreate deleted milestone/error-log documents merely to preserve
  history. The active incident register exists by explicit owner instruction and
  records actionable failures only.
- A new mandatory file must be linked by this index and root `AGENTS.md`.
- Before publish, verify all mandatory paths exist and no live document points
  to a deleted mandatory dependency.

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
