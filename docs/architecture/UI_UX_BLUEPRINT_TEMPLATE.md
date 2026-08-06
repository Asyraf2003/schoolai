# UI/UX Surface and Cinematic Scene Blueprint Template

Status: TEMPLATE
Updated: 2026-08-07

Copy this into `docs/architecture/blueprints/YYYY-MM-DD-surface-name.md`; one blueprint owns one surface or tightly coupled capability.

## 1. Metadata

```text
BLUEPRINT ID:
STATUS: DRAFT / OWNER_ACCEPTED / IMPLEMENTING / PROVEN
OWNER:
DATE:
SOURCE MAIN SHA:
LAST PROVEN SHA:
ACTIVE ROUTE/SURFACE:
TARGET EXECUTION CHANNEL:
RELATED GAP/DECISION/INCIDENT IDS:
```

## 2. Owner acceptance evidence

```text
ACCEPTED BY:
ACCEPTED AT:
OWNER STATEMENT:
EXPLICITLY ACCEPTED VISIBLE RESULT:
EXPLICITLY ACCEPTED ARCHITECTURE/CHOREOGRAPHY:
UNACCEPTED AGENT IMPLEMENTATION DETAILS:
```

Without these fields, status cannot become `OWNER_ACCEPTED`. General permission
to work, “find a solution”, silence, or “push if confident” does not accept a
specific architecture.

## 3. Owner goal and reference

```text
Desired visible/interactive result:
School story or user need:
What must remain unchanged:
Forbidden additions:
URL/file/screenshot/video:
Viewport and timestamp:
Observed trigger:
Observed initial/intermediate/final states:
Observed reverse/interruption:
What is reference FACT:
What must not be copied:
Al Mustaqbal translation:
```

## 4. FACT, HYPOTHESIS, and GAP

```text
FACT: inspected DOM/source/runtime facts
HYPOTHESIS:
EVIDENCE THAT WOULD FALSIFY IT:
GAP ID:
Smallest missing proof/owner decision:
Decision blocked by it:
```

An unresolved owner-level art/engine/scene/architecture GAP keeps status
`DRAFT`.

## 5. Incident mapping

```text
MATCHED INCIDENTS:
FAILED APPROACHES NOT TO REPEAT:
LAST FAILING SHA:
WHY THIS PROPOSAL DOES NOT REPEAT THEM:
```

## 6. Scope

```text
SCOPE IN:
- one surface/capability
- editable owners
SCOPE OUT:
- related but untouched areas
- disabled sections
- content/DB behavior not being changed
READ-ONLY FILES:
EDITABLE FILES:
FORBIDDEN FILES:
```

## 7. Semantic experience

```text
Primary heading/content:
Primary navigation/CTA:
DOM reading/focus order:
Media alternative:
No-JS result:
No-WebGL/static result:
Reduced-motion result:
Failure result:
```

Important content and actions remain outside canvas.

## 8. Ownership map

| Concern | Existing owner | Target owner | Change reason |
|---|---|---|---|
| Blade/content | | | |
| typography | | | |
| layout/responsive | | | |
| locale/RTL/type | | | |
| interaction state | | | |
| motion | | | |
| renderer/assets/Vite | | | |
| runtime geometry | | | |

Record CSS winners/import order, containing/stacking contexts, and all
controllers/listeners before changing ownership.

Runtime semantic-root reparenting and mutually coupled surface controllers are
forbidden by default. Any exception must satisfy
`UI_UX_EXECUTION_HARDENING.md`.

## 9. Storyboard/state machine

| State | Trigger | DOM/CSS | Geometry | Motion/camera | Interruption/reverse | Cleanup | Proof |
|---|---|---|---|---|---|---|---|
| idle | | | | | | | |
| enter | | | | | | | |
| active | | | | | | | |
| transition | | | | | | | |
| exit | | | | | | | |
| suspended | | | | | | | |
| failed/static | | | | | | | |

Include fast/reverse scroll, resize, orientation, repeated entry, hidden tab,
BFCache, locale switch, and navigation interruption.

Define `full frame`, `fixed`, `continuous`, `hold`, and `snap` geometrically.
The agent may not invent their meaning.

## 10. Six-tier contract

