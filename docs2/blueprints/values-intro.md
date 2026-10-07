# MAP-V2-11 — Program → Values background and heading

STATUS: PROVEN (scoped PASS; full repository gates retain baseline FAIL). Issue #67, branch feat/values-intro.
Source main: 9b9501b5593cb468808c602733885df5e3b90da9.

## OWNER_RAW

EN Our Programs keluar biasa; hanya ID memiliki geser baris bawah.
Perhatikan old Values: teks latar Program berubah ke biru; buat latar dan judul
dengan efek geser seperti old dahulu. Kartu/kartun menyusul setelah ini.

## AI_TRANSLATION

EN retains vertical reveal but has zero horizontal travel. ID remains760/1200ms.
Continue one existing decorative plane across a shared Program/Values surface.
Morph white to old #2038ff as Program bottom crosses1.2→.68 viewport heights.
Values uses existing localized two-line heading, split reveal900ms then lower
line shift880ms, old responsive distance from768px and mirrored RTL.

## AI_ASSUMPTIONS

The title-only Values section reserves at least one viewport for review until
cards are requested. No subtitle or card content is rendered in this step.

## OWNER_CONFIRMED

Only Program EN correction, Values background transition and heading. Existing
issue/PR/main publication instruction applies to this follow-up.

## SCOPE

V2 composition/Values view and presenter, EN locale factor, shared background
adapter, Values heading CSS/IO, seam selectors required by the composition,
existing Program background visibility observation, focused tests/docs/proof.

## OUT_OF_SCOPE

Values cards/cartoon/media, subtitle, gallery/scroll line, Header white paint,
About/Hero/nav/content changes, dependency updates, Program detail choreography.

## LEGACY_REFERENCE

resources_old/js/surfaces/home/program-values-world.js: color morph geometry.
resources_old/css/surfaces/home/values/story-kinetic.css: shared field/blue.
resources_old/js/surfaces/home/values/{heading-state,layout}.js: staged reveal.
resources_old/css/surfaces/home/values/story-{heading,heading-responsive,responsive}.css:
heading font, line masks, shift distance/RTL. school-values Blade: semantic title.
lang/{id,en,ar}/home.php: existing heading and heading_lines, not rewritten.

## FACT / DECISION

The existing type plane remains inside Program for dialog queries/restoration;
its absolute containing block becomes the common world, extending into Values.
No second type plane/controller or GSAP change. World paints on native scroll
events via one scheduled frame while visible, with no continuous RAF loop.
No-JS/reduced/capability fallback uses a static blue Values and gradient seam.
Reduced motion preserves readable text without staged motion.

## PROOF

Browser: six tiers/locales, white→blue/reverse, staged heading/RTL, no overflow,
no-JS/reduced/missing IO, detail fallback restoration, lifecycle cleanup.
PHP localized route/data/accessibility, build/structure/diff/full-suite baseline.
Unexecuted browser engines/native hardware are gaps, never claimed PASS.

## GIT

Issue #67; PR/main publication after bounded proof. No force push.

## NEXT VALID STEP

Publish the verified bounded PR into main per owner instruction, then owner pulls.
Cards/cartoon remain deferred until a separate owner request.
