# UI/UX Blueprints

Status: ACTIVE

This folder stores owner-accepted or actively reviewed UI/UX implementation
plans. It does not store standards, command transcripts, error logs, or
historical milestone noise.

## Rules

- One blueprint owns one surface or tightly coupled capability.
- Start from `../UI_UX_BLUEPRINT_TEMPLATE.md`.
- Name files `YYYY-MM-DD-surface-or-capability.md`.
- Use states `DRAFT`, `OWNER_ACCEPTED`, `IMPLEMENTING`, and `PROVEN`.
- Do not implement an owner-level ambiguous `DRAFT`.
- Keep scope in/out, editable/read-only/forbidden files, one active step, proof,
  progress, and NEXT current.
- Link GAP and decision IDs from `../UI_UX_CURRENT_STATE.md`.
- A blueprint is a plan; proof remains in exact command/runtime output and the
  current-state ledger.
- Archive or remove superseded drafts only with owner approval. Never let a
  superseded blueprint override the active one.

## Active blueprints

- `2026-07-31-execution-foundation.md`
  - `PROVEN` for its docs-only scope;
  - owns terminology, target source structure, atomic migration, isolated labs,
    prompt templates, and the path to the first read-only baseline.
