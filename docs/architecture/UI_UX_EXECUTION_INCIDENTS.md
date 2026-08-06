# UI/UX Execution Incident Register

Status: ACTIVE
Updated: 2026-08-07

This file is mandatory input, not optional history. Before proposing a fix, map
the active symptom to prior incidents, list failed approaches, and state what
evidence would falsify the next hypothesis.

Do not use this register as a substitute for current source or runtime proof.

## INC-2026-08-07-HOME-PROGRAM-001

### Scope

Homepage Visi/Misi to Program scroll choreography on
`Asyraf2003/schoolai`.

### Affected commit sequence

```text
076efb5e2e3770d90688cd4c9267633b523ac485
3865b248093da2d379d4e6b666e1a6caf13daec6
719a3b5114660767dc7268a325b9465622754132
9d5dfbe39d84982734ee45c6956a4de464c8811d
2753e10093bc320364b0e39555dddca1a160d709
ed4346f0768add58024aa88748c9a1b94655f2d2
```

### Owner-visible failures

Owner screenshots on 2026-08-06 and 2026-08-07 prove:

- Program white canvas, media, title, and description do not follow the requested
  layer and frame behavior;
- Visi/Misi horizontal motion is interrupted or no longer completes correctly;
- Program media and copy can appear in unintended partial compositions;
- rail/hash navigation does not consistently land on the requested full frame;
- agent-created metadata, counters, timing, layout, and integration decisions
  appeared without explicit owner instruction.

These screenshots prove runtime and owner-visible `FAIL`. They do not by
themselves prove the final technical root cause.

### Process violations

1. Root causes were asserted from screenshots without runtime DOM, computed
   style, rectangles, scroll values, or module-order evidence.
2. Agent inference was presented as FACT.
3. Material architecture changed repeatedly: curtain overlay, single track,
   separated owners, then runtime-integrated owners.
4. Owner-level decisions were implemented without explicit acceptance.
5. A blueprint was labelled `OWNER_ACCEPTED` without acceptance evidence.
6. Source-token and syntax checks were used rhetorically as if they supported
   runtime behavior.
7. Runtime-critical changes were pushed directly to `main` without browser proof.
8. Tests largely asserted implementation tokens rather than visible behavior.
9. The agent claimed “no creative additions” while introducing layout, timing,
   metadata, event, and reparenting decisions.
10. Owner FAIL feedback was followed by another patch instead of source freeze,
    incident update, and read-only diagnosis.
11. A helper commit reached `main` before the complete implementation batch.

### Failed approaches that must not be repeated

- separate fixed white curtain;
- percentage rail anchors unrelated to viewport geometry;
- measuring moving copy and treating that measurement as stable ownership;
- moving title/description nodes between sections;
- separating sticky owners without proving the handoff;
- moving the entire semantic Program root at runtime;
- mutually coupled Vision and Program layout events;
- midpoint or direction rules chosen without an accepted storyboard;
- declaring runtime success from syntax, brace, diff, or publication checks.

### Current evidence boundary

```text
SOURCE_PUBLICATION: PASS
OWNER_RUNTIME_RESULT: FAIL
OWNER_VISUAL_RESULT: FAIL
ROOT_CAUSE: BLOCKED_BY_MISSING_EVIDENCE
```

### Required runtime evidence

```text
current commit SHA
browser/version and OS
viewport width x height and zoom
locale/direction
URL and initial hash
reload versus in-page navigation
input type and exact reproduction steps
screen recording from end of Visi/Misi through Program 2
runtime DOM parent chain
computed position/overflow/transform/sticky values
bounding rectangles for pin, track, Program root, white frame, and active media
scrollY plus controller target/current values at milestones
module mount order and enhancement state
```

### Mandatory prevention

- source mutation remains frozen until current state and blueprint are corrected;
- next work is read-only runtime diagnosis;
- each hypothesis must include falsifying evidence;
- candidate branch is mandatory for the next runtime-critical implementation;
- no promotion to `main` without Chromium/WebKit proof and owner visual review;
- future reports use claim-specific evidence, not a generic `PASS`.

### Status

`OPEN / GOVERNANCE_HARDENED / TECHNICAL_ROOT_CAUSE_UNRESOLVED`
