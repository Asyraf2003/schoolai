# UI/UX Engineering — Current State and Progress Ledger

Status: BLOCKED_BY_MISSING_EVIDENCE
Updated: 2026-08-03
Repository: `Asyraf2003/schoolai`
Source main before correction: `dfef1cef25f3de744c3abaf03d1825781c4f9db6`
Active branch: `ai/fix-depth-gallery-bootstrap-20260803`

Commit publication proves source state only. It does not prove build, browser
fidelity, responsive parity, accessibility, performance, or lifecycle.

## Active production batch

Blueprint: `blueprints/2026-08-03-home-depth-gallery.md`

- ID: `HOME-GALLERY-003`
- State: `IMPLEMENTING`
- Surface: homepage Gallery only
- Owner decision: faithful port of Houmahani Kane / Codrops Atmospheric Depth
  Gallery; only SchoolAI media/text and required homepage integration differ.
- Protected: Hero, Vision/Mission, Values, Programs, Articles, navigation,
  footer, `/galeri`, DB/admin, About, Testimonial, and unrelated typography.

## Runtime FACT

Owner screenshots proved three failed states:

- the first placed oversized white content inside HTML cards;
- the second remained a custom HTML/CSS/raw-WebGL scene with an SVG trail;
- the first faithful Three source port rendered only a flat background and DOM
  labels; media planes, spatial trail, particles, and blob atmosphere were absent.

Source audit of the third failure proved:

- the canvas used `display: none` before initialization;
- `engine.resize()` therefore observed a zero-size canvas and substituted `1x1`;
- the controller displayed the canvas only after initialization and did not run a
  post-layout resize before hiding the semantic fallback;
- `is-depth-ready` was accepted without proving drawing-buffer dimensions, a
  visible plane, a primary texture, or a healthy WebGL context;
- `public/css/welcome-gallery-desktop.css` remained linked from the homepage even
  though its `.galeri-story*` owner had already been replaced.

The screenshot is runtime `FAIL`, not a visual-tuning request.

## Current source contract

The reference mechanics remain:

- deferred Three.js `0.183.0` runtime;
- `PerspectiveCamera(45, 1, 0.1, 100)`;
- textured `PlaneGeometry(3, 3)` meshes;
- plane gap `5`, desktop scale `1`, mobile scale `0.65`, mobile X spread `0.25`;
- current/next plane opacity blending;
- pointer parallax, velocity breath/tilt/scale pulse, and gesture drift;
- orthographic GLSL background with moving blobs, grain, palette blend, depth
  radius response, and velocity luminance response;
- tapered Catmull-Rom tube trail and trail-head particles;
- small DOM title/description overlays independent of media geometry;
- SchoolAI media plus the existing lightbox;
- semantic fallback for no JS, reduced motion, CDN/WebGL/texture/context failure.

The bootstrap correction now adds:

- the canvas remains in layout while hidden by visibility/opacity, never
  `display: none`, so it has measurable geometry before Three initializes;
- hidden bootstrap buffers are capped to viewport height rather than the full
  fallback-list document height;
- the primary texture must load before cinematic activation;
- `ResizeObserver` plus window resize keep renderer and camera geometry current;
- activation occurs one animation frame after sticky layout is applied;
- the semantic fallback is hidden only after a post-layout render proves a
  drawing buffer larger than `1x1`, a live context, and a visible media plane;
- invalid frames and context loss return immediately to the semantic fallback;
- the canvas remains inert and `aria-hidden` until activation passes;
- the legacy public Gallery stylesheet link and file are removed.

## Necessary integration differences

- The reference is a standalone demo that intercepts wheel and touch.
- SchoolAI maps the same camera range/smoothing to a sticky homepage section so
  normal page scrolling remains available before and after Gallery.
- Debug pane, FPS meter, Codrops frame/branding, and demo-only controls are not
  shipped.
- Labels use SchoolAI title and optional description in small black text.
- Clicking or pressing Enter/Space on the active canvas opens SchoolAI media.
- DPR is capped at `1.5` under the SchoolAI quality contract.

## Dependency and CSP decision

- Three.js is dynamically imported from the exact pinned jsDelivr module URL.
- This avoids pretending `package-lock.json` was regenerated when the GitHub
  connector cannot run `npm install`.
- CSP permits `https://cdn.jsdelivr.net` only on the homepage route.
- Failure to load the module retains the semantic fallback.
- A future self-hosted/package migration is separate and must preserve behavior.

## Six-tier source contract

