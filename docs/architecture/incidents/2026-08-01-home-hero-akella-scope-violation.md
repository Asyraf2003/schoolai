# Home Hero Akella Transition Scope Violation

Status: CLOSED_BY_HISTORY_RECONSTRUCTION
Date: 2026-08-01
Owner: Asyraf Mubarak
Clean baseline: `f77545362801059ef801d480d660ca1ce638f464`
Affected reference: `akella/webGLImageTransitions`, Demo 1

## Owner request

The owner requested only a new media transition for the homepage Hero. Manual
navigation should follow the clicked physical arrow, automatic navigation should
follow locale direction, and incoming native video should begin playing
immediately.

The request did not authorize a redesign of the Hero controls, a darker media
treatment, a layout rewrite, or changed native-video playback semantics.

## Scope violations

The implementation exceeded the requested scope in three material ways:

1. Visible Hero controls and composition were changed even though only the media
   transition was requested.
2. The WebGL/canvas composition produced a darker appearance than the approved
   Hero presentation. Demo 1 did not require this darkness change.
3. Incoming video could be represented by a static poster during the transition
   while the native video advanced underneath, creating a visible jump or
   restart impression when the canvas disappeared.

These changes came from implementation assumptions, not explicit owner
instructions.

## Resolution

The owner rejected the complete Akella-related implementation lineage. The
`main` branch was reconstructed from the last commit before the repository was
introduced: `f77545362801059ef801d480d660ca1ce638f464`.

All production source, tests, scripts, workflow expectations, transition code,
WebGL code, shaders, textures, renderer state, UI changes, correction patches,
and rollback patches created during that lineage were excluded from the new
`main` ancestry.

This incident report is intentionally retained as the only durable record from
that work. It is documentation only and must not be imported, bundled, executed,
or treated as an active feature blueprint.

Old commits may remain reachable through archived pull requests, explicit SHA
references, or the safety backup branch. They are not part of the reconstructed
`main` lineage and must not be used as the starting point for future Hero work.

## Binding rules for future sessions

1. A transition-only request may change transition rendering and only the minimum
   state plumbing required to drive it.
2. It must not change control appearance or placement, copy, layout, crop,
   brightness, overlay strength, or unrelated UI without explicit owner approval.
3. Native incoming video must play continuously. Completing a transition must
   not call `load()`, reset `currentTime`, or replay the video to repair visuals.
4. Any visible gamma, color, or brightness shift is a regression unless the
   owner explicitly requests it.
5. Ambiguous product or art-direction decisions must be returned to the owner,
   not silently resolved through implementation preference.
6. Future Hero work must begin from current `main` source and runtime evidence,
   not from the retired Akella commits.
