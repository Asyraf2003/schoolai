# MAP-V2-01 — Landing Page V2 — Shell + Menu + Hero

STATUS: BLOCKED_BY_MISSING_EVIDENCE untuk final closure; implementation tersedia.
BLUEPRINT: IMPLEMENTING sesuai scope owner; keputusan navigasi sudah OWNER_CONFIRMED.

## OWNER_RAW

Lihat [empat arahan mentah](../owner/landing-v2-raw.md), disimpan tanpa parafrasa.
Arahan lengkap owner pada sesi 2026-10-04 mengalahkan blueprint foundation lama.

## OWNER_CONFIRMED

- `resources/` aktif; `resources_old/` referensi; `docs2/` sumber keputusan V2.
- Blade minimal sejak awal; Laravel hanya delivery dan binding data.
- Scope hanya global foundation, shell, Menu/Header, Hero; EN dahulu.
- Urutan: foundation → shell → Menu → Hero static → responsive → media → motion → proof.
- Content/media CF dan URL sistem valid dipertahankan; runtime legacy tidak diimpor.
- Logic JS dipisahkan dari DOM/browser; satu behavior satu owner; tanpa abstraksi spekulatif.
- Tidak membangun section lain, loader global, admin/auth, ID/AR, atau Hero→About.
- Issue, branch, PR ke main; merge hanya setelah DoD terpenuhi.

## AI_TRANSLATION

- Ganti delivery `/` menjadi view landing terisolasi dari composer `welcome` lama.
- Shell EN memakai penerjemah existing dengan locale request EN; tidak mengubah preferensi sesi global.
- Foundation memuat reset, font, token, focus, media dan direction baseline.
- Menu dan Hero mempunyai partial Blade, CSS, core state, adapter DOM masing-masing.
- Entry JS hanya memasang komponen dan contract audio, bukan mengatur semua behavior.
- Preserve contract data Hero dari DB/PPDB/artikel melalui data boundary terpisah,
  tidak menjalankan seluruh controller/composer homepage lama.
- Port berupa fungsi yang dibutuhkan core, bukan kelas/interface untuk setiap browser API.

## AI_ASSUMPTIONS

NONE sebagai requirement. Detail yang belum terbukti ditulis UNKNOWN di peta teknis.

## SCOPE / OUT_OF_SCOPE

Editable: `resources/`, delivery route home, data/presenter landing bila diperlukan,
tests landing, `docs2/`. Legacy, admin/auth, dependency/lock files tidak dimutasi.
Tidak membangun halaman tujuan `/galeri`, `/artikel`, `/ppdb`, `/login` pada pekerjaan ini.
URL tersebut tetap dipertahankan sebagai URL sistem; ketersediaan halamannya dicatat terpisah.

## LEGACY_REFERENCE

[Peta sumber](../architecture/landing-v2-technical-map.md) menggolongkan source yang dibuka.
Audit source dilakukan sebelum frontend baru ditulis; computed CSS/runtime legacy belum dibuktikan.

## FACT → GAP → GOAL → IMPACT

- FACT: main `a44d484f4cd8bc532514f0152e4423a2fcdb9781`; branch `feat/home-v2-hero`.
- FACT saat baseline: resources masih skeleton; route `/` masih memanggil HomeController → `welcome` yang tidak ada di resources baru.
- GAP: tautan section lama kehilangan target dalam scope shell + Hero.
- GOAL: fondasi baru dapat dibangun per komponen tanpa coupling runtime legacy.
- IMPACT: route home dapat pulih secara bertahap; halaman non-landing tetap di luar scope.

## DECISION

- OWNER_CONFIRMED: EN-only, Blade, urutan Menu dahulu, pertahankan content/media.
- LEGACY_OBSERVATION: menu hamburger sampai 1180px, desktop mulai 1181px.
- AI_TRANSLATION: gunakan batas navigasi tersebut; ukur enam tier 360/640/768/1024/1280/1536.
- OWNER_CONFIRMED: item menuju section yang belum ada tetap tampil aktif dengan URL lama.
- UNKNOWN: persetujuan "no 3" dan "no 4 a" tidak memiliki pertanyaan asal;
  tidak digunakan untuk mengarang requirement tambahan.

## BLUEPRINT — contract baseline

| Tier | Menu | Hero |
| --- | --- | --- |
| 360–639 | Menu layar kecil, overflow isi dapat digulir | Satu kolom, tinggi mengikuti isi dan viewport |
| 640–767 | Menu layar kecil | Copy fluid, media cover dengan fokus existing |
| 768–1023 | Menu layar kecil | Ruang copy berkembang, tanpa DOM baru |
| 1024–1279 | Menu layar kecil ≤1180; desktop ≥1181 | Komposisi lebar dengan batas panjang baris |
| 1280–1535 | Desktop, panel media + links | Komposisi legacy, kontrol tidak menimpa copy |
| ≥1536 | Desktop, gutter fluid | Lebar copy dibatasi; media mengisi ruang |

EN aktif. ID/AR final ditunda; logical properties dan input direction mencegah lock-in LTR.
Locale-switch final ditunda; tidak membuat translation engine atau mengaktifkan ID/AR.
Header memiliki seluruh state panel/focus/scroll miliknya. Hero memiliki playback/slide.
Kontrol audio Header mengirim intent ke Hero dan menerima status aktual melalui port.
Konten tetap terbaca tanpa JS; enhancement gagal tidak boleh menutup navigasi.
Reduced motion mematikan autoplay dan motion non-esensial; poster tetap tersedia.
Tidak memasang WebGL/3D scene baru: audit tidak membuktikan kebutuhan Hero saat ini.

## ACTIVE STEP / EXECUTION

Foundation, shell, Menu, Hero static/responsive/media dan enhancement sudah tersedia.
Owner sudah memutuskan URL section tetap aktif. Bukti scoped direkam; final closure tertahan.

## PROOF / PROGRESS

Lihat [catatan bukti](../proof/landing-v2-status.md).
Browser scoped proof tersedia; visual parity penuh dan seluruh gate belum lengkap. Hero belum CLOSED.

## GIT

Repository: `Asyraf2003/schoolai`; base main: `a44d484`.
Satu target issue/PR: Landing Page V2 — Shell + Menu + Hero.
Perubahan lokal AGENTS.md, CLAUDE.md, composer.lock bukan bagian checkpoint ini.

## NEXT VALID STEP

Lengkapi gap proof pada status aktif dan review PR; jangan mulai section lain.
