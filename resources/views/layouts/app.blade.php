<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SchoolAI' }}</title>

    <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f8f3e8;
            color: #1f2937;
        }

        .page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: min(100%, 420px);
            background: #fffaf0;
            border: 1px solid #f0dfbd;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(31, 41, 55, 0.12);
        }

        .title {
            margin: 0 0 8px;
            font-size: 28px;
            line-height: 1.1;
        }

        .muted {
            color: #6b7280;
            margin: 0 0 24px;
        }

        .field {
            display: grid;
            gap: 8px;
            margin-bottom: 16px;
        }

        label {
            font-weight: 700;
            font-size: 14px;
        }

        input {
            width: 100%;
            border: 1px solid #e5d3ad;
            border-radius: 14px;
            padding: 13px 14px;
            font: inherit;
            background: white;
        }

        input:focus {
            outline: 3px solid rgba(251, 191, 36, 0.35);
            border-color: #f59e0b;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            font-size: 14px;
            color: #4b5563;
        }

        .checkbox input {
            width: auto;
        }

        .button {
            width: 100%;
            border: 0;
            border-radius: 16px;
            padding: 13px 16px;
            background: #2563eb;
            color: white;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
        }

        .button:hover {
            background: #1d4ed8;
        }

        .error {
            margin: 8px 0 0;
            color: #b91c1c;
            font-size: 14px;
        }

        .topbar {
            width: min(100%, 900px);
            margin: 0 auto 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel {
            width: min(100%, 900px);
            background: white;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(31, 41, 55, 0.10);
        }

        .logout {
            border: 0;
            border-radius: 12px;
            padding: 10px 14px;
            background: #111827;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
