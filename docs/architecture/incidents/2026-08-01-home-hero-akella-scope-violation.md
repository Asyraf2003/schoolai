# Home Hero Akella Transition Scope Violation

Status: CLOSED_BY_HISTORY_RECONSTRUCTION
Date: 2026-08-01
Owner: Asyraf Mubarak
Accepted stable Hero anchor: `f77545362801059ef801d480d660ca1ce638f464`
Accepted Hero pull request: `#26`
PR #26 base: `dda36abbc1f693294cfbc16b266f7e01d72975ec`
PR #26 proven head: `96ec64c1f77336009494703f273d0e338dd1e72a`
Affected reference: `akella/webGLImageTransitions`, Demo 1

## Accepted stable state

The owner accepts the Hero produced by PR #26 and squash commit
`f77545362801059ef801d480d660ca1ce638f464` as the stable production starting
point. Its Blade, CSS, JavaScript, media lifecycle, responsive behavior, and
visible controls are current product source unless a later explicit owner change
replaces them.

Commit `dda36abbc1f693294cfbc16b266f7e01d72975ec` is the base before PR #26, not
the desired final Hero. Future sessions must not roll the Hero back to `dda36ab`
merely because it predates the stabilization work.

## Owner request that triggered the rejected work

After the accepted PR #26 Hero, the owner requested only a new media transition.
Manual navigation should follow the clicked physical arrow, automatic navigation
should follow locale direction, and incoming native video should begin playing
immediately.

That request did not authorize a redesign of the accepted Hero controls, a
darker media treatment, a layout rewrite, or changed native-video playback
semantics.

## Scope violations

The later Akella-related implementation exceeded the requested scope in three
material ways:

1. Visible Hero controls and composition were changed even though only the media
   transition was requested.
2. The WebGL/canvas composition produced a darker appearance than the accepted
   PR #26 Hero. Demo 1 did not require this darkness change.
3. Incoming video could be represented by a static poster during the transition
   while the native video advanced underneath, creating a visible jump or
   restart impression when the canvas disappeared.

These changes came from implementation assumptions, not explicit owner
instructions.

## Resolution and history boundary

The owner rejected the complete Akella-related lineage created after
`f77545362801059ef801d480d660ca1ce638f464`. The `main` branch was reconstructed
from that accepted stable squash commit, then received only documentation that
records this incident.

All production source, tests, scripts, workflow expectations, transition code,
WebGL code, shaders, textures, renderer state, UI changes, correction patches,
and rollback patches from the rejected lineage were excluded from the new
`main` ancestry.

Those missing files are absent intentionally. Their absence is not a broken
migration, incomplete checkout, or invitation to recreate them. This report is
documentation only and must not be imported, bundled, executed, or treated as an
active feature blueprint.

Old commits and branches may remain reachable for audit, including archived pull
requests, explicit SHA references, and safety backup branches. They are legacy
records only. They must not be merged, cherry-picked, rebased, or used as the
starting point for future Hero work unless the owner explicitly reopens that
historical experiment.

Future normal commits pushed to `main` continue from the reconstructed line:

```text
accepted PR #26 squash f775453
-> retained incident documentation
-> future approved main commits
```

The accepted stable anchor remains in ancestry even when future commits make the
current `main` SHA newer.

## Binding rules for future sessions

1. Start from current `main`, while treating `f775453` as the accepted Hero
   history anchor.
2. A transition-only request may change transition rendering and only the minimum
   state plumbing required to drive it.
3. It must not change control appearance or placement, copy, layout, crop,
   brightness, overlay strength, or unrelated UI without explicit owner approval.
4. Native incoming video must play continuously. Completing a transition must
   not call `load()`, reset `currentTime`, or replay the video to repair visuals.
5. Any visible gamma, color, or brightness shift is a regression unless the
   owner explicitly requests it.
6. Ambiguous product or art-direction decisions must be returned to the owner,
   not silently resolved through implementation preference.
7. Do not report missing Akella files as an active GAP. They were intentionally
   excluded from `main`; this incident document is the retained explanation.