| Tier | Contract |
|---|---|
| XS 360–639 | reference mobile scale/spread; labels at bottom |
| SM 640–767 | mobile scale/spread; wider safe-area spacing |
| MD 768–1023 | desktop scale/spread; labels at viewport sides |
| LG 1024–1279 | same scene; larger side insets |
| XL 1280–1535 | same scene; bounded editorial offsets |
| 2XL >=1536 | same scene; wider atmosphere, bounded labels |

One Blade source, controller, renderer, camera, scene, and sequence serve all
six tiers. ID/EN remain LTR. AR uses logical RTL label alignment without
reversing plane order, trail growth, camera depth, or time.

## Source proof status

| Gate | Status | Evidence |
|---|---|---|
| Owner art direction | `PASS` | faithful port explicitly accepted |
| Latest desktop runtime | `FAIL` | flat background and labels; no media/trail |
| Bootstrap root cause | `PASS` | hidden `display:none` canvas initialized at `1x1` |
| Measurable pre-init canvas | `PASS_SOURCE` | visibility/opacity replace `display:none` |
| Post-layout activation | `PASS_SOURCE` | activation waits one RAF then reruns resize/render |
| First-frame health gate | `PASS_SOURCE` | buffer/context/plane checks precede fallback removal |
| Texture guard | `PASS_SOURCE` | primary texture required for cinematic activation |
| Resize lifecycle | `PASS_SOURCE` | `ResizeObserver` plus window resize |
| Legacy owner removal | `PASS_SOURCE` | public stylesheet link and file removed |
| Reference source audit | `PASS` | engine, planes, scroll, background, label, trail, particles inspected |
| Three plane/camera contract | `PASS_SOURCE` | reference numeric values locked in source/tests |
| Semantic fallback | `PASS_SOURCE` | real links/images/title/caption remain available |
| Lightbox/keyboard | `PASS_SOURCE` | active canvas supports click/Enter/Space |
| Six-tier architecture | `PASS_SOURCE` | one scene plus declared tier adapters |
| RTL architecture | `PASS_SOURCE` | logical labels; no locale/scene fork |
| File limit | `PASS_SOURCE` | focused test checks corrected JS <=200 lines |
| Focused PHP tests | `BLOCKED_BY_MISSING_EVIDENCE` | not executed locally after correction |
| `npm run check:structure` | `FAIL_PRE_EXISTING` | prior Hero checksum mismatch remains unrelated |
| `npm run build` | `BLOCKED_BY_MISSING_EVIDENCE` | unavailable in connector |
| Full PHP suite | `BLOCKED_BY_MISSING_EVIDENCE` | unavailable in connector |
| Chromium/WebKit matrix | `BLOCKED_BY_MISSING_EVIDENCE` | corrected source not rendered yet |
| Lighthouse/PageSpeed | `BLOCKED_BY_MISSING_EVIDENCE` | no comparable runs |

## Progress ledger

| Stage | Status | Proof or blocker |
|---|---|---|
| G01 mandatory docs/current main | `PASS` | rules and source read before correction |
| G02 reference source audit | `PASS` | actual Three architecture inspected |
| G03 owner acceptance | `PASS` | faithful port approved |
| G04 Three camera/planes/background/trail | `IMPLEMENTED_SOURCE` | reference mechanics retained |
| G05 owner desktop runtime | `FAIL` | partial-ready `1x1` canvas screenshot |
| G06 bootstrap root-cause audit | `PASS` | source order and CSS ownership proved |
| G07 measurable canvas correction | `IMPLEMENTED_SOURCE` | layout box exists before renderer init |
| G08 healthy activation gate | `IMPLEMENTED_SOURCE` | post-layout frame required |
| G09 resize/texture/failure lifecycle | `IMPLEMENTED_SOURCE` | bounded fallback behavior added |
| G10 legacy Gallery CSS removal | `IMPLEMENTED_SOURCE` | stale public owner deleted |
| G11 focused bootstrap contract | `IMPLEMENTED_SOURCE` | new Pest source test added |
| G12 automated proof | `BLOCKED_BY_MISSING_EVIDENCE` | local execution required |
| G13 six-tier/browser runtime | `BLOCKED_BY_MISSING_EVIDENCE` | corrected screenshots pending |

## STATUS

The proven `1x1` bootstrap defect and active legacy Gallery stylesheet are
corrected in source. The Gallery remains `BLOCKED_BY_MISSING_EVIDENCE` until the
focused tests and corrected browser rendering are supplied. No visual match or
six-tier PASS is claimed.

## NEXT VALID STEP

After publication, pull `main`, run the focused Gallery tests, and provide one
desktop screenshot at the same viewport before proceeding to the remaining five
tiers and WebKit.
