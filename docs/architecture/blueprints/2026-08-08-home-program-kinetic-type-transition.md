# Homepage Program — Kinetic Type Transition Blueprint

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Date: 2026-08-09
Owner decision: keep the Codrops kinetic transition and match its desktop detail geometry directly while retaining SchoolAI fonts, locale content and the owner-required local Back control.
Reference: `https://github.com/codrops/KineticTypePageTransition`
Reference license: MIT.

## FACT

The Codrops desktop article source uses:

- `top: 20vh` and `height: 80vh` for the article region;
- `width: calc(38vw + 280px)`;
- grid rows `10vw 2rem 12vw auto auto`;
- grid columns `1.5rem 30% 1fr 1.5rem`;
- media in the flexible third column spanning the article height;
- title spanning columns 2 through 3 at `8vw`, line-height `.85`, uppercase and bold weight;
- body copy at the inherited base `1rem` scale;
- image radius `17px 17px 0 0`.

The previous SchoolAI adaptation capped media at `36rem × 43rem`, which made the media materially smaller and vertically centered instead of starting at 20vh and reaching the viewport bottom.

## GOAL

Opening a Program card should retain the current kinetic transition, then resolve into a detail composition whose desktop geometry reads like the Codrops reference while containing only:

- localized `<<< Back` control;
- selected Program title;
- one description;
- one selected Program image.

## DESKTOP DETAIL CONTRACT

- detail article: `top: 20svh`, `height: 80svh`, `width: calc(38vw + 280px)`;
- grid rows: `10vw 2rem 12vw auto 1fr`;
- grid columns: `1.5rem 30% 1fr 1.5rem`;
- media: column 3, all rows, full article height;
- Back: column 2, row 2, directly above title and never viewport-fixed;
- title: columns `2 / 4`, row 3, deliberate media overlap, `8vw`, `.85` line-height, bold weight;
- description: column 2, row 4, `1rem` body scale;
- image radius: `17px 17px 0 0`;
- SchoolAI Inter/Cairo families remain the only intentional type-family deviation from the reference.

## COMPACT CONTRACT

Compact layouts keep the existing usable one-column adaptation. Desktop coordinates are not blindly forced onto tablet/mobile. Back remains above title and media remains reachable without viewport-fixed controls.

## RTL / LOCALE CONTRACT

- Public Program copy remains locale-owned in `lang/id/home_program.php`, `lang/en/home_program.php`, and `lang/ar/home_program.php`.
- Arabic keeps Cairo and normal Arabic casing.
- Desktop media/copy grid ownership mirrors logically under RTL.
- Triple-chevron Back mark mirrors visually to the return direction.

## MOTION CONTRACT

Retain without modification:

- GSAP type transition scale/rotation/line travel;
- idle-card exit choreography;
- detail copy vertical reveal;
- media wrapper `100% -> 0` and image `-100% -> 0` reveal;
- reversible close timeline;
- Escape, Tab containment and focus restoration;
- reduced-motion semantic fallback.

## OUT OF SCOPE

Do not change:

- idle cards or `2 + 4 / 3 + 3 / 2 + 2 + 2` geometry;
- eleven-step Visi/Misi -> Program handoff;
- Program Center Split heading choreography;
- Visi/Misi;
- Hero, Values, Gallery, Articles, navbar or footer.

## PROOF GATE

Static/local:

- `git diff --check`
- `php artisan test --filter=HomeProgramJourneyTest`
- full `php artisan test`
- `npm run check:structure`
- `npm run build`

Runtime:

- 1920px desktop side-by-side reference comparison confirms media top/height/width and title overlap character;
- Back remains immediately above title and is not covered by the site header;
- title and description scales match the reference grammar while using SchoolAI fonts;
- all six details open/close correctly;
- RTL mirrors logically;
- compact tiers remain usable;
- Chromium and WebKit.

Until runtime proof exists, status remains `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`.