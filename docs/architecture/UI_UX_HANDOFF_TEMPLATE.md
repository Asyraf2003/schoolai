# UI/UX Session Handoff Template

Status: TEMPLATE
Updated: 2026-07-31

Create `docs/architecture/handoffs/YYYY-MM-DD-scope.md` only when a handoff
trigger in `UI_UX_SESSION_PROTOCOL.md` occurs.

## 1. Identity

```text
HANDOFF ID:
DATE:
REPOSITORY:
SOURCE MAIN SHA:
RESULTING SHA, IF PUBLISHED:
ACTIVE BLUEPRINT / STATE:
FROM CHANNEL:
TO EXACTLY ONE CHANNEL:
CONTEXT STATUS:
```

## 2. Goal and scope packet

```text
OWNER GOAL:
ACTIVE SURFACE OR CAPABILITY:
REFERENCE:
READ-ONLY FILES:
EDITABLE FILES:
FORBIDDEN FILES:
PROTECTED SURFACES:
EXPECTED OUTPUT:
```

## 3. Durable state

```text
FACT:
- inspected evidence only

OWNER-ACCEPTED DECISIONS:
- decision and date

OPEN GAPS:
- GAP ID / unknown / impact / smallest proof or answer

FILES CHANGED:
- path / reason / owner replaced or preserved
```

## 4. Proof

```text
COMMAND OR RUNTIME GATE:
- exact command/profile
- exact result
- PASS / FAIL / BLOCKED_BY_MISSING_EVIDENCE

NOT PROVEN:
- every required gate not actually run

PUBLICATION:
- branch / commit / ref result
```

Publication success is not build, browser, responsive, RTL, accessibility,
PageSpeed, CWV, or WebGL proof.

## 5. Progress and continuation

```text
COMPLETED:
ACTIVE OR PAUSED:
PENDING:
STATUS:
ROLLBACK POINT:

NEXT EXECUTION CHANNEL:
ONE NEXT VALID STEP:
EXPECTED NEXT OUTPUT:
NEXT PROOF:
```

The receiving agent must resolve current `main`, read the canonical chain, and
reconcile the handoff SHA before execution.
