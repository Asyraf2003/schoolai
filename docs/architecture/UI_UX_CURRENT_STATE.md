# UI/UX Engineering — Current State

Status: ACTIVE
Audited: 2026-07-31
Repository: `Asyraf2003/schoolai`
Audited `main`: `89f57a7c627b429f2d2d40d8867404d84db19226`

## FACT

- The application is Laravel 13 + Blade + Vite 8.
- Public UI supports ID and EN in LTR and AR in RTL.
- The current Arabic contract uses Cairo for all semantic roles.
- The Unified Text System M01 is `PASS`; M02 remains active and blocked by
  missing responsive/locale runtime evidence.
- `scripts/verify-source-structure.mjs` enforces a maximum of 200 lines for
  PHP, Blade, JS, and CSS under `app`, `database`, `resources`, and `routes`.
- The structure verifier also checks local imports, Vite/Blade asset reachability,
  and ordered-module checksums for mechanically split CSS.
- `resources/css/pages/welcome.css` currently imports 47 ordered files.
- Several imported files are named `*-cascade-*`; multiple files contain
  `!important`. This proves accumulated cascade risk, not that every rule is
  wrong.
- Homepage behavior is spread across page entries and imported controllers.
  Some systems already use good patterns such as `requestAnimationFrame`,
  `IntersectionObserver`, `prefers-reduced-motion`, logical properties, and
  lazy import.
- Mobile navigation dynamically imports its cinematic CSS/JS only at
  `max-width: 1180px` and has a semantic/fallback control path.
- The latest main commit adjusts mobile Vision/Mission text fitting by measuring
  DOM content and setting `--vision-mobile-heading-size` from JavaScript.
- The Unified Text System contract says responsive typography belongs in CSS
  and JS must not invent viewport typography. This is a proven ownership
  tension requiring a narrow audit; it is not authorization to remove the
  current behavior.
- About is commented out in the homepage Blade and its Vite entries are
  disabled there. Source remains.
- Testimonial assets exist in Vite while the inspected homepage Blade does not
  render a testimonial section. Its intended activation state is not yet
  proven.
- `package.json` has no declared Three.js or other 3D engine dependency.
- Before this package, the repository had no root `AGENTS.md` and no
  `docs/architecture/README.md`.

## GAP

- No current Lighthouse/PageSpeed baseline was available through the repository.
- No field CrUX/RUM evidence was available, so Core Web Vitals `3/3` is unproven.
- No Safari/WebKit runtime matrix was available.
- Repository inspection through the GitHub connector cannot prove build, PHP
  tests, rendered pixels, computed styles, animation frame stability, or memory
  lifecycle.
- The exact number of redundant versus intentional rules inside the 47-file
  homepage chain is not yet measured.
- The correct long-term replacement for mechanically split cascade files is not
  proven and must not be guessed.

## GOAL

Create a UI/UX engineering system that can add cinematic storytelling, motion,
and optional 3D while keeping:

- semantic Blade output;
- ID/EN/AR hierarchy and locale switching;
- LTR/RTL correctness;
- mobile/tablet/desktop usability;
- current Chromium and Safari/WebKit support;
- accessibility and reduced motion;
- a credible path to Lighthouse 100/100/100/100 and field CWV 3/3;
- maintainable ownership without new cascade or controller patch chains.

## IMPACT

The contract applies to public Blade, CSS, JS, media, Vite entries, locale
rendering, navigation, and future canvas/WebGL modules. It does not itself
change current visuals, data, routes, controllers, or disabled sections.

## DECISION

- Do not perform a big-bang homepage rewrite.
- Stop adding generic `cascade-*` patches for new work.
- Migrate ownership one surface at a time after identifying real winners and
  runtime state.
- Keep one semantic DOM by default.
- Adapt locale/direction, responsive layout, and browser capabilities through
  narrow layers rather than parallel page implementations.
- Treat 3D as an isolated progressive enhancement with a static fallback and a
  measured loading/lifecycle budget.
- Preserve source-equivalence checks until a dedicated migration proves a safe
  replacement.

## TARGET OWNERSHIP

| Concern | Primary owner |
|---|---|
| Content and semantics | Blade/lang/DB render context |
| Typography hierarchy | Unified Text System |
| Component layout/visuals | Named component or section CSS |
| Responsive layout | CSS media/container rules |
| Direction/Arabic adaptation | logical CSS + narrow AR adapter |
| Interaction state | one JS controller per surface |
| Motion orchestration | lazy motion module with lifecycle |
| 3D rendering | isolated lazy renderer with static fallback |
| Browser differences | capability detection and fallback |
| Quality status | measured proof, never source inspection alone |

## STATUS

`BLOCKED_BY_MISSING_EVIDENCE`

The governance package can be established, but current UI quality and the
100/100/100/100 target are not yet proven.

## NEXT VALID STEP

Create a read-only UI/UX baseline for the current homepage: built asset sizes,
Lighthouse lab runs, rendered entry map, and ID/EN/AR screenshots at
390/768/1440 in Chromium before selecting the first ownership migration.
