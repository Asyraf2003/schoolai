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
6. `docs/architecture/UI_UX_EXECUTION_FOUNDATION.md`
7. `docs/architecture/UI_UX_RESPONSIVE_LOCALE_MATRIX.md`
8. `docs/architecture/UI_UX_DOD.md`

Also read:

- `UI_UX_LUSION_REFERENCE.md` for visual, motion, or 3D work;
- `UI_UX_PERFORMANCE_BROWSER_MATRIX.md` for performance, media, browser,
  animation, canvas, WebGL, or responsive work;
- `UI_UX_WEBGL_3D_PIPELINE.md` for 3D, models, shaders, canvas, cinematic
  scenes, or render-frame timing;
- `UI_UX_BLUEPRINT_TEMPLATE.md` before proposing or implementing a surface.
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
- The latest user-named file, route, screenshot, video, issue, commit, command
  output, viewport tier, cinematic scene, or section defines active scope until
  the user changes it.
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
npm run check:structure
npm run build
php artisan test
```

Runtime proof must cover `UI_UX_DOD.md`. An unavailable gate is
`BLOCKED_BY_MISSING_EVIDENCE`, never `PASS`.

Direct writes to `main` require explicit permission for the exact repository and
branch. Fetch `main` immediately before writing, update only by fast-forward,
never force-push, then report and verify the resulting commit SHA.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.5. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Test every code change by adding or updating a test.
- Run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

</laravel-boost-guidelines>
