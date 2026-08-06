# UI/UX Agent Prompt Templates

Status: ACTIVE
Updated: 2026-08-07

## 1. Usage

Choose exactly one template matching the requested channel. Replace bracketed
fields; do not remove scope, proof, incident, runtime, or mutation boundaries.

Every prompt inherits `AGENTS.md`, `UI_UX_EXECUTION_INCIDENTS.md`,
`UI_UX_EXECUTION_HARDENING.md`, and the mandatory
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
READ_GATE ATTESTATION: [mandatory paths + blob SHAs]
MATCHED INCIDENTS: [...]
FAILED APPROACHES NOT TO REPEAT: [...]
BLUEPRINT: [path + state + acceptance evidence]
READ-ONLY FILES: [...]
EDITABLE FILES: [...]
FORBIDDEN FILES: [...]
MUTATION AUTHORIZATION: [read-only or exact allowed write/push action]
RUNTIME PROOF CAPABILITY: [available / unavailable]
ROLLBACK: [SHA + method]
```

## 3. Read-only audit

```text
Use the shared header.

Goal: establish FACT, HYPOTHESIS, GAP, falsifying evidence, and the smallest
valid next step. Do not edit, commit, push, open/modify PRs, or mutate external
state.

Inspect current main, mandatory docs, incident register, actual Blade/content
source, CSS winners/import order, JS state/lifecycle, Vite graph, media/assets,
six viewport tiers, ID/EN/AR, LTR/RTL, Chromium/WebKit, accessibility, and
available performance/runtime evidence only as relevant to the active surface.

Return:
FACT / HYPOTHESIS / FALSIFIER / GAP / GOAL / IMPACT / DECISION NEEDED /
STATUS / ONE NEXT VALID STEP.

A screenshot may prove a symptom or FAIL, not technical root cause. Do not claim
unrun proof. If an owner decision is needed, use the A/B/C/Hybrid contract.
```

## 4. Edit or implementation

```text
Use the shared header. The blueprint must be OWNER_ACCEPTED with recorded
acceptance evidence. Relevant incidents must be mapped and critic findings must
be resolved.

Implement one atomic surface/capability only. Audit owners before editing.
Preserve protected and unrelated areas. Do not create browser, locale, or
viewport forks; do not append anonymous cascade/controller fixes.

Use the six-tier fluid contract, ID/EN/AR and RTL/LTR lifecycle,
Chromium/WebKit capability fallback, accessibility, and declared performance
budget. Keep source files <=200 lines.

Runtime-critical work must stay on a candidate branch until required browser and
owner proof pass. If this channel cannot run browser proof, production source
mutation and direct-main publication are forbidden.

Run every available gate in UI_UX_DOD.md. Update UI_UX_CURRENT_STATE.md when
FACT/HYPOTHESIS/GAP/decision/progress/NEXT changes.

Publish only if MUTATION AUTHORIZATION names the repository, branch, scope, and
intended result and every applicable promotion gate is proven. Never force-push.
```

## 5. Build and proof only

```text
Use the shared header with MUTATION AUTHORIZATION: read-only.

Do not change source to make a failing gate disappear. Run the requested focused
proof plus:
git diff --check
git status --short
npm run check:structure
npm run build
php artisan test

For runtime proof, record exact URL/SHA/profile/tool/browser/OS/hardware,
viewport/tier, locale/direction, input/motion mode, run count, DOM parent chain,
computed styles, rectangles, controller state, and raw result.

Report each proof category separately. Parser, token tests, build, browser, and
owner proof are not interchangeable.

Return the first root-cause evidence and one next step. A build PASS does not
prove browser, responsive, RTL, accessibility, PageSpeed, or WebGL behavior.
```

## 6. Runtime diagnosis

```text
Use the shared header with MUTATION AUTHORIZATION: read-only.

Collect the complete reproduction packet from
UI_UX_EXECUTION_HARDENING.md: SHA, browser, viewport, zoom, locale, hash,
reproduction steps, recording, DOM parent chain, computed styles, rectangles,
scroll/current/target/state, and module order.

List competing hypotheses. For each, state evidence that would falsify it.
Perform the adversarial critic pass. Do not choose or implement architecture
until one hypothesis survives evidence and the owner accepts the resulting
blueprint.
```

## 7. Owner-visible failure response

```text
When owner evidence shows a required FAIL:
1. freeze source mutation;
2. set OWNER_VISUAL and FINAL_STATUS to FAIL;
3. update UI_UX_CURRENT_STATE.md;
4. update UI_UX_EXECUTION_INCIDENTS.md;
5. demote unsupported blueprint state;
6. perform read-only diagnosis;
7. name one smallest evidence step.

Do not immediately patch or publish another architecture.
```

## 8. Isolated experiment

```text
Use the shared header.

EXPERIMENT ID: [slug]
HYPOTHESIS: [one testable visual/technical claim]
FALSIFYING EVIDENCE: [what would disprove it]
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

## 9. Continue from handoff

```text
Use the shared header and name the handoff file.

First resolve current main and compare it with HANDOFF SOURCE SHA. Validate the
read attestation, mandatory files, incident mapping, blueprint acceptance,
editable/read-only/forbidden scope, exact proof, critic findings, open GAP IDs,
rollback, and one NEXT.

If source, evidence, or intent changed materially, stop with a reconciliation
GAP. Do not repeat completed proof without cause and do not continue from chat
memory alone.

Execute only the handoff's one NEXT in its named channel. Update the handoff or
current-state ledger before naming another non-trivial NEXT.
```

## 10. Handoff creation

```text
Use UI_UX_HANDOFF_TEMPLATE.md.

Record read attestation, source and resulting SHA, incident mapping,
blueprint/state/acceptance evidence, scope packet, owner decisions,
FACT/HYPOTHESIS/GAP/falsifiers, files changed, exact proof/results, critic
findings, rollback, progress/status, protected areas, context status, one target
channel, and exactly one NEXT.

Do not call publication proof runtime proof. Do not leave any required decision
or execution failure only in chat.
```

## 11. A/B/C/Hybrid question contract

Use only when the missing choice changes architecture, art direction, data, or
acceptance:

```text
QUESTION PURPOSE:
RELATION TO OWNER GOAL:
KNOWN FACT:
HYPOTHESIS:
FALSIFYING EVIDENCE:
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
blueprint, incident register, or explicit owner instruction.
