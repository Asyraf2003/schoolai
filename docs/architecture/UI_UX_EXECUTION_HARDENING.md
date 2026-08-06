# UI/UX Execution Hardening Contract

Status: ACTIVE
Owner required: 2026-08-07

## Purpose

This contract closes execution gaps exposed by
`INC-2026-08-07-HOME-PROGRAM-001`. It supplements, and does not replace, the
existing Decision Policy, Session Protocol, Engineering Contract, Execution
Foundation, matrices, and DOD.

## 1. Absolute read gate

Before analysis, diagnosis, planning, command suggestions, editing, testing,
committing, pushing, PR/issue mutation, or status claims, an agent may only:

1. resolve current `main`;
2. read `AGENTS.md` and every mandatory architecture document in full.

The session must record:

```text
READ_GATE: PASS
CURRENT_MAIN_SHA:
MANDATORY_FILES_READ: path + blob SHA
ACTIVE_SCOPE:
MATCHED_INCIDENTS:
FAILED_APPROACHES_NOT_TO_REPEAT:
FACT:
HYPOTHESIS:
GAP:
ONE_ACTIVE_STEP:
PROOF_REQUIRED:
```

Without a complete attestation, execution is forbidden. Reading without
following the rules does not satisfy the gate.

## 2. Incident register gate

`UI_UX_EXECUTION_INCIDENTS.md` is mandatory input.

Before a fix, the agent must:

- map current symptoms to prior incidents;
- list failed approaches that cannot be repeated;
- identify last proven and current failing states;
- state evidence that would falsify each material hypothesis.

## 3. FACT, HYPOTHESIS, and GAP separation

- `FACT` requires inspected source, runtime, tool output, or explicit owner
  decision.
- `HYPOTHESIS` is an inference and must include a falsifier.
- `GAP` names missing evidence and the decision it blocks.

A screenshot may prove visual intent or `FAIL`. It does not prove DOM ownership,
computed style, module order, root cause, browser parity, or performance.

## 4. Claim-to-evidence matrix

| Claim | Minimum matching evidence |
|---|---|
| syntax valid | parser/linter |
| source structure | inspected source + structural test |
| runtime DOM owner | live parent/child inspection |
| sticky/containing block | computed style + bounding rectangles |
| full frame | viewport/element rectangles at milestone |
| choreography/smoothness | recording + current/target/state evidence |
| browser support | declared Chromium and WebKit runs |
| responsive/RTL/accessibility | required matrix runs |
| performance/CWV | declared measurement profile |
| requested visible result | owner visual acceptance |

Evidence cannot be substituted across rows. Commit success proves publication
only.

## 5. Runtime-critical mutation freeze

The following are runtime-critical:

- sticky/pinned scrolling;
- scroll choreography;
- transforms and containing blocks;
- anchor/hash geometry;
- runtime semantic-root reparenting;
- cross-surface timelines/controllers;
- canvas/WebGL;
- browser-specific layout.

When the active channel cannot run required browser proof:

- production source mutation is forbidden;
- direct write to `main` is forbidden;
- work may continue only as read-only diagnosis, docs, or an accepted candidate
  branch;
- one runtime step must be handed to `owner/local terminal` or another capable
  channel.

General permission to push `main` does not waive this gate. An exception requires
explicit owner acceptance of the named missing proof, production risk, and
rollback.

## 6. Owner feedback invalidation

Owner screenshot, video, or reproducible output showing a required failure sets:

```text
OWNER_VISUAL: FAIL
FINAL_STATUS: FAIL
```

Before another source patch:

1. freeze source mutation;
2. update `UI_UX_CURRENT_STATE.md`;
3. update the incident register;
4. demote unsupported blueprint state;
5. run read-only diagnosis;
6. request or collect the smallest falsifying evidence.

Do not respond to runtime failure with an immediate replacement architecture.

## 7. Runtime reproduction packet

For motion/layout bugs, collect:

```text
commit SHA
browser/version, OS, hardware
viewport width x height, zoom
locale/direction
URL and initial hash
reload or in-page navigation
input type and exact steps
recording or screenshot sequence
DOM parent chain
computed styles
bounding rectangles
scroll/current/target/state values
module mount order and enhancement state
```

Missing data keeps technical root cause `BLOCKED_BY_MISSING_EVIDENCE`.

## 8. Builder versus critic

The builder proposes the smallest solution.

The critic independently tries to disprove it:

- alternative root causes;
- initial hash/deep link;
- reverse, fast, and interrupted scroll;
- resize, orientation, short height, zoom;
- reduced motion and failed enhancement;
- locale/RTL;
- BFCache and repeated mount;
- module load order;
- duplicate ownership;
- tests that merely assert implementer-written tokens;
- rollback failure.

Unresolved critic findings block implementation and publication.

## 9. Blueprint acceptance evidence

A blueprint cannot be `OWNER_ACCEPTED` without:

```text
ACCEPTED BY:
ACCEPTED AT:
OWNER STATEMENT:
EXPLICITLY ACCEPTED VISIBLE RESULT:
EXPLICITLY ACCEPTED ARCHITECTURE/CHOREOGRAPHY:
UNACCEPTED AGENT IMPLEMENTATION DETAILS:
```

“Find a solution”, “push if confident”, silence, or general permission does not
accept a specific architecture.

## 10. Runtime reparenting prohibition

Moving a semantic surface root into another surface at runtime is forbidden by
default. An exception requires owner acceptance plus proof for semantics,
containing blocks, sticky/overflow/transform, initial hash, module order,
resize, BFCache, no-JS/reduced fallback, Chromium, WebKit, and rollback.

Mutually coupled surface controllers or geometry events count as duplicate
ownership unless one shared coordinator is explicitly accepted and tested.

## 11. Candidate branch and promotion

Runtime-critical work uses:

```text
proven main
-> candidate branch
-> source proof
-> runtime geometry/choreography proof
-> Chromium/WebKit proof
-> adversarial critic
-> owner visual acceptance
-> fast-forward promotion
```

Owner FAIL freezes the candidate. Diagnose or rollback before replacement.

## 12. Required status format

```text
SOURCE_SYNTAX:
SOURCE_STRUCTURE:
BUILD_TEST:
RUNTIME_GEOMETRY:
RUNTIME_CHOREOGRAPHY:
RESPONSIVE_LOCALE:
BROWSER_ENGINE:
ACCESSIBILITY:
PERFORMANCE:
OWNER_VISUAL:
PUBLICATION:
FINAL_STATUS:
```

Each line uses `PASS`, `FAIL`, or `BLOCKED_BY_MISSING_EVIDENCE`. Never report one
unqualified `PASS` for mixed categories.
