<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrar | Cuaderno Naranja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin:0; min-height:100vh; font-family:'Space Grotesk',system-ui,sans-serif; background: linear-gradient(180deg,#f7efe4 0%,#f6f0e7 100%); color:#1f2528; display:grid; place-items:center; padding:1.5rem; }
        .card { width:min(460px,100%); background:#fffdf9; border:1px solid #d6cfc2; border-radius:1.2rem; box-shadow:0 14px 28px rgba(31,37,40,.12); padding:1.5rem; }
        h1 { font-family:'Fraunces',Georgia,serif; margin-bottom:.4rem; }
        p { color:#5f666c; margin-bottom:1rem; }
        .field { display:grid; gap:.35rem; margin-bottom:.85rem; }
        input { width:100%; border:1px solid #d6cfc2; border-radius:.7rem; padding:.75rem .8rem; font:inherit; background:#fff; }
        label { font-size:.9rem; color:#5f666c; }
        button { width:100%; border:0; border-radius:.8rem; padding:.8rem 1rem; background:#e16a2d; color:#fff; font-weight:700; cursor:pointer; }
        .row { display:flex; justify-content:space-between; gap:.75rem; align-items:center; margin-top:1rem; flex-wrap:wrap; }
        a { color:#1f7a8c; text-decoration:none; }
        .error { color:#b42318; font-size:.86rem; }
    </style>
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
