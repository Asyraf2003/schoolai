# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Source main before batch: `6c572ab40fd15362830646844abf7a71d5efa7fa`
Gallery implementation source: `4ff6287f6072f919b76dc738bb1f6bd0804a466c`

Commit publication proves source state only. It does not prove rendering,
responsive parity, accessibility, performance, or browser lifecycle.

## Active production batch

Blueprint: `blueprints/2026-08-03-home-depth-gallery.md`

- ID: `HOME-GALLERY-003`
- State: `IMPLEMENTING`
- Surface: homepage Gallery only
- Reference: Houmahani Kane / Codrops Atmospheric Depth Gallery
- Protected: Hero, Vision/Mission, School Values, Programs, Articles,
  navigation, footer, dedicated Gallery page, translations, authentication,
  database schema, admin CRUD, dependencies, and existing media.

## FACT and DECISION

- The owner explicitly selected the reference's atmospheric depth-gallery
  direction and authorized direct publication to `main`.
- Existing six normalized database/content items remain the only homepage
  Gallery data source.
- The homepage Gallery now renders semantic HTML links and copy around one
  decorative low-power WebGL atmosphere.
- Scroll progress moves the cards through one CSS 3D depth scene; cards alternate
  inline position without creating locale or viewport forks.
- The WebGL renderer is implemented with project-owned raw WebGL1 code. No
  dependency or `package-lock.json` change is introduced.
- The Gallery controller is dynamically imported near the section, uses one RAF,
  caps DPR at 1.5, and suspends offscreen or while the document is hidden.
- WebGL failure and context loss preserve the DOM depth experience over a CSS
  atmosphere. Reduced motion preserves a static responsive card grid.
- Existing homepage lightbox behavior is reused; no-JS cards remain normal media
  links.
- One semantic DOM serves ID, EN, and AR. Logical alignment handles RTL; vertical
  time/depth progression is not reversed for Arabic.
- Base XS plus boundaries at 640, 768, 1024, 1280, and 1536px define the same
  scene across all six tiers.

## Source proof status

| Gate | Status | Evidence |
|---|---|---|
| Scope diff | `PASS_SOURCE` | compare `6c572ab..4ff6287f` contains only Gallery owners, route entries, focused test, and blueprint |
| Six-tier source contract | `PASS_SOURCE` | dedicated responsive module declares 640/768/1024/1280/1536 boundaries and fluid base |
| Locale architecture | `PASS_SOURCE` | one Blade tree/controller; RTL uses logical styling only |
| Semantic fallback | `PASS_SOURCE` | card anchors, list semantics, HTML copy, hidden decorative canvas |
| Reduced motion | `PASS_SOURCE` | controller does not initialize depth/WebGL; CSS static grid remains |
| Lifecycle bound | `PASS_SOURCE` | proximity import, offscreen/hidden suspension, BFCache handling, context-loss fallback, disposal |
| Dependency scope | `PASS_SOURCE` | no package or lockfile change |
| Source file limit | `PASS_SOURCE` | every added active source file is below 200 lines in connector diff |
| Previous clean worktree/diff | `PASS` | owner command reached `npm run check:structure` after clean status and `git diff --check` |
| `npm run check:structure` | `FAIL_PRE_EXISTING` | owner proof: `welcome-hero.css` checksum mismatch before Gallery patch |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | command chain stopped at structure gate |
| Focused PHP test | `BLOCKED_BY_MISSING_EVIDENCE` | added but not executed in a repository checkout |
| Full PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | command chain stopped before tests |
| Chromium/WebKit runtime | `BLOCKED_BY_MISSING_EVIDENCE` | no browser run yet |
| Lighthouse/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | no comparable runtime run yet |

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G01 reference/source audit | `PASS` | current template and SchoolAI owners inspected |
| G02 owner decision | `PASS` | reference direction and direct-main publication explicitly accepted |
| G03 blueprint | `PASS` | `HOME-GALLERY-003` records one bounded surface |
| G04 semantic composition | `IMPLEMENTED_SOURCE` | database media, HTML copy, links, list semantics, and CTA preserved |
| G05 atmospheric renderer | `IMPLEMENTED_SOURCE` | one raw WebGL canvas/program/buffer with palette blending |
| G06 depth choreography | `IMPLEMENTED_SOURCE` | scroll camera feeling, alternating positions, pointer and velocity response |
| G07 six-tier/RTL adapters | `IMPLEMENTED_SOURCE` | one scene with declared tier boundaries and logical RTL treatment |
| G08 lifecycle/fallback | `IMPLEMENTED_SOURCE` | lazy import, suspend, reduced/static, failure, disposal |
| G09 focused contract test | `IMPLEMENTED_SOURCE` | source contract added; execution still blocked |
| G10 automated proof | `BLOCKED_BY_MISSING_EVIDENCE` | pre-existing Hero checksum prevents build/test chain |
| G11 runtime matrix | `BLOCKED_BY_MISSING_EVIDENCE` | tiers/locales/engines/reduced motion untested |

## STATUS

Homepage Gallery replacement is implemented in source and prepared for a
non-force fast-forward to `main`. Completion remains blocked because the existing
Hero checksum failure prevents the required structure/build/test chain, and no
browser or performance proof has run.

## NEXT VALID STEP

Pull current `main` and run only `npm run check:structure`. Report the exact
result. Do not tune Gallery visuals until the repository structure gate is
reconciled or explicitly classified as an unrelated accepted blocker.
