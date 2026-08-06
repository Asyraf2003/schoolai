# SchoolAI Agent Rules

## Absolute zero-execution gate

Before any UI/UX analysis, diagnosis, plan, command suggestion, edit, test,
commit, push, PR/issue mutation, or status claim, an agent may only:

1. resolve the current `main` SHA;
2. read this file and every mandatory architecture document in full.

The agent must then record the mandatory read attestation defined in
`docs/architecture/UI_UX_EXECUTION_HARDENING.md`. If any mandatory file cannot
be read or followed, execution stops with `BLOCKED_BY_MISSING_EVIDENCE`.

There is no silent exception, best-effort bypass, or permission to improvise.
Reading the rulebook without following it does not satisfy the gate.

## Canonical instruction source

`docs/architecture/` is the canonical UI/UX rulebook for Codex, Web AI with
GitHub access, humans, and future agents.

Before UI/UX analysis, planning, editing, or command suggestions, read:

1. `docs/architecture/README.md`
2. `docs/architecture/UI_UX_DECISION_POLICY.md`
3. `docs/architecture/UI_UX_SESSION_PROTOCOL.md`
4. `docs/architecture/UI_UX_EXECUTION_INCIDENTS.md`
5. `docs/architecture/UI_UX_EXECUTION_HARDENING.md`
6. `docs/architecture/UI_UX_CURRENT_STATE.md`
7. `docs/architecture/UI_UX_ENGINEERING.md`
8. `docs/architecture/UI_UX_EXECUTION_FOUNDATION.md`
9. `docs/architecture/UI_UX_RESPONSIVE_LOCALE_MATRIX.md`
10. `docs/architecture/UI_UX_DOD.md`

Also read:

- `UI_UX_LUSION_REFERENCE.md` for visual, motion, or 3D work;
- `UI_UX_PERFORMANCE_BROWSER_MATRIX.md` for performance, media, browser,
  animation, canvas, WebGL, or responsive work;
- `UI_UX_WEBGL_3D_PIPELINE.md` for 3D, models, shaders, canvas, cinematic
  scenes, or render-frame timing;
- `UI_UX_BLUEPRINT_TEMPLATE.md` before proposing or implementing a surface;
- `UI_UX_PROMPT_TEMPLATES.md` when starting a bounded agent task;
- `UI_UX_HANDOFF_TEMPLATE.md` when work crosses sessions or agents.

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
- Record the required read attestation before doing work beyond mandatory
  reading.
- Read the incident register and map the active symptom to failed approaches.
- The latest user-named file, route, screenshot, video, issue, commit, command
  output, viewport tier, cinematic scene, or section defines active scope until
  the user changes it.
- Audit read-only before editing. Inspect the actual Blade DOM, CSS
  winners/import order, JS state, assets, locale source, Vite entry path, and
  available runtime geometry.
- A screenshot proves a symptom or composition, not ownership or root cause.
- Separate FACT, HYPOTHESIS, and GAP. Every material hypothesis must state what
  evidence would falsify it.
- Write or accept one bounded blueprint before implementation.
- `OWNER_ACCEPTED` requires explicit acceptance evidence; general permission to
  work or push does not accept a specific architecture.
- Execute one atomic surface or capability at a time.
- Do not perform unrelated cleanup, activation, deactivation, or redesign.
- Do not hide an unexplained conflict with a stronger selector, later import,
  inline fallback, timeout, z-index escalation, duplicate controller, runtime
  root reparenting, or cross-surface geometry loop.
- Remove or migrate a proven losing/conflicting owner when safe.
- Keep source files under the enforced 200-line limit.
- Use `rg` and `fd` for local discovery.
- Never claim build, browser, responsive, RTL, accessibility, Lighthouse,
  PageSpeed, smoothness, full-frame geometry, or Core Web Vitals results without
  the matching proof category.

If missing information changes architecture or art direction, record a GAP and
ask for the smallest proof or owner decision. Offer two or three viable options
plus tradeoffs and a recommended hybrid when useful. Do not silently choose an
ADR-level decision.

## Runtime-critical mutation freeze

Sticky/pinned scroll, scroll choreography, transforms/containing blocks,
anchor/hash geometry, runtime semantic-root reparenting, cross-surface
controllers, canvas, WebGL, and browser-specific layout are runtime-critical.

If the active execution channel cannot run the required browser proof:

- production source mutation is forbidden;
- direct writes to `main` are forbidden;
- work may continue only as read-only diagnosis, governance docs, or an accepted
  candidate branch;
- the runtime step must move to `owner/local terminal` or another capable
  channel.

General authorization to push `main` does not waive this rule. Any exception
requires explicit owner acceptance of the named missing proof, risk, and
rollback.

## Owner feedback invalidation

Owner screenshot, video, or reproducible output showing a required failure sets
the surface to `FAIL` immediately.

Before another source patch:

1. freeze source mutation;
2. update `UI_UX_CURRENT_STATE.md`;
3. update `UI_UX_EXECUTION_INCIDENTS.md`;
4. demote unsupported blueprint status;
5. perform read-only diagnosis;
6. collect the smallest falsifying evidence.

Do not answer a runtime failure by immediately producing another architecture.

## Mandatory adversarial review

Before publication, run a separate critic pass covering alternative root causes,
initial hash, reverse/interrupted scroll, resize, short height, zoom, reduced
motion, locale/RTL, failed enhancement, BFCache, module order, duplicate
ownership, token-test self-confirmation, and rollback.

Unresolved critic findings block publication.

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
- One controller/state owner per surface.
- Moving a semantic surface root into another surface at runtime is forbidden by
  default. Exceptions must satisfy `UI_UX_EXECUTION_HARDENING.md`.
- Cross-surface mutual geometry measurement is forbidden unless one explicit
  shared coordinator is owner-accepted and proven.
- Do not copy Lusion code, assets, branding, shaders, or exact compositions.
  Translate its storytelling principles into Al Mustaqbal identity.

## Responsive and locale contracts

- Certified minimum viewport width is `360px`; `390px` remains the primary XS
  baseline.
- Global tiers start at `360`, `640`, `768`, `1024`, `1280`, and `1536px`.
- The existing navigation contract remains hamburger through `1180px` and
  desktop from `1181px`; test both exact widths when navigation is affected.
- Every surface blueprint must define behavior in all six tiers and ID, EN, and
  AR, including LTR/RTL motion and locale-switch lifecycle.
- Width tiers describe available space, not guessed device identity.

## WebGL, performance, and accessibility

- WebGL is an intended required capability for owner-approved cinematic scenes.
  It is never required for semantic content, navigation, locale switching, or
  primary actions.
- Use one page-level renderer/context and scheduler by default. Six viewport
  tiers must not become six DOMs, bundles, contexts, or animation loops.
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
- The owner's “six frames” means the six responsive viewport tiers. It never
  means six required models, scenes, renderers, or parallel implementations.
- Experiments must use the isolated lab contract. Production must never import,
  route to, preload, or bundle lab code.

## Required proof before completion

At minimum, run and report:

```bash
git diff --check
git status --short
npm run check:structure
npm run build
php artisan test
```

Runtime proof must cover `UI_UX_DOD.md`. An unavailable gate is
`BLOCKED_BY_MISSING_EVIDENCE`, never `PASS`.

Direct writes to `main` require explicit permission for the exact repository and
branch. Fetch `main` immediately before writing, update only by fast-forward,
never force-push, then report and verify the resulting commit SHA.
