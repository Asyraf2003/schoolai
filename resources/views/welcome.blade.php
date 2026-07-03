<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
      Sekolah Ceria Nusantara — TK &amp; SD Modern, Ceria, dan Ramah Anak
    </title>
    <meta
      name="description"
      content="Sekolah Ceria Nusantara adalah sekolah TK dan SD modern dengan kurikulum aktif, guru ramah anak, dan lingkungan yang aman dan menyenangkan. PPDB 2026/2027 telah dibuka!"
    />
    <link
      rel="icon"
      href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.85em%22 font-size=%2290%22>🎨</text></svg>"
    />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body data-page="welcome">
    <!-- Skip link untuk aksesibilitas keyboard -->
    <a href="#main-content" class="skip-link">Langsung ke konten utama</a>

    <!-- Definisi SVG yang dipakai berulang (wave divider) agar file tetap ringan -->
    <svg width="0" height="0" style="position: absolute" aria-hidden="true">
      <defs>
        <path
          id="wave-shape"
          d="M0,40 C240,110 480,-20 720,40 C960,100 1200,-10 1440,40 L1440,120 L0,120 Z"
        ></path>
        <path
          id="blob-shape"
          d="M45.6,-58.3C58.4,-49.6,67.4,-33.9,71.6,-16.6C75.8,0.7,75.2,19.7,66.9,34.2C58.6,48.7,42.6,58.7,25.6,64.5C8.6,70.3,-9.4,71.9,-25.8,66.6C-42.2,61.3,-57,49.1,-65.4,33.4C-73.8,17.7,-75.8,-1.5,-70.4,-18.1C-65,-34.7,-52.2,-48.7,-37.3,-57C-22.4,-65.3,-11.2,-67.9,4.4,-74C20,-80.1,45.6,-67.1,45.6,-58.3Z"
        ></path>
      </defs>
    </svg>

    <!-- ======================= NAVBAR ======================= -->
    <header class="navbar" id="navbar">
      <div class="navbar__inner container">
        <a href="#beranda" class="navbar__logo">
          <span class="navbar__logo-icon">🌈</span>
          <span class="navbar__logo-text">
            Sekolah Ceria<br /><small>Nusantara</small>
          </span>
        </a>

        <nav class="navbar__menu" id="navMenu" aria-label="Menu utama">
          <ul>
            <li><a href="#beranda" class="nav-link active">Beranda</a></li>
            <li><a href="#program" class="nav-link">Program</a></li>
            <li><a href="#galeri" class="nav-link">Galeri</a></li>
            <li><a href="#artikel" class="nav-link">Artikel</a></li>
            <li><a href="#ppdb" class="nav-link">PPDB</a></li>
            <li><a href="#kontak" class="nav-link">Kontak</a></li>
          </ul>
          <a href="#ppdb" class="btn btn--primary navbar__cta">Daftar PPDB</a>
        </nav>

        <button
          class="hamburger"
          id="hamburgerBtn"
          aria-label="Buka menu"
          aria-expanded="false"
          aria-controls="navMenu"
        >
          <span></span><span></span><span></span>
        </button>
      </div>
    </header>

    <!-- Overlay gelap saat menu mobile terbuka -->
    <div class="nav-overlay" id="navOverlay"></div>

    <main id="main-content">
      <!-- ======================= HERO ======================= -->
      <section class="hero" id="beranda">
        <div class="hero__decor" aria-hidden="true">
          <span
            class="floaty floaty--star"
            style="--x: 8%; --y: 18%; --delay: 0s"
            >⭐</span
          >
          <span
            class="floaty floaty--cloud"
            style="--x: 78%; --y: 12%; --delay: 0.6s"
            >☁️</span
          >
          <span
            class="floaty floaty--pencil"
            style="--x: 4%; --y: 70%; --delay: 1.1s"
            >✏️</span
          >
          <span
            class="floaty floaty--balloon"
            style="--x: 88%; --y: 65%; --delay: 0.3s"
            >🎈</span
          >
          <div class="blob blob--yellow blob--1"></div>
          <div class="blob blob--blue blob--2"></div>
        </div>

        <div class="container hero__inner">
          <div class="hero__content reveal">
            <p class="eyebrow eyebrow--pink">
              🎉 PPDB Tahun Ajaran 2026/2027 Resmi Dibuka
            </p>
            <h1 class="hero__title">
              Tempat Ceria untuk Anak
              <span class="text-highlight">Tumbuh, Bermain,</span> dan Belajar
            </h1>
            <p class="hero__subtitle">
              Sekolah Ceria Nusantara memadukan kurikulum modern, guru yang
              hangat, dan lingkungan yang aman supaya si kecil selalu semangat
              berangkat sekolah setiap hari.
            </p>
            <div class="hero__actions">
              <a href="#ppdb" class="btn btn--primary btn--lg"
                >Daftar PPDB Sekarang</a
              >
              <a href="#program" class="btn btn--ghost btn--lg"
                >Lihat Program</a
              >
            </div>
          </div>

          <div class="hero__visual reveal reveal--delay-1">
            <div class="hero__illustration" id="tiltIllustration">
              <div class="illustration-card">
                <div class="illustration-sun">☀️</div>
                <div class="illustration-building">
                  <div class="roof"></div>
                  <div class="wall">
                    <span>🏫</span>
                  </div>
                </div>
                <div class="illustration-kids">🧒🎒 🧑‍🎓📚 👧🖍️</div>
                <div class="illustration-grass"></div>
              </div>

              <div class="sticker-card sticker-card--1">
                <span class="sticker-card__icon">✅</span>
                <span>Akreditasi A</span>
              </div>
              <div class="sticker-card sticker-card--2">
                <span class="sticker-card__icon">🤗</span>
                <span>Guru Ramah Anak</span>
              </div>
              <div class="sticker-card sticker-card--3">
                <span class="sticker-card__icon">🎨</span>
                <span>Kelas Aktif &amp; Kreatif</span>
              </div>
            </div>
          </div>
        </div>

        <div class="wave-divider" aria-hidden="true">
          <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <use href="#wave-shape" class="wave-fill-cream"></use>
          </svg>
        </div>
      </section>

      <!-- ======================= STATISTIK ======================= -->
      <section class="stats-ribbon reveal">
        <div class="container stats-ribbon__grid">
          <div class="stat-item">
            <span class="stat-item__number" data-count="320" data-suffix="+"
              >0</span
            >
            <span class="stat-item__label">Siswa Aktif</span>
          </div>
          <div class="stat-item">
            <span class="stat-item__number" data-count="32">0</span>
            <span class="stat-item__label">Guru &amp; Staff</span>
          </div>
          <div class="stat-item">
            <span class="stat-item__number" data-count="18">0</span>
            <span class="stat-item__label">Program Kegiatan</span>
          </div>
          <div class="stat-item">
            <span class="stat-item__number" data-count="12">0</span>
            <span class="stat-item__label">Tahun Pengalaman</span>
          </div>
        </div>
      </section>

      <!-- ======================= QUICK INFO ======================= -->
      <section class="quick-info section">
        <div class="container">
          <div class="quick-info__grid">
            <div class="info-card reveal">
              <span
                class="info-card__icon"
                style="background: var(--color-yellow-soft)"
                >📅</span
              >
              <h3>Tahun Ajaran 2026/2027</h3>
              <p>Persiapan kelas baru untuk TK dan SD sudah dimulai.</p>
            </div>
            <div class="info-card reveal reveal--delay-1">
              <span
                class="info-card__icon"
                style="background: var(--color-mint-soft)"
                >📝</span
              >
              <h3>Pendaftaran Dibuka</h3>
              <p>Gelombang 1 sedang berlangsung, kuota terbatas tiap kelas.</p>
            </div>
            <div class="info-card reveal reveal--delay-2">
              <span
                class="info-card__icon"
                style="background: var(--color-blue-soft)"
                >🎒</span
              >
              <h3>Jenjang TK &amp; SD</h3>
              <p>Mulai dari Kelompok Bermain hingga Sekolah Dasar kelas 6.</p>
            </div>
            <div class="info-card reveal reveal--delay-3">
              <span
                class="info-card__icon"
                style="background: var(--color-pink-soft)"
                >⏳</span
              >
              <h3>Kuota Terbatas</h3>
              <p>Setiap kelas hanya menerima jumlah siswa yang terbatas.</p>
            </div>
          </div>
        </div>
        <div class="wave-divider" aria-hidden="true">
          <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <use href="#wave-shape" class="wave-fill-yellow"></use>
          </svg>
        </div>
      </section>

      <!-- ======================= PPDB ======================= -->
      <section class="ppdb-section" id="ppdb">
        <div class="container">
          <div class="ppdb-panel reveal">
            <div class="ppdb-panel__decor" aria-hidden="true">
              <span
                class="floaty floaty--star"
                style="--x: 90%; --y: 8%; --delay: 0.2s"
                >✨</span
              >
              <span
                class="floaty floaty--book"
                style="--x: 3%; --y: 75%; --delay: 0.9s"
                >📚</span
              >
            </div>
            <div class="ppdb-panel__text">
              <p class="eyebrow eyebrow--white">
                Penerimaan Peserta Didik Baru
              </p>
              <h2 class="section-title section-title--white">
                Daftarkan Si Kecil, Mulai Petualangan Belajarnya
              </h2>
              <p class="ppdb-panel__desc">
                Prosesnya mudah dan tidak ribet. Tim kami siap membantu mulai
                dari pengisian formulir sampai hari pertama anak bersekolah.
              </p>
              <a href="#" class="btn btn--white btn--lg"
                >Klik ke Pendaftaran PPDB</a
              >
            </div>

            <ol class="ppdb-steps">
              <li class="ppdb-step">
                <span class="ppdb-step__num">1</span>
                <h4>Isi Formulir</h4>
                <p>Lengkapi data anak dan orang tua secara online.</p>
              </li>
              <li class="ppdb-step">
                <span class="ppdb-step__num">2</span>
                <h4>Verifikasi Data</h4>
                <p>Tim admin memeriksa kelengkapan berkas pendaftaran.</p>
              </li>
              <li class="ppdb-step">
                <span class="ppdb-step__num">3</span>
                <h4>Observasi Anak</h4>
                <p>Sesi kenalan singkat yang santai dan menyenangkan.</p>
              </li>
              <li class="ppdb-step">
                <span class="ppdb-step__num">4</span>
                <h4>Pengumuman</h4>
                <p>Hasil pendaftaran dikirim lewat WhatsApp dan email.</p>
              </li>
            </ol>
          </div>
        </div>
      </section>

      <!-- ======================= VISI MISI ======================= -->
      <section class="visi-misi section">
        <div class="container visi-misi__grid">
          <div class="visi-card reveal">
            <span class="eyebrow eyebrow--blue">🧭 Visi Kami</span>
            <p class="visi-card__text">
              Menjadi sekolah TK dan SD pilihan keluarga Indonesia yang
              membentuk anak ceria, mandiri, dan berkarakter kuat sejak usia
              dini.
            </p>
          </div>
          <div class="misi-list">
            <div class="misi-card reveal reveal--delay-1">
              <span class="misi-card__icon">🌱</span>
              <p>
                Menghadirkan pembelajaran aktif yang menumbuhkan rasa ingin tahu
                anak.
              </p>
            </div>
            <div class="misi-card reveal reveal--delay-2">
              <span class="misi-card__icon">🤝</span>
              <p>
                Membangun karakter santun dan mandiri melalui kebiasaan
                sehari-hari.
              </p>
            </div>
            <div class="misi-card reveal reveal--delay-3">
              <span class="misi-card__icon">🏡</span>
              <p>
                Menyediakan lingkungan sekolah yang aman, hangat, dan
                menyenangkan.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- ======================= NILAI SEKOLAH ======================= -->
      <section class="nilai-section section">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--purple">Nilai yang Kami Tanamkan</p>
            <h2 class="section-title">4 Nilai Utama Sekolah Ceria Nusantara</h2>
          </div>
          <div class="nilai-grid">
            <div class="nilai-card nilai-card--yellow reveal">
              <span class="nilai-card__icon">😄</span>
              <h3>Ceria</h3>
              <p>
                Anak belajar dengan gembira lewat permainan dan cerita setiap
                hari.
              </p>
            </div>
            <div class="nilai-card nilai-card--mint reveal reveal--delay-1">
              <span class="nilai-card__icon">🌱</span>
              <h3>Mandiri</h3>
              <p>
                Dibiasakan mengurus keperluan sendiri sesuai dengan usianya.
              </p>
            </div>
            <div class="nilai-card nilai-card--pink reveal reveal--delay-2">
              <span class="nilai-card__icon">🤝</span>
              <h3>Santun</h3>
              <p>Dibimbing berkata baik dan menghargai teman serta guru.</p>
            </div>
            <div class="nilai-card nilai-card--purple reveal reveal--delay-3">
              <span class="nilai-card__icon">🎨</span>
              <h3>Kreatif</h3>
              <p>
                Diberi ruang untuk berkarya lewat seni, cerita, dan proyek
                sederhana.
              </p>
            </div>
          </div>
        </div>
        <div class="wave-divider" aria-hidden="true">
          <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <use href="#wave-shape" class="wave-fill-mint"></use>
          </svg>
        </div>
      </section>

      <!-- ======================= PROGRAM UNGGULAN ======================= -->
      <section class="program-section section" id="program">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--orange">Kurikulum Kami</p>
            <h2 class="section-title">Program Unggulan</h2>
            <p class="section-subtitle">
              Rangkaian program yang dirancang supaya anak belajar sambil
              bermain.
            </p>
          </div>
          <div class="program-flow">
            <article class="program-card program-card--rot-1">
              <span
                class="program-card__icon"
                style="background: var(--color-pink-soft)"
                >📖</span
              >
              <h3>Kelas Literasi Ceria</h3>
              <p>
                Membaca dan mendongeng interaktif untuk menumbuhkan cinta buku
                sejak dini.
              </p>
            </article>
            <article class="program-card program-card--rot-2">
              <span
                class="program-card__icon"
                style="background: var(--color-mint-soft)"
                >🔬</span
              >
              <h3>Science Fun Lab</h3>
              <p>
                Eksperimen sains sederhana yang aman dan seru untuk
                dipraktikkan.
              </p>
            </article>
            <article class="program-card program-card--rot-1">
              <span
                class="program-card__icon"
                style="background: var(--color-blue-soft)"
                >🌍</span
              >
              <h3>English Day</h3>
              <p>
                Sehari penuh berbahasa Inggris lewat lagu, cerita, dan
                permainan.
              </p>
            </article>
            <article class="program-card program-card--rot-2">
              <span
                class="program-card__icon"
                style="background: var(--color-yellow-soft)"
                >🕌</span
              >
              <h3>Tahfidz &amp; Karakter</h3>
              <p>Hafalan surat pendek dibarengi pembiasaan akhlak yang baik.</p>
            </article>
            <article class="program-card program-card--rot-1">
              <span
                class="program-card__icon"
                style="background: var(--color-purple-soft)"
                >💻</span
              >
              <h3>Coding Kids Dasar</h3>
              <p>
                Belajar logika pemrograman dengan cara visual dan menyenangkan.
              </p>
            </article>
            <article class="program-card program-card--rot-2">
              <span
                class="program-card__icon"
                style="background: var(--color-orange-soft)"
                >🌳</span
              >
              <h3>Outdoor Learning</h3>
              <p>Eksplorasi alam terbuka untuk melatih rasa ingin tahu anak.</p>
            </article>
          </div>
        </div>
      </section>

      <!-- ======================= EKSTRAKURIKULER ======================= -->
      <section class="ekskul-section section">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--mint">Kembangkan Bakat</p>
            <h2 class="section-title">Ekstrakurikuler</h2>
            <p class="section-subtitle">
              Pilih kegiatan favorit anak di luar jam pelajaran utama.
            </p>
          </div>

          <div
            class="filter-bar"
            role="group"
            aria-label="Filter ekstrakurikuler"
          >
            <button class="filter-btn active" data-filter="semua">Semua</button>
            <button class="filter-btn" data-filter="seni">Seni</button>
            <button class="filter-btn" data-filter="olahraga">Olahraga</button>
            <button class="filter-btn" data-filter="sains">Sains</button>
          </div>

          <div class="ekskul-grid" id="ekskulGrid">
            <div class="ekskul-card" data-category="seni">
              <span class="ekskul-card__icon">🖍️</span>
              <h3>Menggambar</h3>
              <span class="tag tag--seni">Seni</span>
            </div>
            <div class="ekskul-card" data-category="seni">
              <span class="ekskul-card__icon">💃</span>
              <h3>Menari</h3>
              <span class="tag tag--seni">Seni</span>
            </div>
            <div class="ekskul-card" data-category="olahraga">
              <span class="ekskul-card__icon">⚽</span>
              <h3>Futsal Mini</h3>
              <span class="tag tag--olahraga">Olahraga</span>
            </div>
            <div class="ekskul-card" data-category="olahraga">
              <span class="ekskul-card__icon">🏕️</span>
              <h3>Pramuka Siaga</h3>
              <span class="tag tag--olahraga">Olahraga</span>
            </div>
            <div class="ekskul-card" data-category="seni">
              <span class="ekskul-card__icon">🎵</span>
              <h3>Musik</h3>
              <span class="tag tag--seni">Seni</span>
            </div>
            <div class="ekskul-card" data-category="sains">
              <span class="ekskul-card__icon">🤖</span>
              <h3>Robotik Dasar</h3>
              <span class="tag tag--sains">Sains</span>
            </div>
          </div>
          <p class="ekskul-empty" id="ekskulEmpty" hidden>
            Belum ada kegiatan di kategori ini.
          </p>
        </div>
        <div class="wave-divider" aria-hidden="true">
          <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <use href="#wave-shape" class="wave-fill-blue"></use>
          </svg>
        </div>
      </section>

      <!-- ======================= GALERI ======================= -->
      <section class="galeri-section section" id="galeri">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--white">Momen Sekolah</p>
            <h2 class="section-title section-title--white">Galeri Kegiatan</h2>
            <p class="section-subtitle section-subtitle--white">
              Sekilas cerita keseruan anak-anak di Sekolah Ceria Nusantara.
            </p>
          </div>

          <div class="galeri-grid" id="galeriGrid">
            <button
              class="galeri-item galeri-item--tall"
              style="--g1: var(--color-yellow); --g2: var(--color-orange)"
              data-caption="Semangat pagi anak-anak TK sebelum masuk kelas."
            >
              <span class="galeri-item__emoji">🌞🧒</span>
              <span class="galeri-item__caption"
                >Semangat Pagi di Kelas TK</span
              >
            </button>
            <button
              class="galeri-item"
              style="--g1: var(--color-mint); --g2: var(--color-blue)"
              data-caption="Anak-anak antusias mencoba eksperimen sains sederhana."
            >
              <span class="galeri-item__emoji">🧪🔬</span>
              <span class="galeri-item__caption">Eksperimen Sains Seru</span>
            </button>
            <button
              class="galeri-item"
              style="--g1: var(--color-blue); --g2: var(--color-purple)"
              data-caption="Waktu bermain bersama di taman sekolah yang aman."
            >
              <span class="galeri-item__emoji">🛝🎈</span>
              <span class="galeri-item__caption">Waktu Bermain di Taman</span>
            </button>
            <button
              class="galeri-item galeri-item--tall"
              style="--g1: var(--color-pink); --g2: var(--color-purple)"
              data-caption="Keceriaan anak-anak dalam lomba mewarnai tahunan."
            >
              <span class="galeri-item__emoji">🖍️🎨</span>
              <span class="galeri-item__caption">Lomba Mewarnai Ceria</span>
            </button>
            <button
              class="galeri-item"
              style="--g1: var(--color-orange); --g2: var(--color-yellow)"
              data-caption="Latihan rutin Pramuka Siaga di halaman sekolah."
            >
              <span class="galeri-item__emoji">🏕️🧭</span>
              <span class="galeri-item__caption">Latihan Pramuka Siaga</span>
            </button>
            <button
              class="galeri-item"
              style="--g1: var(--color-purple); --g2: var(--color-pink)"
              data-caption="Penampilan siswa dalam pentas seni tahunan sekolah."
            >
              <span class="galeri-item__emoji">🎭🎶</span>
              <span class="galeri-item__caption">Pentas Seni Tahunan</span>
            </button>
          </div>
        </div>
      </section>

      <!-- ======================= ARTIKEL ======================= -->
      <section class="artikel-section section" id="artikel">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--yellow">Tips &amp; Wawasan</p>
            <h2 class="section-title">Artikel untuk Orang Tua</h2>
          </div>
          <div class="artikel-grid">
            <article class="artikel-card reveal">
              <div
                class="artikel-card__thumb"
                style="
                  background: linear-gradient(
                    135deg,
                    var(--color-yellow-soft),
                    var(--color-orange-soft)
                  );
                "
              >
                📰
              </div>
              <div class="artikel-card__body">
                <span class="artikel-card__date">3 Juni 2026</span>
                <h3>Tips Membantu Anak Berani Masuk Sekolah</h3>
                <p>
                  Panduan sederhana bagi orang tua untuk membantu anak lebih
                  percaya diri di hari pertama sekolah.
                </p>
                <a href="#artikel" class="link-arrow">Baca Artikel →</a>
              </div>
            </article>
            <article class="artikel-card reveal reveal--delay-1">
              <div
                class="artikel-card__thumb"
                style="
                  background: linear-gradient(
                    135deg,
                    var(--color-mint-soft),
                    var(--color-blue-soft)
                  );
                "
              >
                🧩
              </div>
              <div class="artikel-card__body">
                <span class="artikel-card__date">18 Mei 2026</span>
                <h3>Belajar Sambil Bermain: Kenapa Penting?</h3>
                <p>
                  Mengenal manfaat metode belajar sambil bermain untuk tumbuh
                  kembang anak usia dini.
                </p>
                <a href="#artikel" class="link-arrow">Baca Artikel →</a>
              </div>
            </article>
            <article class="artikel-card reveal reveal--delay-2">
              <div
                class="artikel-card__thumb"
                style="
                  background: linear-gradient(
                    135deg,
                    var(--color-pink-soft),
                    var(--color-purple-soft)
                  );
                "
              >
                🏫
              </div>
              <div class="artikel-card__body">
                <span class="artikel-card__date">2 Mei 2026</span>
                <h3>Cara Memilih Sekolah TK dan SD yang Tepat</h3>
                <p>
                  Beberapa hal penting yang perlu dipertimbangkan orang tua
                  sebelum memilih sekolah untuk anak.
                </p>
                <a href="#artikel" class="link-arrow">Baca Artikel →</a>
              </div>
            </article>
          </div>
        </div>
      </section>

      <!-- ======================= PENGUMUMAN ======================= -->
      <section class="pengumuman-section section">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--blue">Jangan Sampai Terlewat</p>
            <h2 class="section-title">Pengumuman Sekolah</h2>
          </div>
          <div class="pengumuman-grid">
            <div class="sticky-note sticky-note--yellow reveal">
              <span class="sticky-note__pin">📌</span>
              <span class="sticky-note__date">10 Juli 2026</span>
              <h3>Jadwal Open House</h3>
              <p>Kunjungi kelas dan kenali guru-guru sebelum mendaftar.</p>
            </div>
            <div class="sticky-note sticky-note--pink reveal reveal--delay-1">
              <span class="sticky-note__pin">📌</span>
              <span class="sticky-note__date">1 Juni – 31 Juli 2026</span>
              <h3>Pendaftaran PPDB Gelombang 1</h3>
              <p>Segera daftarkan si kecil, kuota gelombang 1 terbatas.</p>
            </div>
            <div class="sticky-note sticky-note--mint reveal reveal--delay-2">
              <span class="sticky-note__pin">📌</span>
              <span class="sticky-note__date">17 Agustus 2026</span>
              <h3>Libur Nasional</h3>
              <p>
                Sekolah libur dalam rangka Hari Kemerdekaan Republik Indonesia.
              </p>
            </div>
            <div class="sticky-note sticky-note--purple reveal reveal--delay-3">
              <span class="sticky-note__pin">📌</span>
              <span class="sticky-note__date">20 Desember 2026</span>
              <h3>Kegiatan Pentas Seni</h3>
              <p>Penampilan bakat seni siswa TK dan SD di akhir semester.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- ======================= FASILITAS ======================= -->
      <section class="fasilitas-section section">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--mint">Fasilitas Sekolah</p>
            <h2 class="section-title">Nyaman dan Aman untuk Anak</h2>
          </div>
          <div class="fasilitas-grid">
            <div class="fasilitas-item reveal">
              <span class="fasilitas-item__icon">🏫</span>
              <p>Ruang Kelas Nyaman</p>
            </div>
            <div class="fasilitas-item reveal">
              <span class="fasilitas-item__icon">📚</span>
              <p>Perpustakaan Mini</p>
            </div>
            <div class="fasilitas-item reveal">
              <span class="fasilitas-item__icon">🛝</span>
              <p>Area Bermain Aman</p>
            </div>
            <div class="fasilitas-item reveal">
              <span class="fasilitas-item__icon">🩺</span>
              <p>UKS</p>
            </div>
            <div class="fasilitas-item reveal">
              <span class="fasilitas-item__icon">📷</span>
              <p>CCTV Area Sekolah</p>
            </div>
            <div class="fasilitas-item reveal">
              <span class="fasilitas-item__icon">🍎</span>
              <p>Kantin Sehat</p>
            </div>
          </div>
        </div>
        <div class="wave-divider" aria-hidden="true">
          <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <use href="#wave-shape" class="wave-fill-purple"></use>
          </svg>
        </div>
      </section>

      <!-- ======================= KONTAK ======================= -->
      <section class="kontak-section section" id="kontak">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--purple">Hubungi Kami</p>
            <h2 class="section-title">Kontak &amp; Lokasi</h2>
          </div>
          <div class="kontak-grid">
            <div class="kontak-info reveal">
              <div class="kontak-info__item">
                <span class="kontak-info__icon">📍</span>
                <div>
                  <h4>Alamat</h4>
                  <p>
                    Jl. Mawar Ceria No. 12, Kel. Sukamaju, Kota Bahagia, 12345
                  </p>
                </div>
              </div>
              <div class="kontak-info__item">
                <span class="kontak-info__icon">💬</span>
                <div>
                  <h4>WhatsApp</h4>
                  <p>0812-3456-7890</p>
                </div>
              </div>
              <div class="kontak-info__item">
                <span class="kontak-info__icon">✉️</span>
                <div>
                  <h4>Email</h4>
                  <p>info@sekolahcerianusantara.sch.id</p>
                </div>
              </div>
              <div class="kontak-info__item">
                <span class="kontak-info__icon">🕘</span>
                <div>
                  <h4>Jam Operasional</h4>
                  <p>Senin – Jumat, 07.00 – 15.00 WIB</p>
                </div>
              </div>
              <a href="#" class="btn btn--primary">Hubungi Admin</a>
            </div>

            <!-- Placeholder peta custom dengan CSS, tanpa embed eksternal -->
            <div
              class="map-placeholder reveal reveal--delay-1"
              role="img"
              aria-label="Peta lokasi Sekolah Ceria Nusantara"
            >
              <div class="map-placeholder__grid"></div>
              <div class="map-placeholder__road map-placeholder__road--h"></div>
              <div class="map-placeholder__road map-placeholder__road--v"></div>
              <div class="map-placeholder__pin">
                📍<span>Sekolah Ceria Nusantara</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- ======================= FOOTER ======================= -->
    <footer class="site-footer">
      <div class="container site-footer__grid">
        <div class="footer-brand">
          <a href="#beranda" class="navbar__logo navbar__logo--footer">
            <span class="navbar__logo-icon">🌈</span>
            <span class="navbar__logo-text"
              >Sekolah Ceria<br /><small>Nusantara</small></span
            >
          </a>
          <p>
            Tempat anak tumbuh ceria, belajar bermakna, dan bersiap meraih masa
            depan.
          </p>
          <div class="footer-social">
            <a href="#" aria-label="Instagram">📸</a>
            <a href="#" aria-label="Facebook">📘</a>
            <a href="#" aria-label="YouTube">📺</a>
            <a href="#" aria-label="TikTok">🎵</a>
          </div>
        </div>

        <div class="footer-links">
          <h4>Tautan Cepat</h4>
          <ul>
            <li><a href="#beranda">Beranda</a></li>
            <li><a href="#program">Program</a></li>
            <li><a href="#galeri">Galeri</a></li>
            <li><a href="#artikel">Artikel</a></li>
            <li><a href="#ppdb">PPDB</a></li>
            <li><a href="#kontak">Kontak</a></li>
          </ul>
        </div>

        <div class="footer-contact">
          <h4>Kontak</h4>
          <p>📍 Jl. Mawar Ceria No. 12, Kota Bahagia</p>
          <p>💬 0812-3456-7890</p>
          <p>✉️ info@sekolahcerianusantara.sch.id</p>
        </div>
      </div>
      <div class="site-footer__bottom">
        <p>
          &copy; <span id="currentYear"></span> Sekolah Ceria Nusantara. Semua
          hak dilindungi.
        </p>
      </div>
    </footer>

    <!-- Tombol scroll to top -->
    <button
      class="scroll-top-btn"
      id="scrollTopBtn"
      aria-label="Kembali ke atas"
      hidden
    >
      ⬆️
    </button>

    <!-- Lightbox galeri -->
    <div class="lightbox" id="lightbox" hidden>
      <div class="lightbox__backdrop" id="lightboxBackdrop"></div>
      <div
        class="lightbox__content"
        role="dialog"
        aria-modal="true"
        aria-label="Pratinjau galeri"
      >
        <button class="lightbox__close" id="lightboxClose" aria-label="Tutup">
          ✕
        </button>
        <div class="lightbox__visual" id="lightboxVisual"></div>
        <p class="lightbox__caption" id="lightboxCaption"></p>
      </div>
    </div>

  </body>
</html>
