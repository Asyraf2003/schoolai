# Homepage V2 AI working contract

Status: **ACTIVE PROTOCOL**

## OWNER_RAW

> "jadi rapi bener\" si ai bisa fokus hanya baca dari sana aja gituu"

> "jangan tambah\" asumsi anda, fokus ke masalah kita, klo ada asumsi tulsi asumsi"

> "terlihat mana arahan mentah saya, mana yg sudah diterjemahkan anda"

## AI_TRANSLATION

Setiap sesi AI yang mengerjakan homepage V2 harus dapat melanjutkan pekerjaan dari docs2 tanpa mengaudit ulang seluruh legacy repository.

AI wajib tahu mana yang berasal langsung dari owner dan mana yang merupakan interpretasi teknis AI.

## SESSION ENTRYPOINT

Sesi baru wajib membaca:

1. `docs2/README.md`
2. blueprint/map aktif
3. architecture file yang dirujuk map aktif
4. handoff aktif jika sudah ada

Jangan membaca seluruh `docs/` legacy secara default.

Jika source legacy perlu dibaca, map aktif harus menyebutnya sebagai `LEGACY_REFERENCE` dan menjelaskan apa yang dicari: behavior, visual, data, media, atau contract.

## MAP FORMAT WAJIB

Setiap map implementasi V2 harus mempunyai:

```text
# MAP-V2-XX — Nama target

STATUS:

## OWNER_RAW
Kata/arah owner yang menjadi sumber map.

## AI_TRANSLATION
Goal teknis yang diterjemahkan AI dari OWNER_RAW.

## AI_ASSUMPTIONS
Semua hal yang belum diberikan owner tetapi digunakan AI untuk berpikir.
Jika tidak ada, tulis NONE.

## OWNER_CONFIRMED
Keputusan hasil diskusi yang sudah dikonfirmasi owner.

## SCOPE
Apa yang boleh disentuh.

## OUT_OF_SCOPE
Apa yang tidak boleh disentuh.

## LEGACY_REFERENCE
Legacy source yang memang perlu dibaca dan alasannya.
Jika tidak perlu, tulis NONE.

## FACT
Temuan source/runtime yang terbukti.

## DECISION
Keputusan implementasi dan apakah berasal dari owner, evidence, atau assumption.

## PROOF
Test/browser/performance proof.

## GIT
Issue, branch, PR, commit/main SHA.

## NEXT VALID STEP
Satu langkah lanjut yang presisi.
```

## ANTI-ASSUMPTION RULE

AI tidak boleh:

- mengubah interpretasi menjadi "owner meminta";
- mengisi requirement yang tidak diberikan hanya karena dianggap best practice;
- membawa architecture legacy ke V2 tanpa alasan;
- memperluas scope karena melihat masalah menarik di section lain;
- menyatakan performance/UX PASS hanya dari automated test;
- membuat abstraction shared sebelum ada kebutuhan nyata.

Bila perlu menebak untuk melanjutkan analisis, tulis eksplisit:

```text
AI_ASSUMPTION: ...
WHY NEEDED: ...
RISK IF WRONG: ...
```

## ONE ACTIVE TARGET

Pekerjaan berjalan satu target pada satu waktu:

```text
section
→ seam/transition berikutnya
→ section berikutnya
```

Temuan di luar target dicatat sebagai `NEXT MAP`, bukan langsung dimutasi.

## CONTINUOUS GIT WORKFLOW

Saat implementasi sudah dimulai, setiap map harus punya jejak yang dapat dilanjutkan sesi lain:

```text
Issue
→ branch
→ audit/implementation
→ commits/checkpoints
→ tests/browser proof
→ PR
→ CI
→ merge main bila PASS
→ handoff CLOSED
→ next map
```

Jangan membuat satu branch besar untuk seluruh homepage V2.

## TOKEN / SESSION HANDOFF

Jika token/sesi hampir habis sebelum target CLOSED:

1. simpan source pada state aman;
2. commit dan push checkpoint bila ada mutation yang layak dipertahankan;
3. update Issue;
4. update handoff docs2;
5. tulis `NEXT VALID STEP` yang presisi;
6. jangan mengklaim target CLOSED.

Sesi berikutnya cukup membaca docs2 + Issue/branch/PR target aktif, lalu melanjutkan.

## HANDOFF MINIMUM

Handoff aktif minimal mencatat:

- active map;
- status;
- owner raw yang relevan atau link map;
- source main SHA;
- branch;
- issue;
- PR;
- commits/checkpoint;
- FACT terbaru;
- decision terbaru;
- tests/proof;
- blocker;
- exact next valid step.

## STATUS VOCABULARY

Gunakan status yang jelas:

- `PLANNED`
- `AUDITING`
- `IMPLEMENTING`
- `VERIFYING`
- `BLOCKED`
- `CLOSED`

Jangan memakai `CLOSED` jika acceptance target masih gagal.

## CURRENT STATE

Docs2 foundation sedang dibuat. Belum ada MAP-V2 implementasi yang aktif dari dokumen ini.

Map implementasi pertama nanti harus dimulai dari Hero sesuai blueprint foundation.