| Tier | Layout/content | Media/crop | Controls/input | Motion | WebGL camera/quality/fallback |
|---|---|---|---|---|---|
| XS 360–639 | | | | | |
| SM 640–767 | | | | | |
| MD 768–1023 | | | | | |
| LG 1024–1279 | | | | | |
| XL 1280–1535 | | | | | |
| 2XL >=1536 | | | | | |

Record component-specific boundaries and navigation 1180/1181 impact.

## 11. Locale/direction contract

| Locale | Copy source | Line composition | Alignment | Directional motion/camera | Proof risk |
|---|---|---|---|---|---|
| ID/LTR | | | | | |
| EN/LTR | | | | | |
| AR/RTL | | | | | |

Define ID/EN/AR switch while this surface is idle, active, loading, and failed.

## 12. Cinematic scene contract, when applicable

```text
Scene ID and school purpose:
Owning semantic surface:
DOM/poster fallback:
Shared runtime/assets:
Unique assets:
Idle/enter/active/exit/reverse:
Suspend/failure/dispose:
Previous/next scene, if any:
```

There is no required scene count. Adapt the same scene across all six tiers; do not create a scene copy per tier.

## 13. Capability/browser contract

```text
Tier 0 static:
Tier 1 DOM motion:
Tier 2 efficient WebGL:
Tier 3 high fidelity:
Chromium-specific proof:
WebKit-specific proof:
Unsupported/context-loss fallback:
```

## 14. Asset and performance budget

| Group | Format | Critical/deferred | Transfer | Decoded/GPU | Activation | LOD | Dispose |
|---|---|---|---:|---:|---|---|---|
| fallback/LCP | | | | | | | |
| engine/code | | | | | | | |
| model | | | | | | | |
| textures | | | | | | | |

Record baseline and accepted delta; LCP/CLS/INP/long-task rule; Tier 2/3
render-frame time; DPR cap; PageSpeed profile/run count; and reject/downgrade
condition.

## 15. Accessibility

```text
landmarks/names and screen-reader/canvas treatment:
focus order/restoration and keyboard/touch/pointer:
reduced motion, contrast, audio/captions, 200% zoom/text expansion:
```

## 16. Runtime reproduction packet

For motion/layout work, record:

```text
commit SHA
browser/version, OS, hardware
viewport width x height, zoom
locale/direction
URL and initial hash
reload or in-page navigation
input and reproduction steps
recording
DOM parent chain
computed styles and bounding rectangles
scroll/current/target/state values
module mount order and enhancement state
```

## 17. Adversarial critic

```text
ALTERNATE ROOT CAUSES:
INITIAL HASH / DEEP LINK:
REVERSE / FAST / INTERRUPTED:
RESIZE / ORIENTATION / SHORT HEIGHT / ZOOM:
REDUCED MOTION / FAILED ENHANCEMENT:
LOCALE / RTL:
BFCache / REPEATED MOUNT:
MODULE ORDER:
DUPLICATE OWNERSHIP:
TOKEN-TEST SELF-CONFIRMATION RISK:
ROLLBACK RISK:
UNRESOLVED CRITIC FINDINGS:
```

Unresolved material critic findings block implementation or publication.

## 18. Execution plan and rollback

List ordered atomic steps. Mark exactly one `ACTIVE`.

```text
1. ACTIVE: output / proof
2. PENDING:
3. PENDING:
ROLLBACK SHA:
ROLLBACK FILES/COMMAND:
PROMOTION GATE:
```

Runtime-critical work remains on a candidate branch until required browser and
owner proof pass.

## 19. Proof and acceptance

Record the complete proof matrix from `UI_UX_DOD.md`: automated gates; focused
DOM/state tests; six tiers/boundaries; ID/EN/AR and LTR/RTL;
Chromium/WebKit/input/accessibility; Lighthouse/PageSpeed; WebGL asset/lifecycle/
context loss; and owner visual feedback.

Use category-specific status:

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

`PROVEN` requires all declared gates. Commit publication alone is not proof.

## 20. Handoff

Record files changed, proof, open GAP IDs, progress, context status, exactly one
next execution channel, and one NEXT per `UI_UX_SESSION_PROTOCOL.md`.
