<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Statistik Homepage</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    :root {
      color-scheme: light;
      --admin-ink: #1f2433;
      --admin-muted: #6b7280;
      --admin-bg: #f6f3ec;
      --admin-panel: #ffffff;
      --admin-line: #e8dfd2;
      --admin-orange: #d76b22;
      --admin-green: #166534;
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: var(--admin-bg);
      color: var(--admin-ink);
    }

    .admin-shell {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 260px 1fr;
    }

    .admin-sidebar {
      padding: 24px;
      background: #211f3b;
      color: #fff;
    }

    .admin-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 32px;
      font-weight: 800;
      line-height: 1.1;
    }

    .admin-brand__mark {
      width: 42px;
      height: 42px;
      display: grid;
      place-items: center;
      border-radius: 14px;
      background: #fff7dd;
      color: #211f3b;
      font-weight: 900;
    }

    .admin-nav {
      display: grid;
      gap: 10px;
    }

    .admin-nav a,
    .admin-logout {
      display: block;
      width: 100%;
      padding: 12px 14px;
      border-radius: 14px;
      color: rgba(255,255,255,0.82);
      text-decoration: none;
      border: 0;
      background: transparent;
      text-align: left;
      font: inherit;
      cursor: pointer;
    }

    .admin-nav a.active {
      color: #211f3b;
      background: #fff7dd;
      font-weight: 800;
    }

    .admin-logout:hover,
    .admin-nav a:hover {
      background: rgba(255,255,255,0.12);
      color: #fff;
    }

    .admin-main {
      padding: 32px;
    }

    .admin-header {
      display: flex;
      justify-content: space-between;
      gap: 16px;
      align-items: flex-start;
      margin-bottom: 24px;
    }

    .admin-header h1 {
      margin: 0 0 8px;
      font-size: clamp(1.7rem, 3vw, 2.4rem);
      letter-spacing: -0.04em;
    }

    .admin-header p {
      margin: 0;
      color: var(--admin-muted);
      max-width: 660px;
    }

    .admin-link-home {
      white-space: nowrap;
      padding: 11px 14px;
      border-radius: 999px;
      background: #fff;
      color: var(--admin-ink);
      text-decoration: none;
      font-weight: 800;
      box-shadow: 0 12px 30px rgba(31,36,51,0.08);
    }

    .flash-message {
      margin: 0 0 18px;
      padding: 14px 16px;
      border-radius: 16px;
      background: #dcfce7;
      color: var(--admin-green);
      font-weight: 800;
    }

    .stats-admin-form {
      display: grid;
      gap: 18px;
    }

    .stats-admin-grid {
      display: grid;
      gap: 16px;
    }

    .stats-admin-card {
      background: var(--admin-panel);
      border: 1px solid var(--admin-line);
      border-radius: 24px;
      padding: 20px;
      box-shadow: 0 18px 45px rgba(31,36,51,0.07);
    }

    .stats-admin-card__head {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 16px;
      color: var(--admin-muted);
      font-size: 0.86rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    .stats-admin-fields {
      display: grid;
      grid-template-columns: 220px 1fr;
      gap: 16px;
    }

    .field {
      display: grid;
      gap: 8px;
    }

    .field label {
      font-weight: 800;
      font-size: 0.9rem;
    }

    .field input {
      width: 100%;
      box-sizing: border-box;
      border: 1px solid var(--admin-line);
      border-radius: 14px;
      padding: 12px 14px;
      font: inherit;
      color: var(--admin-ink);
      background: #fffdf8;
      outline: none;
    }

    .field input:focus {
      border-color: var(--admin-orange);
      box-shadow: 0 0 0 4px rgba(215,107,34,0.12);
    }

    .field-error {
      color: #b91c1c;
      font-size: 0.84rem;
      font-weight: 700;
    }

    .admin-actions {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      position: sticky;
      bottom: 18px;
      padding: 12px;
      background: rgba(246,243,236,0.82);
      backdrop-filter: blur(12px);
      border-radius: 20px;
    }

    .admin-save-btn {
      border: 0;
      border-radius: 999px;
      padding: 13px 20px;
      background: linear-gradient(135deg, #f59e0b, #ea580c);
      color: #fff;
      font-weight: 900;
      cursor: pointer;
      box-shadow: 0 16px 34px rgba(234,88,12,0.24);
    }

    @media (max-width: 860px) {
      .admin-shell {
        grid-template-columns: 1fr;
      }

      .admin-sidebar {
        position: static;
        padding: 18px;
      }

      .admin-nav {
        grid-template-columns: 1fr 1fr;
      }

      .admin-main {
        padding: 20px;
      }

      .admin-header {
        display: grid;
      }

      .stats-admin-fields {
        grid-template-columns: 1fr;
      }

      .admin-actions {
        position: static;
      }
    }
  </style>
</head>
<body>
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <div class="admin-brand">
        <span class="admin-brand__mark">AM</span>
        <span>Al Mustaqbal<br><small>Admin Panel</small></span>
      </div>

      <nav class="admin-nav" aria-label="Menu admin">
        <a href="{{ route('admin.stats.edit') }}" class="active">Statistik Homepage</a>
        <a href="{{ route('home') }}">Lihat Homepage</a>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="admin-logout">Logout</button>
        </form>
      </nav>
    </aside>

    <main class="admin-main">
      <header class="admin-header">
        <div>
          <h1>Edit Statistik Homepage</h1>
          <p>Ubah teks besar dan label di bagian statistik. Kolom value boleh angka, angka plus, atau teks biasa.</p>
        </div>

        <a href="{{ route('home') }}" class="admin-link-home">Lihat Website</a>
      </header>

      @if (session('success'))
        <p class="flash-message">{{ session('success') }}</p>
      @endif

      <form method="POST" action="{{ route('admin.stats.update') }}" class="stats-admin-form">
        @csrf
        @method('PUT')

        <div class="stats-admin-grid">
          @foreach ($statistics as $index => $statistic)
            <section class="stats-admin-card">
              <div class="stats-admin-card__head">
                <span>Item {{ $index + 1 }}</span>
                <span>#{{ $statistic->id }}</span>
              </div>

              <input type="hidden" name="statistics[{{ $index }}][id]" value="{{ $statistic->id }}">

              <div class="stats-admin-fields">
                <div class="field">
                  <label for="stat-value-{{ $statistic->id }}">Value</label>
                  <input
                    id="stat-value-{{ $statistic->id }}"
                    name="statistics[{{ $index }}][value]"
                    value="{{ old('statistics.' . $index . '.value', $statistic->value) }}"
                    maxlength="80"
                    required
                  >
                  @error('statistics.' . $index . '.value')
                    <span class="field-error">{{ $message }}</span>
                  @enderror
                </div>

                <div class="field">
                  <label for="stat-label-{{ $statistic->id }}">Label</label>
                  <input
                    id="stat-label-{{ $statistic->id }}"
                    name="statistics[{{ $index }}][label]"
                    value="{{ old('statistics.' . $index . '.label', $statistic->label) }}"
                    maxlength="120"
                    required
                  >
                  @error('statistics.' . $index . '.label')
                    <span class="field-error">{{ $message }}</span>
                  @enderror
                </div>
              </div>
            </section>
          @endforeach
        </div>

        <div class="admin-actions">
          <button type="submit" class="admin-save-btn">Simpan Statistik</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
