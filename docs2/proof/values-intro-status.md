# Values intro #67 — 2026-10-08

Scoped PASS. Main source9b9501b5, branch feat/values-intro.
Owner requested title/background only and EN Program without horizontal slide.

EN/AR Program final x0; ID remains760ms reveal then1200ms weighted slide,
187.2px at1440. Values reuses one existing20-line type plane through the seam.
White→#2038ff follows old Program-bottom1.2→.68viewport range with smoothstep;
samples0/49.96/100/49.96/0% prove forward and reverse color equality.
One native-scroll scheduled frame, no perpetual RAF loop; visibility and page
suspend/resume/disposal retain static semantic fallback.

Values heading: existing localized two-line copy,900ms reveal then880ms slide
with old cubic-bezier(.22,1,.36,1). At1440 x0 through900ms,96.9074 at1340ms,
100.8px at1780ms; AR signs mirrored. Below768px old horizontal distance is0.
Header space is reserved for title readability; Cairo/RTL uses1.35leading
to preserve current V2 diacritic clearance. No cards/cartoon/subtitle/media.

9 Chromium browser tests PASS: Program normal/reduced/no-JS/reentry/motion,
Values21locale/tier layout cells,3sequence/detail cells,27fallback cells.
The shared plane spans the Values bottom, masks fit their title width and both
lines remain below the actual Header in layout proof. BFCache suspend/resume PASS.
Full GSAP3.7.1 open/close and home/dialog type restoration PASS through a fixture;
existing detail source remains unchanged. Reduced/no-JS/missing-IO remain readable.

PHP V2:27tests/333assertions PASS; focused JS7PASS.
Full PHP baseline332/186pass/71failure/75error/1644assertions;
final336/190pass/71failure/75error/1687assertions. Same failure/error names.
Full Node Runtime baseline/final38tests:19pass,7fail,12skip; same failures.
Build/structure282sources<200lines/Pint/diff PASS.460protected paths unchanged.
No dependency or legacy-source changes. Full repository gates remain FAIL.

Data/hashes: [values-intro-67.json](values-intro-67.json).
Browser media/CDN access is unavailable in this runtime; layout used the exact
64×64canonical logo downloaded from the existing URL, and detail used the exact
GSAP3.7.1 library. Other live media, WebKit/native device, CWV and whole-site
certification are not claimed. Fixture source and hashes are in the data file.

Next: publish PR/main under existing owner authorization, then owner pulls.
Values cards/cartoon await the next bounded owner instruction.
