# Menu/Hero correction — current status

Date: 2026-10-05. Branch `feat/home-v2-hero`, draft PR #64, Issue #63.
STATUS: BLOCKED_BY_MISSING_EVIDENCE for final closure: owner UI review pending.
Hero CLOSED = NO. Merge = NO, explicitly prohibited by owner.
Active blueprint: [MAP-V2-02](../blueprints/menu-hero-correction.md).

## Implemented
Media 2:3 with shared frame on tablet/full-menu; four fixed slots per column;
HP dropdown contains menu only (no image/caption), per latest OWNER_RAW correction.
Language EN/ID/AR uses existing POST/session/cookie, dynamic lang/dir. RTL foundation
ready; RTL visual tuning pending. Single logo, yellow borders, white open surface,
white hover over Hero, boundary-based scroll, wave by capability, animated compact
exit, Sound flow, poster/text fallback, automatic slides without pause control.

## Required checks
- PASS: git diff --check.
- PASS: npm run check:structure, 247 sources under 200 lines.
- PASS: npm run build, CSS 11.27kB / JS 13.31kB before gzip.
- PASS: Pint dirty.
- PASS: LandingV2Test, 7 tests / 46 assertions.
- PASS: LandingV2Runtime, 4 tests.
- FAIL: complete PHP suite, 312 tests, 166 passed, 71 failed, 75 errors.
  Legacy archived-path expectations remain; no legacy-wide CI repair performed.

## Browser evidence
- [Current matrix](correction-browser.json): Chromium/Firefox/WebKit, phone,
  tablet portrait/landscape, desktop; language switching and geometry.
- [Autoplay fixture](correction-autoplay.json): two image slides built from raw
  current server HTML, automatic progression verified in all three engines.
  Earlier matrix's Chromium autoplay=false used a mutated DOM snapshot and a
  fixed five-second sample; it is superseded by the raw-HTML event-based check.
- [Edge matrix](correction-edge.json): Edge 154.0.4258.53, nine sizes 360–1536,
  EN/ID/AR switching, no horizontal overflow or JS errors. Captured before the
  final stalled-media guard (which avoids timing out paused/ready media).
- [Edge behavior](correction-edge-behavior.json): actual canonical video/audio,
  white hover, yellow accent, slots 1–5, scroll boundary, mobile no wave,
  exit animation and close, failed-media fallback, mobile media hidden.
- Earlier session ran same behavior checks in Chromium153 / Firefox155 /
  WebKit26.6 successfully; temporary JSON was lost when environment restarted.
  Those observations are not substituted for retained artifacts.
- Earlier axe run: zero violations for 390/1181/1536 open/closed; temporary
  artifact lost, so this is a session observation, not durable accessibility certification.
- Native Safari NOT VERIFIED. WebKit automation is not Safari certification.
- No Lighthouse/field performance claim. No full Arabic visual certification.

## Review images
[HP menu](correction-menu-390.png), [desktop menu](correction-menu-1536.png).
Media URLs remain canonical. Screenshots are presentation evidence only.

## Handoff / next valid step
Owner inspects UI in PR #64. Do not merge, activate another section, or start
Hero→About. Main remains untouched. AGENTS.md, CLAUDE.md and composer.lock were
pre-existing local edits and are excluded from this patch.
