<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar | Cuaderno Naranja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/auth.css'])
</head>
<body>
    <div class="card">
        <h1>Entrar</h1>
        <p>Usa tu usuario para publicar con tu propia cuenta.</p>

        @if ($errors->any())
            <div class="error" style="margin-bottom:1rem;">
                Revisa tus datos e intenta de nuevo.
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field" style="display:flex;align-items:center;gap:.5rem;">
                <input id="remember" name="remember" type="checkbox" value="1" style="width:auto;">
                <label for="remember" style="margin:0;">Recordarme</label>
            </div>
            <button type="submit">Entrar</button>
        </form>

        <div class="row">
            <a href="{{ route('register') }}">Crear cuenta</a>
            <a href="{{ route('blog.index') }}">Volver al blog</a>
        </div>
    </div>
</body>
</html>
