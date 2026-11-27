# UI/UX Handoffs

Status: ACTIVE

This folder stores durable cross-session or cross-agent continuation packets.
Create one only when a trigger in `../UI_UX_SESSION_PROTOCOL.md` occurs.

## Rules

- Start from `../UI_UX_HANDOFF_TEMPLATE.md`.
- Name files `YYYY-MM-DD-scope.md`.
- Record exact source/resulting SHA, blueprint/state, scope packet, proof,
  open GAP IDs, progress/status, one target channel, and exactly one NEXT.
- Reconcile the handoff SHA with current `main` before execution.
- Do not use a handoff as a proof log, design blueprint, or replacement for
  `../UI_UX_CURRENT_STATE.md`.
- Superseded handoffs cannot override newer owner decisions, source, or proof.
- Do not preserve secrets, credentials, generated reports, or large command
  transcripts here.

## Active handoffs

- [Galeri dan Testimoni — handoff untuk Claude](2026-11-26-gallery-testimonials-handoff.md)
  · [Prompt dengan ruang tambahan pemilik](2026-11-26-gallery-testimonials-claude-prompt.md)
