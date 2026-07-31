# UI/UX Engineering — Current State and Progress Ledger

Status: ACTIVE
Audited: 2026-07-31
Repository: `Asyraf2003/schoolai`
Audited `main`: `42c123fc02d1b886989eaedfe0ca59f619ef56d9`

## Active product goal

Build a distinctive Al Mustaqbal school experience with the visual polish,
motion, spatial storytelling, and meaningful 3D ambition associated with
Lusion-class work while retaining:

- semantic school content and usable fallbacks;
- six responsive width tiers from 360px upward;
- ID/EN in LTR and AR in RTL;
- current Chromium and Safari/WebKit;
- accessibility and reduced motion;
- Lighthouse/PageSpeed `100/100/100/100`;
- field CWV `3/3` only when p75 field evidence proves it;
- maintainable Blade/CSS/JS/WebGL ownership.

## FACT

- The application is Laravel 13 + Blade + Vite 8.
- Public locale switching is a server flow:
  `POST /bahasa/{locale}` -> session/cookie -> redirect -> new Blade response.
- The public `<html>` emits `lang` and `dir` from the active locale.
- ID and EN currently use the shared Inter public adapter.
- AR currently uses the Cairo adapter and RTL.
- `package.json` currently declares no Three.js, Babylon.js, or other 3D engine.
- `scripts/verify-source-structure.mjs` enforces a 200-line limit for PHP,
  Blade, JS, and CSS under its configured source roots.
- `resources/css/pages/welcome.css` imports 47 ordered files.
- Several imported files use `cascade-*` names and multiple source files use
  `!important`; this proves cascade risk, not that each rule is wrong.
- Mobile navigation dynamically loads its cinematic CSS/JS at
  `max-width: 1180px`; desktop navigation begins at `1181px`.
- Some current systems already use `requestAnimationFrame`,
  `IntersectionObserver`, logical properties, lazy import, and reduced motion.
- Some responsive typography is measured by JavaScript, proving a narrow
  ownership tension that requires audit before change.
- About is disabled in rendered homepage while source remains.
- Testimonial source/assets exist without proven active homepage rendering.
- The owner intentionally removed 20 historical/milestone documents at
  `42c123fc`, including Unified Text System handoff/DOD/M00/M01 files.
- The removed typography files cannot support any present milestone claim.
- Lusion currently describes its work as design + motion + 3D + development.
  Its “Of The Oak” case study explicitly documents a Houdini-to-WebGL pipeline,
  compressed custom data, and instancing.
- The owner accepted 360px as certified minimum and 390px as primary XS
  baseline.
- The owner requires WebGL for approved cinematic frame work, with strict
  performance governance and functional fallback.

## Resolved governance gaps

This package removes live dependencies on the deleted typography milestone
documents and replaces them with current source/runtime inspection.

It also establishes:

- decision and missing-data policy;
- mandatory session-start and cross-agent protocol;
- blueprint-first and one-active-step gates;
- six responsive tiers and exact boundary rules;
- ID/EN/AR plus LTR/RTL transition lifecycle;
- WebGL/3D asset, renderer, frame, and disposal contracts;
- progress-write and handoff gates;
- PageSpeed/CWV, Chromium/WebKit, and accessibility proof.

## Open GAP

### `BASELINE-GAP-001`

No current built-asset, Lighthouse/PageSpeed, WebKit, screenshot, long-task,
frame-time, GPU-memory, or field CWV baseline is committed.

Impact: numeric feature delta budgets and any quality `PASS` remain unproven.

Smallest proof: build + asset inventory + repeatable Chromium/WebKit runtime
matrix before the first cinematic implementation.

### `TEXT-GAP-001`

The deleted milestone docs previously claimed typography progress. Current
source exists, but no replacement audit proves full surface coverage.

Impact: agents must inspect current DOM/computed typography per active surface;
they may not reuse old M01/M02 percentages.

### `FRAME-GAP-001`

The phrase “six model frames” has no inspected source asset, accepted storyboard,
frame names, content mapping, or exact definition in the available repo/docs.

