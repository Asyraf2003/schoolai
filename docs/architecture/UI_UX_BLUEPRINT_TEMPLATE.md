# UI/UX Surface and Cinematic Scene Blueprint Template

Status: TEMPLATE
Updated: 2026-07-31

Copy this into `docs/architecture/blueprints/YYYY-MM-DD-surface-name.md`; one blueprint owns one surface or tightly coupled capability.

## 1. Metadata

```text
BLUEPRINT ID:
STATUS: DRAFT / OWNER_ACCEPTED / IMPLEMENTING / PROVEN
OWNER:
DATE:
SOURCE MAIN SHA:
ACTIVE ROUTE/SURFACE:
TARGET EXECUTION CHANNEL:
RELATED GAP/DECISION IDS:
```

## 2. Owner goal and reference

```text
Desired visible/interactive result:
School story or user need:
What must remain unchanged:
URL/file/screenshot/video:
Viewport and timestamp:
Observed trigger:
Observed initial/intermediate/final states:
What is reference FACT:
What must not be copied:
Al Mustaqbal translation:
```

## 3. FACT and GAP

```text
FACT: inspected DOM/source/runtime facts
GAP ID:
Smallest missing proof/owner decision:
Decision blocked by it:
```

An unresolved owner-level art/engine/scene GAP keeps status `DRAFT`.

## 4. Scope

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

## 5. Semantic experience

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

## 6. Ownership map

| Concern | Existing owner | Target owner | Change reason |
|---|---|---|---|
| Blade/content | | | |
| typography | | | |
| layout/responsive | | | |
| locale/RTL/type | | | |
| interaction state | | | |
| motion | | | |
| renderer/assets/Vite | | | |

Record CSS winners/import order and all controllers/listeners before changing ownership.

## 7. Storyboard/state machine

| State | Trigger | DOM/CSS | Motion/camera | Interruption/reverse | Cleanup |
|---|---|---|---|---|---|
| idle | | | | | |
| enter | | | | | |
| active | | | | | |
| exit | | | | | |
| suspended | | | | | |
| failed/static | | | | | |

Include fast/reverse scroll, resize, orientation, repeated entry, hidden tab,
BFCache, locale switch, and navigation interruption.

## 8. Six-tier contract

| Tier | Layout/content | Media/crop | Controls/input | Motion | WebGL camera/quality/fallback |
|---|---|---|---|---|---|
| XS 360–639 | | | | | |
| SM 640–767 | | | | | |
| MD 768–1023 | | | | | |
| LG 1024–1279 | | | | | |
| XL 1280–1535 | | | | | |
| 2XL >=1536 | | | | | |

Record component-specific boundaries and navigation 1180/1181 impact.

## 9. Locale/direction contract

| Locale | Copy source | Line composition | Alignment | Directional motion/camera | Proof risk |
|---|---|---|---|---|---|
| ID/LTR | | | | | |
| EN/LTR | | | | | |
| AR/RTL | | | | | |

Define ID/EN/AR switch while this surface is idle, active, loading, and failed.

## 10. Cinematic scene contract, when applicable

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

## 11. Capability/browser contract

```text
Tier 0 static:
Tier 1 DOM motion:
Tier 2 efficient WebGL:
Tier 3 high fidelity:
Chromium-specific proof:
WebKit-specific proof:
Unsupported/context-loss fallback:
```

## 12. Asset and performance budget

| Group | Format | Critical/deferred | Transfer | Decoded/GPU | Activation | LOD | Dispose |
|---|---|---|---:|---:|---|---|---|
| fallback/LCP | | | | | | | |
| engine/code | | | | | | | |
| model | | | | | | | |
| textures | | | | | | | |

Record baseline and accepted delta; LCP/CLS/INP/long-task rule; Tier 2/3
render-frame time; DPR cap; PageSpeed profile/run count; and reject/downgrade
condition.

## 13. Accessibility

```text
landmarks/names and screen-reader/canvas treatment:
focus order/restoration and keyboard/touch/pointer:
reduced motion, contrast, audio/captions, 200% zoom/text expansion:
```

## 14. Execution plan

List ordered atomic steps. Mark exactly one `ACTIVE`.

```text
1. ACTIVE: output / proof
2. PENDING:
3. PENDING:
```

Do not implement future steps merely because they are adjacent.

## 15. Proof and acceptance

Record the complete proof matrix from `UI_UX_DOD.md`: automated gates; focused
DOM/state tests; six tiers/boundaries; ID/EN/AR and LTR/RTL;
Chromium/WebKit/input/accessibility; Lighthouse/PageSpeed; WebGL asset/lifecycle/
context loss; and owner visual feedback.

`PROVEN` requires all declared gates. Commit publication alone is not proof.

## 16. Handoff

Record files changed, proof, open GAP IDs, progress, context status, exactly one
next execution channel, and one NEXT per `UI_UX_SESSION_PROTOCOL.md`.
