# Vision/Mission Effect 25 Scroll Story

BLUEPRINT ID: `HOME-VISION-002`
STATUS: `IMPLEMENTING`
OWNER: Asyraf Mubarak
DATE: 2026-08-03
SOURCE MAIN SHA: `3e0b6ea36b5989a88202c51ee4d063a1d3c3259b`
ACTIVE ROUTE/SURFACE: homepage Vision/Mission
TARGET EXECUTION CHANNEL: Web AI GitHub direct `main`

## Goal and reference

Remove the About surface completely and make Vision/Mission the sole homepage
scroll-typography story. Every editorial beat translates Codrops On-Scroll
Typography Animations Set 2 effect 25: large left-aligned text remains in a
sticky viewport while glyphs grow vertically from their baseline as normal
page scroll advances. School fonts, content, colors, semantics, and normal
browser scrolling remain authoritative; reference code and type assets are not
copied.

## Facts and decisions

- The first published version still rendered About and mixed four motion styles.
- Owner feedback removes About, its local image layers, and its tests/entries.
- All Vision/Mission text scenes use one vertical `scaleY` grammar.
- Existing marked phrases keep blue, orange, green, or purple emphasis.
- ID/EN animate complete Unicode characters.
- Arabic animates complete words, never isolated letters, preserving joining
  and shaping while keeping sequential reveal.
- Native requestAnimationFrame responds only to scroll, resize, and reduced
  motion changes. No package, font, smooth-scroll, or WebGL dependency is added.

## Scope

Editable: Vision/Mission partial, dedicated CSS, story controller/splitter,
homepage and Vite entries, focused test, prior blueprint/current-state docs,
and deletion of the About source owners.

Protected: Hero, Testimonial, School Values, Programs, Gallery, Articles,
navigation, translations, authentication, database, dependencies, and assets.

## Storyboard

Each opening, vision, bridge, and mission beat owns a long bounded scene with a
100svh sticky stage. Text starts vertically collapsed at its baseline and grows
sequentially to full height. Reverse scroll reverses the reveal. Key phrases
retain semantic accent colors. No-JS and failure states show complete static
text. Reduced motion removes long travel and sticky positioning.

## Six-tier and locale contract

One semantic DOM serves XS, SM, MD, LG, XL, and 2XL. Scene travel and page
padding increase at 640, 768, 1024, 1280, and 1536px while typography remains
fluid. ID/EN use the SchoolAI Latin display owner and LTR character reveal. AR
uses Cairo/RTL, normal tracking, and word-level reveal. Text alignment follows
logical start; vertical scroll time is not mirrored.

## Proof gates

Required: diff check, structure check, Vite build, focused and full PHP tests,
six-tier ID/EN/AR review, Chromium/WebKit, reduced motion, 200% zoom,
fast/reverse scroll, resize/orientation, BFCache, and PageSpeed comparison.
Publication alone proves source state only.
