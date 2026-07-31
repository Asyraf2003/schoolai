# SchoolAI Architecture Index

Status: ACTIVE
Updated: 2026-07-31

## Purpose

This folder is the canonical source for SchoolAI engineering decisions that
must survive across sessions, tools, and AI agents.

Root `AGENTS.md` is the router. This folder owns the detailed contracts,
evidence, handoffs, and definitions of done.

## UI/UX first-read order

1. `UI_UX_CURRENT_STATE.md`
   - verified repository facts, known risks, gaps, and the next valid step.
2. `UI_UX_ENGINEERING.md`
   - normative ownership model for Blade, CSS, JavaScript, locale, responsive,
     browser, motion, and 3D.
3. `UI_UX_DOD.md`
   - discovery, execution, proof, and completion gates.
4. `UI_UX_PERFORMANCE_BROWSER_MATRIX.md`
   - Lighthouse/Core Web Vitals contract, capability tiers, and WebKit/Chromium
     validation.
5. `UI_UX_LUSION_REFERENCE.md`
   - non-normative interaction reference and translation rules for SchoolAI.

## Existing systems that remain authoritative

When text is affected, read in this order:

1. `UNIFIED_TEXT_SYSTEM_HANDOFF.md`
2. `UNIFIED_TEXT_SYSTEM_DOD.md`
3. `ARABIC_TYPOGRAPHY_REFACTOR.md`
4. the current milestone evidence named by the handoff.

The UI/UX system consumes the Unified Text System. It does not replace it.

## Document roles

- `*_CURRENT_STATE.md` records current facts, gaps, status, and next step.
- `*_ENGINEERING.md` defines normative architecture and ownership.
- `*_DOD.md` defines workflow and proof required for `PASS`.
- `*_REFERENCE.md` supplies inspiration and analysis, not binding code.
- `*_HANDOFF.md` is the current continuation point for a long-running scope.
- M00/M01/etc. files preserve milestone evidence and may be historical.

## Authority and conflicts

Use this priority:

1. explicit current user scope;
2. root `AGENTS.md`;
3. active current-state or handoff document;
4. active DOD and engineering contract;
5. milestone evidence;
6. reference documents;
7. archived or historical observations.

An older observation cannot override a newer explicit contract. If two active
documents conflict, stop and record the conflict as a GAP before editing.

## Update discipline

- Do not leave a durable architecture decision only in chat.
- Update the current-state or handoff when proof, status, or next step changes.
- Keep one active next step.
- Do not rewrite historical evidence to make a new implementation look valid.
- Link a new mandatory rule from this index and root `AGENTS.md`.
- Treat repository/runtime evidence as authoritative over document assumptions.
