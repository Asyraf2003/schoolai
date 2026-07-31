# UI/UX Session and Cross-Agent Protocol

Status: ACTIVE
Updated: 2026-07-31

## Purpose

Every SchoolAI design session starts with scope and evidence control, then
executes one bounded surface/capability. This protocol adapts the strict
`docs-private/engineering` work model to visual, motion, responsive, locale,
browser, and WebGL work.

## Mandatory session start

1. Fetch current `main` and record its SHA.
2. Read the mandatory chain in `docs/architecture/README.md`.
3. Verify every mandatory file exists; stop on a broken dependency.
4. Identify the latest user-named surface, file, route, screenshot, video,
   frame, model, issue, commit, or command output.
5. Classify each reference:
   - `ACTIVE`: may change in this step;
   - `CONSTRAINT`: must remain true;
   - `REFERENCE`: informs design only;
   - `DEFERRED`: explicitly outside this step.
6. Inspect source/runtime owners before repo-state claims.
7. Prepare or load one surface blueprint.
8. State exactly one active step and its proof.
9. Apply the Progress Write Gate before naming NEXT.

## Required work sections

Use concise sections for small tasks and full sections for non-trivial work:

```text
FACT
GAP
GOAL
IMPACT
DECISION
BLUEPRINT
ACTIVE STEP
EXECUTION
PROOF
PROGRESS
STATUS
NEXT VALID STEP
```

## FACT and GAP

FACT is limited to inspected source/docs, visible runtime/tool output, and
explicit owner decisions. Reference-site behavior may be a reference FACT but
is not a SchoolAI implementation fact.

GAP includes unknown ownership, absent models/assets, unclear frame meaning,
missing browser/runtime evidence, missing budgets, and unaccepted art direction.
Use the decision/data-request rule in `UI_UX_DECISION_POLICY.md`.

## Surface scope packet

Before implementation or cross-agent transfer, record:

```text
TARGET AGENT / EXECUTION CHANNEL
ACTIVE SURFACE OR CAPABILITY
OWNER GOAL
CURRENT MAIN SHA
REFERENCE URL / SCREENSHOT / VIDEO
READ-ONLY FILES
EDITABLE FILES
FORBIDDEN FILES
ACCEPTED BLUEPRINT
KNOWN FACTS
OPEN GAPS
EXPECTED OUTPUT
PROOF COMMANDS AND RUNTIME MATRIX
NEXT VALID STEP
```

The receiving agent stays inside the packet unless the owner changes scope.

## Blueprint-first gate

Use `UI_UX_BLUEPRINT_TEMPLATE.md`. Code may start only when:

- scope in/out and visible result are explicit;
- semantic content/fallback is defined;
- DOM, CSS, JS, Vite, locale, media, and renderer ownership are known;
- all six responsive tiers are covered;
- ID/EN/AR and LTR/RTL behavior are covered;
- Chromium/WebKit capability/failure behavior is covered;
- performance/accessibility budgets and proof paths are declared;
- owner-level visual decisions are accepted.

Unknown numeric budgets may be baseline-dependent, but the baseline step and
accept/reject rule must already be defined.

## One active step

One active step has one target, bounded scope, expected output, and proof method.
It may contain the largest safe cohesive patch for that one capability.

Valid examples:

- establish the read-only homepage baseline;
- implement one approved hero frame plus fallback;
- replace one proven CSS owner for Gallery layout;
- add the renderer lifecycle for one approved scene;
- prove one surface across the six-tier/locale/engine matrix.

Invalid examples:

- redesign the entire site;
- add WebGL, clean all CSS, and fix all languages;
- make phone/tablet/desktop versions;
- copy Lusion and optimize later.

## Execution-channel rules

Exactly one NEXT channel must be named:

- `owner/local terminal`;
- `Terminal Codex`;
- `Web AI`;
- `explicit collaboration packet`.

Terminal Codex may edit/run local proof within user authorization. Web AI with
GitHub access is read-only by default and must not mutate files, refs, commits,
PRs, issues, or CI without exact permission naming action, repo, branch, scope,
and intended result.

Phrases such as “analyze,” “plan,” or “review” do not authorize mutation.
Explicit “push these docs directly to `Asyraf2003/schoolai` `main`” does.

Local proof and connector proof must remain separate. When they differ, mark GAP
and request the smallest reconciling proof.

## Progress Write Gate

Before any non-trivial NEXT, ask whether new proof or owner decisions changed:

- active FACT/GAP;
- blueprint state;
- stage status/progress;
- browser/performance evidence;
- next valid step.

If yes, update `UI_UX_CURRENT_STATE.md` in the same batch when feasible.
Plans alone do not increase implementation progress. A docs commit can complete
a governance step but cannot increase visual/runtime progress.

## Handoff triggers

Create/update a durable handoff or current-state entry when:

- work crosses sessions/agents;
- context is running low;
- an accepted blueprint, decision, or proof changes durable state;
- an active step ends with a GAP;
- the owner pauses or switches execution channel.

Required handoff fields:

- date, source SHA, active scope, target channel;
- read-only/editable/forbidden files;
- blueprint and decisions;
- files changed;
- proof run and exact results;
- open GAP IDs;
- progress/status;
- context status;
- exactly one NEXT.

Do not use chat history as the only handoff.

## Documentation cascade

When a rule changes how sessions start, design, execute, prove, or hand off,
update all affected entrypoints in the same batch:

- root `AGENTS.md`;
- `docs/architecture/README.md`;
- the normative contract/matrix;
- `UI_UX_CURRENT_STATE.md`;
- DOD/template when its gate changes.

Do not revive deleted milestone/error logs merely to preserve history.

## Stop conditions

Stop and mark `BLOCKED_BY_MISSING_EVIDENCE` when:

- mandatory docs or named source cannot be found;
- source and docs conflict materially;
- active ownership/root cause is unknown;
- the six-frame meaning or required asset is unknown for frame implementation;
- owner-level art direction/engine choice is unresolved;
- a required proof gate cannot run;
- direct GitHub mutation lacks exact authorization;
- `main` moved before write and the patch has not been revalidated.
