<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin</title>
</head>
<body>
    <main>
        <h1>Login Admin</h1>

        <p>
            <a href="{{ route('google.redirect') }}">Masuk dengan Google</a>
        </p>

        <p>atau masuk dengan email dan password</p>

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <div>
                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >

                @error('email')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                >

                @error('password')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <label>
                <input type="checkbox" name="remember" value="1">
                Ingat saya
            </label>

            <button type="submit">Masuk</button>
        </form>
    </main>
</body>
</html>
