# SchoolAI Agent Rules

## Canonical instruction source

`docs/architecture/` is the canonical UI/UX rulebook for Codex, Web AI with
GitHub access, humans, and future agents.

Before UI/UX analysis, planning, editing, or command suggestions, read:

1. `docs/architecture/README.md`
2. `docs/architecture/UI_UX_DECISION_POLICY.md`
3. `docs/architecture/UI_UX_SESSION_PROTOCOL.md`
4. `docs/architecture/UI_UX_CURRENT_STATE.md`
5. `docs/architecture/UI_UX_ENGINEERING.md`
6. `docs/architecture/UI_UX_RESPONSIVE_LOCALE_MATRIX.md`
7. `docs/architecture/UI_UX_DOD.md`

Also read:

- `UI_UX_LUSION_REFERENCE.md` for visual, motion, or 3D work;
- `UI_UX_PERFORMANCE_BROWSER_MATRIX.md` for performance, media, browser,
  animation, canvas, WebGL, or responsive work;
- `UI_UX_WEBGL_3D_PIPELINE.md` for any 3D, model, shader, canvas, or frame work;
- `UI_UX_BLUEPRINT_TEMPLATE.md` before proposing or implementing a surface.

When visible text or Arabic is affected, inspect the current semantic DOM, lang
files/DB source, and live typography owners:

- `resources/css/text-system.css`
- `resources/css/public-latin-inter.css`
- `resources/css/arabic-typography.css`
- `resources/css/arabic-typography-base.css`
- `resources/css/arabic-type-scale.css`

Do not depend on deleted Unified Text System milestone documents. Current source
and runtime proof are authoritative.

## Mandatory working protocol

Use this sequence:

```text
FACT
-> GAP
-> GOAL
-> IMPACT
-> DECISION
-> BLUEPRINT
-> ACTIVE STEP
-> EXECUTION
-> PROOF
-> PROGRESS
-> STATUS
-> NEXT VALID STEP
```

Allowed work statuses are `PASS`, `FAIL`, and
`BLOCKED_BY_MISSING_EVIDENCE`. Blueprint states are separate:
`DRAFT`, `OWNER_ACCEPTED`, `IMPLEMENTING`, and `PROVEN`.

Rules:

- Fetch and inspect current `main`; never trust an old SHA or chat state.
- Validate that every mandatory document exists before continuing.
- The latest user-named file, route, screenshot, video, issue, commit, command
  output, frame, or section defines active scope until the user changes it.
- Audit read-only before editing. Inspect the actual Blade DOM, CSS
  winners/import order, JS state, assets, locale source, and Vite entry path.
- A screenshot proves a symptom or composition, not ownership or root cause.
- Write or accept one bounded blueprint before implementation.
- Execute one atomic surface or capability at a time.
- Do not perform unrelated cleanup, activation, deactivation, or redesign.
- Do not hide an unexplained conflict with a stronger selector, later import,
  inline fallback, timeout, z-index escalation, or duplicate controller.
- Remove or migrate a proven losing/conflicting owner when safe.
- Keep source files under the enforced 200-line limit.
- Use `rg` and `fd` for local discovery.
- Never claim build, browser, responsive, RTL, accessibility, Lighthouse,
  PageSpeed, or Core Web Vitals results without actual proof.

If missing information changes architecture or art direction, record a GAP and
ask for the smallest proof or owner decision. Offer two or three viable options
plus tradeoffs and a recommended hybrid when useful. Do not silently choose an
ADR-level decision.

## Agent and mutation boundaries

- Local Codex may inspect, edit, run proof, and publish only within explicit
  repository and branch authorization.
- Web AI with GitHub access is read-only by default.
- GitHub mutation requires exact owner permission naming action, repository,
  branch, scope, and intended result.
- Cross-agent work requires a scope packet with editable, read-only, and
  forbidden files, accepted blueprint, proof gates, and exactly one next
  execution channel.
- Durable proof that changes status must update
  `UI_UX_CURRENT_STATE.md` before a new implementation step is named.

## UI/UX architecture guardrails

- One semantic DOM is the default across ID, EN, and AR.
- ID and EN share Inter/LTR; AR uses Cairo/RTL through a narrow adapter.
- Locale may adapt direction, family, tracking, line composition, and a proven
  optical exception; it must not become a parallel component architecture.
- Motion meaning is shared. Mirror directional meaning for RTL; use
  locale-specific choreography only from an owner-accepted storyboard.
- Responsive behavior is CSS-first and governed by six width tiers. Do not
  create full phone, tablet, desktop, browser, or locale forks.
- Browser support is capability-based with `@supports`, feature detection, and
  a usable fallback; user-agent forks require a reproduced engine defect.
- Component CSS owns layout and treatment. Typography owners own type. JS owns
  state/orchestration, not breakpoint typography or duplicate content.
- Do not copy Lusion code, assets, branding, shaders, or exact compositions.
  Translate its storytelling principles into Al Mustaqbal identity.

## Responsive and locale contracts

- Certified minimum viewport width is `360px`; `390px` remains the primary XS
  baseline.
- Global tiers start at `360`, `640`, `768`, `1024`, `1280`, and `1536px`.
- The existing navigation contract remains hamburger through `1180px` and
  desktop from `1181px`; test both exact widths when navigation is affected.
- Every surface blueprint must define behavior in all six tiers and ID, EN,
  and AR, including LTR/RTL motion and locale-switch lifecycle.
- Width tiers describe available space, not guessed device identity.

## WebGL, performance, and accessibility

- WebGL is an intended required capability for owner-approved cinematic frames.
  It is never required for semantic content, navigation, locale switching, or
  primary actions.
- Use one page-level renderer/context and scheduler by default. Six frames must
  not become six simultaneous contexts, bundles, or animation loops.
- Renderer, models, textures, and decoders stay outside the initial critical
  path and load by capability plus proximity or intent.
- A static semantic fallback is always present. Reduced motion, context loss,
  unsupported graphics, and constrained devices downgrade fidelity, not access.
- Product target: Lighthouse/PageSpeed `100/100/100/100` on declared profiles.
- Field target: good LCP, INP, and CLS. Field `3/3` requires p75 RUM/CrUX
  evidence and cannot be proven by Lighthouse.
- Reserve geometry for media/canvas, cap DPR from measured budgets, pause
  offscreen/hidden work, and dispose graphics, media, observers, and listeners.
- Preserve keyboard, focus, contrast, touch/pointer, screen-reader, zoom, and
  `prefers-reduced-motion` behavior.

## Known scope protections

- About may be disabled in rendered homepage while source remains present.
- Testimonial source may exist without rendering. Do not alter either unless
  active scope names it.
- Typography milestone docs were intentionally removed; do not resurrect or
  claim their old progress without new source/runtime evidence.
- Current absence of a 3D dependency is a fact, not permission to choose an
  engine without a blueprint.
- The meaning/content of the owner's “six model frames” remains an explicit GAP
  until the owner resolves `FRAME-GAP-001` in `UI_UX_CURRENT_STATE.md`.

## Required proof before completion

At minimum, run and report:

```bash
git diff --check
npm run check:structure
npm run build
php artisan test
```

Runtime proof must cover `UI_UX_DOD.md`. An unavailable gate is
`BLOCKED_BY_MISSING_EVIDENCE`, never `PASS`.

Direct writes to `main` require explicit permission for the exact repository and
branch. Fetch `main` immediately before writing, update only by fast-forward,
never force-push, then report and verify the resulting commit SHA.