Impact: the pipeline can guarantee one renderer, six-frame lifecycle, locale,
responsive, fallback, and budgets, but implementation cannot invent what each
frame depicts or how it narrates the school.

Owner decision options:

| Option | Meaning | Benefit | Cost/risk |
|---|---|---|---|
| A | Six independent 3D scenes/models | maximum visual variety | highest payload, memory, loading complexity |
| B | One shared 3D world with six camera/content states | smallest reusable runtime | less visual independence |
| C | Six DOM/media story frames, WebGL only on focal frames | easiest PageSpeed path | less continuous 3D identity |
| Hybrid — recommended | One renderer/shared world; six story states selectively stream unique models and DOM overlays | strong continuity with bounded cost | needs asset/story discipline |

Smallest owner proof: choose A/B/C/Hybrid, then name the purpose/content of
frames 1–6 or provide the missing screenshot/video/model reference.

### `ENGINE-GAP-001`

WebGL is accepted as a capability, but the implementation engine is unselected.

Options for the later engine ADR:

| Option | Benefit | Cost/risk |
|---|---|---|
| Three.js, targeted imports | custom cinematic control and ecosystem | bundle/API discipline required |
| Babylon.js | integrated engine/tooling | larger abstraction surface |
| Raw WebGL2 | maximum low-level control | highest engineering/testing cost |
| Hybrid — recommended | minimal Three.js core + owned lifecycle + custom shaders only where measured | requires strict import and ownership review |

Engine selection must follow the baseline and accepted frame blueprint.

### `AUDIT-GAP-001`

Mandatory docs are now manually linked, but no repository script automatically
rejects a broken UI/UX documentation chain.

Impact: document-existence/link audit is a separate future capability. It must
not be added without a full checkout and build/test proof.

## Accepted decisions

- Use one semantic DOM and content source by default.
- Use six width tiers: XS, SM, MD, LG, XL, and 2XL.
- Certified width starts at 360px; 390px remains a baseline.
- Preserve the 1180/1181 navigation boundary.
- Treat tiers as space contracts, not device detection.
- Use Inter/LTR for ID/EN and Cairo/RTL for AR until source changes with proof.
- Current locale switching remains server-rendered; transition enhancement must
  suspend outgoing motion/renderer and initialize the new direction cleanly.
- WebGL is required for owner-approved cinematic frames, but semantic content,
  navigation, CTA, and locale switching must work without it.
- Use one page-level WebGL context/renderer/scheduler by default.
- Keep renderer/model/texture/decoder bytes outside the initial critical path.
- Downgrade fidelity before removing content or interaction meaning.
- Do not big-bang rewrite the homepage or add new anonymous cascade patches.

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G00 governance hardening | `PASS` | owner decisions encoded; mandatory package prepared and remote publication is the batch proof |
| B00 current UI/performance baseline | `BLOCKED_BY_MISSING_EVIDENCE` | `BASELINE-GAP-001` |
| F00 six-frame product definition | `BLOCKED_BY_MISSING_EVIDENCE` | `FRAME-GAP-001` |
| E00 WebGL engine selection | `BLOCKED_BY_MISSING_EVIDENCE` | baseline + frame definition required |
| I00 cinematic implementation | `BLOCKED_BY_MISSING_EVIDENCE` | accepted blueprint/runtime proof absent |
| R00 PageSpeed/CWV acceptance | `BLOCKED_BY_MISSING_EVIDENCE` | no lab or field evidence |

Governance progress does not imply visual implementation progress.

## STATUS

`BLOCKED_BY_MISSING_EVIDENCE`

The operating system is defined. Current UI quality, six-frame content,
renderer choice, browser parity, PageSpeed, and CWV remain unproven.

## NEXT VALID STEP

Execution channel: owner decision.

Resolve only `FRAME-GAP-001`: select A, B, C, or Hybrid and identify the
purpose/content of frames 1–6 (or provide the missing reference). Do not select
the engine or implement frames in the same step.
