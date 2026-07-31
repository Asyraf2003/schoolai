# SchoolAI Agent Rules

## Canonical instruction source

This repository uses `docs/architecture/` as the canonical UI/UX engineering
rulebook. These rules apply to Codex, Web AI with GitHub access, and human
contributors.

Before UI/UX analysis, planning, editing, or command suggestions, read:

1. `docs/architecture/README.md`
2. `docs/architecture/UI_UX_CURRENT_STATE.md`
3. `docs/architecture/UI_UX_ENGINEERING.md`
4. `docs/architecture/UI_UX_DOD.md`

Also read:

- `docs/architecture/UI_UX_LUSION_REFERENCE.md` for visual, motion, or 3D work;
- `docs/architecture/UI_UX_PERFORMANCE_BROWSER_MATRIX.md` for performance,
  media, browser, animation, canvas, WebGL, or responsive work;
- `docs/architecture/UNIFIED_TEXT_SYSTEM_HANDOFF.md` and
  `docs/architecture/UNIFIED_TEXT_SYSTEM_DOD.md` when visible text is affected;
- `docs/architecture/ARABIC_TYPOGRAPHY_REFACTOR.md` when Arabic is affected.

A user-named file, section, route, screenshot, video, issue, commit, or command
output defines the active scope until the user changes it.

## Mandatory working protocol

Use this sequence:

```text
FACT
-> GAP
-> GOAL
-> IMPACT
-> DECISION
-> EXECUTION
-> PROOF
-> STATUS
-> NEXT VALID STEP
```

Allowed statuses:

- `PASS`
- `FAIL`
- `BLOCKED_BY_MISSING_EVIDENCE`

Rules:

- Fetch and inspect current `main`; never trust an old SHA or old chat state.
- Audit read-only before editing.
- Inspect the actual Blade DOM, CSS winners/import order, JavaScript state,
  assets, locale source, and Vite entry path for the active surface.
- Prove root cause before patching. A screenshot proves a symptom, not the
  cascade, containing block, stacking context, or state owner.
- Change one atomic surface or capability at a time.
- Do not perform unrelated cleanup or redesign.
- Do not add a stronger selector, later import, inline fallback, timeout, or
  duplicate controller merely to hide an unexplained conflict.
- Remove or migrate the proven losing/conflicting owner when safe; do not grow
  the patch chain.
- Keep source files under the enforced 200-line limit.
- Use `rg` and `fd` for local discovery.
- Never claim a build, test, browser, responsive, RTL, accessibility,
  Lighthouse, PageSpeed, or Core Web Vitals result without actual proof.

## UI/UX architecture guardrails

- One semantic DOM is the default across ID, EN, and AR.
- ID and EN share the Latin system. AR uses the approved Cairo/RTL adapter.
- Locale may adapt direction, family, tracking, line composition, and a proven
  optical exception. It must not become a parallel component architecture.
- Motion meaning is shared across locales by default. Mirror directional motion
  with logical direction where appropriate; use locale-specific choreography
  only when an approved storyboard requires it.
- Responsive behavior is CSS-first. Do not create complete phone, tablet, and
  desktop implementations unless the interaction model is proven different.
- Browser support is capability-based with `@supports`, feature detection, and
  a usable fallback. Do not create Safari and Chromium forks or use user-agent
  sniffing as the primary architecture.
- Component CSS owns layout and visual treatment. The Unified Text System owns
  migrated typography. JavaScript owns state and orchestration, not breakpoint
  typography or duplicate content.
- Advanced motion and 3D are progressive enhancement. Semantic content,
  navigation, and primary actions must remain usable without them.
- Do not copy Lusion code, assets, branding, or exact compositions. Translate
  interaction principles into the Al Mustaqbal identity.

## Performance and accessibility

- Product target: Lighthouse/PageSpeed 100 for Performance, Accessibility,
  Best Practices, and SEO on the declared test profile.
- Field target: good LCP, INP, and CLS. Field `3/3` requires real-user/CrUX
  evidence and cannot be proven by Lighthouse alone.
- A 3D or cinematic feature is not accepted if it blocks initial semantic
  rendering, delays the LCP resource without approval, causes layout shift,
  traps input, or has no static fallback.
- Lazy-load non-critical motion, media, and renderers by proximity or intent.
- Pause and release inactive video, animation loops, observers, listeners, and
  graphics resources.
- Respect `prefers-reduced-motion`; preserve keyboard, focus, contrast,
  touch/pointer, and screen-reader behavior.
- Canvas/WebGL must not be the sole carrier of important text or actions.

## Known scope protections

- About may be disabled in the rendered homepage while source remains present.
- Testimonial source may exist without being rendered. Do not alter either
  section unless the active task names it.
- The Unified Text System is active work. Do not invalidate its evidence or
  migration order while changing UI/UX.
- The `1180px` navigation boundary is an existing explicit contract. Test
  `1180px` and `1181px` when navigation is touched.
- Public baseline widths are `390px`, `768px`, and `1440px`; add component
  boundary widths when the active change requires them.

## Required proof before completion

At minimum, run and report:

```bash
git diff --check
npm run check:structure
npm run build
php artisan test
```

Runtime proof must cover the matrix required by `UI_UX_DOD.md`. If the current
execution channel cannot run a gate, report it as missing evidence.

GitHub writes may go directly to `main` only when the user explicitly authorizes
that exact repository and branch. Never force-push. Fetch current `main` again
immediately before writing and report the resulting commit SHA.
