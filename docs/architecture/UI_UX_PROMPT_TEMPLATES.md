# UI/UX Agent Prompt Templates

Status: ACTIVE
Updated: 2026-07-31

## 1. Usage

Choose exactly one template matching the requested channel. Replace bracketed
fields; do not remove scope, proof, or mutation boundaries.

Every prompt inherits `AGENTS.md` and the mandatory
`docs/architecture/README.md` chain. A prompt cannot lower those contracts.

## 2. Shared header

```text
REPOSITORY: Asyraf2003/schoolai
TARGET BRANCH: [main or named branch]
CURRENT MAIN SHA: [resolve now; never reuse chat SHA]
EXECUTION CHANNEL: [Terminal Codex / Web AI / owner terminal]
ACTIVE SURFACE OR CAPABILITY: [one]
OWNER GOAL: [visible result or evidence goal]
REFERENCE: [file/route/URL/screenshot/video/command output]
BLUEPRINT: [path + state]
READ-ONLY FILES: [...]
EDITABLE FILES: [...]
FORBIDDEN FILES: [...]
MUTATION AUTHORIZATION: [read-only or exact allowed write/push action]
```

## 3. Read-only audit

```text
Use the shared header.

Goal: establish FACT/GAP and the smallest valid next step. Do not edit, commit,
push, open/modify PRs, or mutate external state.

Inspect current main, mandatory docs, actual Blade/content source, CSS
winners/import order, JS state/lifecycle, Vite graph, media/assets, six viewport
tiers, ID/EN/AR, LTR/RTL, Chromium/WebKit, accessibility, and available
performance evidence only as relevant to the active surface.

Return:
FACT / GAP / GOAL / IMPACT / DECISION NEEDED / STATUS / ONE NEXT VALID STEP.
Do not claim unrun proof. If an owner decision is needed, use the A/B/C/Hybrid
question contract.
```

## 4. Edit or implementation

```text
Use the shared header. The blueprint must be OWNER_ACCEPTED.

Implement one atomic surface/capability only. Audit owners before editing.
Preserve protected and unrelated areas. Do not create browser, locale, or
viewport forks; do not append anonymous cascade/controller fixes.

Use the six-tier fluid contract, ID/EN/AR and RTL/LTR lifecycle,
Chromium/WebKit capability fallback, accessibility, and declared performance
budget. Keep source files <=200 lines.

Run every available gate in UI_UX_DOD.md. Update UI_UX_CURRENT_STATE.md when
FACT/GAP/decision/progress/NEXT changes.

Publish only if MUTATION AUTHORIZATION names the repository, branch, scope, and
intended result. Never force-push.
```

## 5. Build and proof only

```text
Use the shared header with MUTATION AUTHORIZATION: read-only.

Do not change source to make a failing gate disappear. Run the requested focused
proof plus:
git diff --check
npm run check:structure
npm run build
php artisan test

For runtime proof, record exact URL/SHA/profile/tool/browser/OS/hardware,
viewport/tier, locale/direction, input/motion mode, run count, and raw result.
Distinguish PASS, FAIL, and BLOCKED_BY_MISSING_EVIDENCE.

Return the first root-cause evidence and one next step. A build PASS does not
prove browser, responsive, RTL, accessibility, PageSpeed, or WebGL behavior.
```

## 6. Isolated experiment

```text
Use the shared header.

EXPERIMENT ID: [slug]
HYPOTHESIS: [one testable visual/technical claim]
ACCEPTANCE METRIC: [observable result + budget]
EXPIRY/REMOVAL RULE: [date or decision]

Work only inside the accepted lab boundary from
UI_UX_EXECUTION_FOUNDATION.md. Production must not import, route to, preload, or
bundle the lab. Use synthetic/licensed local assets; do not mutate production
DB/content.

Provide normal, reduced, static, failure, suspend, and dispose behavior as
applicable. Test the minimum representative tiers/locales/engines needed to
answer the hypothesis.

Conclude ACCEPTED or REJECTED. Promotion is a separate production blueprint and
patch; it is never a production import of the lab.
```

## 7. Continue from handoff

```text
Use the shared header and name the handoff file.

First resolve current main and compare it with HANDOFF SOURCE SHA. Validate
mandatory files, blueprint state, editable/read-only/forbidden scope, exact
proof, open GAP IDs, and one NEXT.

If source or intent changed materially, stop with a reconciliation GAP. Do not
repeat completed proof without cause and do not continue from chat memory alone.

Execute only the handoff's one NEXT in its named channel. Update the handoff or
current-state ledger before naming another non-trivial NEXT.
```

## 8. Handoff creation

```text
Use UI_UX_HANDOFF_TEMPLATE.md.

Record source and resulting SHA, blueprint/state, scope packet, owner decisions,
FACT/GAP, files changed, exact proof/results, progress/status, protected areas,
context status, one target channel, and exactly one NEXT.

Do not call publication proof runtime proof. Do not leave any required decision
only in chat.
```

## 9. A/B/C/Hybrid question contract

Use only when the missing choice changes architecture, art direction, data, or
acceptance:

```text
QUESTION PURPOSE:
RELATION TO OWNER GOAL:
KNOWN FACT:
UNKNOWN / GAP ID:
DECISION THIS BLOCKS:

A — [name]
+ [benefit]
- [cost/risk]

B — [name]
+ [benefit]
- [cost/risk]

C — [name]
+ [benefit]
- [cost/risk]

HYBRID — [only when concrete]
+ [benefit]
- [cost/risk]

RECOMMENDATION:
SMALLEST ANSWER NEEDED:
```

Do not ask for information already present in current source, proof, accepted
blueprint, or explicit owner instruction.
