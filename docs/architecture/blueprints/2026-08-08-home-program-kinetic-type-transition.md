# Homepage Program — Kinetic Type Transition Blueprint

Status: `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`
Date: 2026-08-09
Owner decision: keep the Codrops kinetic transition and desktop media/article geometry, while adapting title scale only when localized SchoolAI copy is longer than the reference title.
Reference: `https://github.com/codrops/KineticTypePageTransition`
Reference license: MIT.

## FACT

The Codrops desktop reference uses:

- `top: 20vh` and `height: 80vh` for the article region;
- `width: calc(38vw + 280px)`;
- grid columns `1.5rem 30% 1fr 1.5rem`;
- media in the flexible third column spanning the article height;
- a short display title at `8vw`, line-height `.85`, uppercase and bold weight;
- body copy at `1rem`;
- image radius `17px 17px 0 0`.

Owner runtime proof established a content mismatch rather than a media-geometry mismatch: SchoolAI localized titles can be substantially longer than `HARMONY`. Keeping `8vw` for every title while reserving a fixed `12vw` row caused wrapped titles to overflow into the description.

## GOAL

Opening a Program card retains the current kinetic transition and Codrops-like media composition while rendering only:

- localized `<<< Back` control;
- selected Program title;
- one description;
- one selected Program image.

Longer localized titles must preserve the same editorial character without colliding with the description.

## DESKTOP DETAIL CONTRACT

- detail article: `top: 20svh`, `height: 80svh`, `width: calc(38vw + 280px)`;
- grid rows: `10vw 2rem auto auto 1fr`;
- grid columns: `1.5rem 30% 1fr 1.5rem`;
- media: column 3, all rows, full article height, unchanged from the accepted geometry;
- Back: column 2, row 2, directly above title and never viewport-fixed;
- title: columns `2 / 4`, row 3, deliberate media overlap, `.85` Latin line-height and bold reference-style weight;
- description: column 2, row 4, `1rem` body scale and naturally positioned after the title;
- image radius: `17px 17px 0 0`;
- SchoolAI Inter/Cairo families remain the type-family adaptation.

### Localized title scaling

Blade derives a presentation tier from the actual localized title length, not program identity or locale hardcoding:

- `short`: <= 12 characters -> `8vw`;
- `medium`: <= 20 characters -> `5.75vw`;
- `long`: > 20 characters -> `4.75vw`.

Desktop titles use balanced wrapping. RTL uses the same length tiers while preserving Cairo and Arabic-specific line-height/casing.

The purpose is not to make long titles visually small. It is to keep them in the same dominant editorial role while preventing three-line overflow from colliding with description copy.

## COMPACT CONTRACT

Compact layouts keep the existing one-column adaptation. Desktop length-tier overrides are not used to redesign compact layouts.

## RTL / LOCALE CONTRACT

- Public Program copy remains locale-owned in `lang/id/home_program.php`, `lang/en/home_program.php`, and `lang/ar/home_program.php`.
- No title tier is assigned by hardcoded program name or language.
- Arabic keeps Cairo and normal Arabic casing.
- Desktop media/copy ownership mirrors logically under RTL.
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

- accepted detail media geometry in this correction;
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

- the same desktop `Taman Kanak-kanak` case no longer collides with its description;
- short titles preserve the reference-like 8vw impact;
- medium/long titles remain dominant and generally resolve in one or two lines;
- description starts after the actual title height;
- media remains visually unchanged from the accepted previous correction;
- Back remains directly above title and reachable below the site header;
- RTL mirrors logically;
- compact tiers remain usable;
- Chromium and WebKit.

Until runtime proof exists, status remains `IMPLEMENTED_SOURCE / BLOCKED_BY_MISSING_EVIDENCE`.
