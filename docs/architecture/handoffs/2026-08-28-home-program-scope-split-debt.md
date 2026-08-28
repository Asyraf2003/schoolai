# Homepage Program Scope Split — Technical Debt

Date: 2026-08-28
Status: ACTIVE DECISION / DEFERRED FOLLOW-UP

## Decision

The homepage `Program Kami / Our Programs / برامجنا` section must show only the eight official featured programs. The previous 19-card composition was too dense for a landing-page narrative and made the section read like a full catalog instead of a curated showcase.

Active homepage Program codes:

1. `TQ` — Tahfidz Al-Qur'an / Qur'an Memorization / تحفيظ القرآن
2. `KH` — Khitobah (Public Speaking) / الخطابة
3. `LC` — Arabic & English Club / نادي العربية والإنجليزية
4. `SJ` — Pembiasaan Sholat Berjamaah / Congregational Prayer Habit / تعويد الصلاة جماعة
5. `TS` — Taekwondo & Swimming / التايكوندو والسباحة
6. `IT` — IT Class / حصة تقنية المعلومات
7. `FD` — Full Day School / الدوام المدرسي الكامل
8. `SC` — Small Class Concept / مفهوم الصفوف الصغيرة

Responsive formation for this section is locked to:

- Desktop: `4 + 4`
- Tablet: `4 + 4`
- Mobile: `2 + 2 + 2 + 2`

The full 19-item localized inventory remains in `lang/{id,en,ar}/home_program.php`. The homepage Composer filters that inventory to the eight codes above. Do not delete the deferred items from the translation inventory until their future sections are implemented.

## Deferred Section A — Jenjang Pendidikan

Technical debt codes: `PG`, `TK`, `SD`.

Canonical Indonesian scope:

- PAUD Tahfidz / Kelompok Bermain
- Kindergarten (TK)
- Primary School (SDIT)

These should become a dedicated education-level section rather than returning to the featured Program grid.

## Deferred Section B — School Programs

Technical debt codes: `MD`, `GG`, `EN`, `MH`, `CC`, `PH`, `SF`, `OC`.

Canonical scope:

- Market Day (Healthy Shopping)
- Go Green
- Entrepreneur / Entrepreneurship
- Manasik Haji
- Cooking Class
- PHBI
- Scientific / Fun Experiment
- Outing Class

These should become a dedicated School Programs / activity section with its own information hierarchy rather than being mixed into the featured Program grid.

## Constraints for both future sections

Any future implementation must preserve:

- ID, EN, and AR content parity.
- Correct LTR/RTL logical layout.
- Mobile, tablet, and desktop responsive behavior.
- Safari and Chromium compatibility.
- Existing localized copy in `home_program.php` as the source inventory unless content is intentionally revised.
- The current eight-card featured Program section as a separate curated surface.

## Debt exit criteria

This debt is considered closed only when both deferred groups have dedicated approved sections, three-language parity tests, responsive geometry contracts, and visual verification across LTR and RTL. Until then, the content remains intentionally stored but not rendered on the homepage Program section.
