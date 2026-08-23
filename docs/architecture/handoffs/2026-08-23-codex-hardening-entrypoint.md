# Codex Hardening Entrypoint — 2026-08-23

Status: `OWNER-ACCEPTED / CODEX-ENTRYPOINT`
Repository: `Asyraf2003/schoolai`
Target branch: `main`

## Instruction precedence for this hardening task

Follow `AGENTS.md` and the mandatory `docs/architecture/README.md` chain.
For H1-H7 hardening specifically, the current-state and H2-H7 execution packet
contain the latest owner-accepted task decisions and resolve older generic
wording when both discuss the same hardening behavior.

Two explicit clarifications:

1. Preparation after the initial semantic/static viewport is aggressive and
   sequential in the accepted order: Hero -> Program -> Values -> Vision/Mission
   -> Gallery -> Article -> Footer. Proximity/visibility/intent controls runtime
   activation and continuous execution; it is not a requirement that background
   code/media preparation wait for proximity.
2. A page-level renderer/context/scheduler is not a mandatory refactor target.
   Do not create a global renderer, scheduler, worker architecture, or framework
   merely for architectural uniformity. Consolidate only when current ownership
   and measurement prove it is needed.

## Task source

Execute only the capability currently allowed by:

- `docs/architecture/UI_UX_CURRENT_STATE.md`
- `docs/architecture/handoffs/2026-08-23-h2-h7-execution-packets.md`

H1 browser slow/reverse acceptance is a hard gate before H2. Missing runtime
proof is `BLOCKED_BY_MISSING_EVIDENCE`, not permission to continue or invent a
new patch.

## Working method

Use the workflow, mutation rules, proof gates, scope boundaries, responsive /
locale contracts, and status vocabulary already defined in `AGENTS.md` and the
mandatory architecture docs. Do not repeat repository-wide discovery unless
current source materially conflicts with the durable packet.

Work one capability at a time. No unrelated redesign, cleanup, dependency work,
or speculative architecture.

## Owner report format

After each capability report only:

```text
CAPABILITY:
MAIN SHA:
STATUS: PASS / FAIL / BLOCKED_BY_MISSING_EVIDENCE
CHANGED:
PROOF:
KNOWN BASELINE DEBT:
NEXT VALID STEP:
```

Keep the report concise. Do not restate the architecture documents.