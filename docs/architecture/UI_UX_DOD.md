# UI/UX Workflow and Definition of Done

Status: ACTIVE
Target branch: `main`

## 1. Required workflow

Every UI/UX batch follows:

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

Only one surface or capability is active at a time.

## 2. Discovery before editing

Inspect only the active scope:

- current `main` commit and working diff;
- rendered Blade and full relevant parent chain;
- locale/lang/DB sources for visible content;
- all CSS selectors/import order affecting the nodes;
- computed winners when runtime is available;
- all JS controllers, listeners, timers, observers, fallbacks, and state classes;
- Vite entry and dynamic-import path;
- media loading, sizing, autoplay, poster, and fallback;
- viewport/direction/browser behavior;
- accessibility tree, focus, keyboard, touch, and reduced motion;
- existing tests and active architecture/handoff documents.

A screenshot or reference video must be translated into observable states:

- trigger;
- starting geometry/style;
- progression/easing;
- final state;
- reverse/interruption behavior;
- responsive and direction behavior;
- reduced-motion result.

Do not edit until root ownership and the smallest safe change are stated.

## 3. Pre-execution report

Report FACT, GAP, GOAL, IMPACT, and DECISION. Name the proven owner, exact
visible result, missing evidence, full support impact, smallest change, and
excluded areas. Ask one concise clarification only when the GAP changes the
implementation decision materially.

## 4. Execution rules

- Fetch current files/blob state again immediately before writing.
- Change only proven owners.
- Preserve unrelated user work and active milestone evidence.
- Do not refactor another section for consistency.
- Do not activate/deactivate About or Testimonial unless named.
- Do not change DB/controller/content source for a presentation problem.
- Keep source files at or below 200 lines.
- Add/update tests for durable DOM/state/accessibility contracts.
- Update current-state/handoff documentation when status or next step changes.

## 5. Required automated proof

Run:

```bash
git diff --check
git status --short
npm run check:structure
npm run build
php artisan test
```

Use focused tests while iterating. Final `PASS` requires the relevant full gate.
If dependencies or the execution channel block a command, record
`BLOCKED_BY_MISSING_EVIDENCE`; do not translate it into `PASS`.

## 6. Runtime proof matrix

Public surface minimum:

| Dimension | Required proof |
|---|---|
| Width | 390, 768, 1440px |
| Locale | ID, EN, AR |
| Direction | LTR and RTL |
| Engine | tested Chromium and Safari/WebKit versions |
| Motion | normal and `prefers-reduced-motion` |
| Input | keyboard, pointer, touch where relevant |
| Zoom/text | 200% or documented equivalent check |

Add 1180 and 1181px when navigation or its containing layout changes. Add exact
content breakpoints when wrapping or geometry changes there.

Verify:

- no clipping or unintended horizontal overflow;
- no hidden/unreachable content;
- correct logical alignment and directional motion;
- stable focus and state after repeated interaction;
- resize and orientation changes;
- fast scroll and reverse scroll;
- page hidden/visible lifecycle;
- media/renderer fallback and failure path.

## 7. Performance proof

The release target is Lighthouse/PageSpeed:

- Performance: 100
- Accessibility: 100
- Best Practices: 100
- SEO: 100

Record:

- URL, commit, date, tool/version, device profile, network/CPU settings;
- cold/warm condition and run count;
- category scores and metric values;
- transferred JS/CSS/fonts/images/video/3D assets;
- LCP element/resource;
- CLS sources;
- long tasks and animation/rendering evidence.

Run at least three comparable lab samples for a performance-sensitive change;
report median and worst result. A single lucky 100 is not durable proof.

Field Core Web Vitals `3/3` requires p75 real-user/CrUX evidence for:

- LCP <= 2.5s;
- INP <= 200ms;
- CLS <= 0.1.

Lab Lighthouse cannot prove field INP or a field `3/3` result.

## 8. Motion/3D proof

In addition to the normal matrix, record:

- static/semantic fallback;
- enhancement activation condition;
- initial and deferred transfer size;
- renderer start/stop/dispose events;
- canvas resolution and DPR cap;
- visible/offscreen and tab hidden behavior;
- dropped-frame/long-task evidence on target devices;
- unsupported/context-loss behavior;
- memory/resource cleanup after close or navigation.

No 3D feature is `PASS` when only the high-end desktop path was observed.

## 9. Definition of Done per surface

A surface is `PASS` only when:

1. source/state ownership is explicit;
2. no new anonymous cascade or duplicate controller was added;
3. semantic content works before enhancement;
4. ID/EN/AR hierarchy and locale switching are stable;
5. LTR/RTL behavior is intentional;
6. responsive evidence is complete;
7. Chromium and WebKit evidence is complete;
8. keyboard, touch/pointer, focus, and reduced motion work;
9. loading, media, motion, and renderer lifecycle are bounded;
10. relevant Lighthouse/CWV evidence is reported honestly;
11. structure, build, tests, and diff checks pass;
12. changed files are within the 200-line source limit;
13. no unrelated section/data/controller was changed;
14. user feedback for the batch is resolved;
15. current-state/handoff and next step are accurate.

## 10. Batch completion report

```text
BATCH: surface/capability
FACT / GAP / DECISION
EXECUTION: exact files and ownership changed
PROOF: diff, structure, build, tests, runtime matrix,
       accessibility, performance, and lifecycle
STATUS: PASS / FAIL / BLOCKED_BY_MISSING_EVIDENCE
NEXT VALID STEP: exactly one action and execution channel
```

Never claim completion from commit success alone.
