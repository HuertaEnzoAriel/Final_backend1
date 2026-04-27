<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Comentario | Cuaderno Naranja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin:0; min-height:100vh; font-family:'Space Grotesk',system-ui,sans-serif; color:#1f2528; background:linear-gradient(180deg,#f7efe4 0%,#f6f0e7 100%); }
        .container { width:min(860px, calc(100% - 2rem)); margin:0 auto; }
        main { padding:1.8rem 0; }
        .card { background:#fffdf9; border:1px solid #d6cfc2; border-radius:1rem; box-shadow:0 14px 28px rgba(31,37,40,.12); padding:1.25rem; }
        h1 { margin:.2rem 0 .5rem; font-family:'Fraunces',Georgia,serif; font-size:2rem; }
        .muted { color:#5f666c; }
        textarea { width:100%; min-height:170px; resize:vertical; border:1px solid #d6cfc2; border-radius:.7rem; padding:.75rem .8rem; font:inherit; margin-top:.9rem; }
        .action { margin-top:.8rem; border:0; background:#e16a2d; color:#fff; border-radius:.8rem; padding:.8rem 1rem; font-weight:700; cursor:pointer; }
        .top { display:flex; justify-content:space-between; align-items:center; gap:.8rem; flex-wrap:wrap; }
        .btn { border:1px solid #d6cfc2; background:#fff; color:#1f2528; padding:.45rem .7rem; border-radius:.7rem; text-decoration:none; font-weight:600; }
        .error { color:#b42318; font-size:.85rem; }
    </style>
</head>
<body>
    <main class="container">
        <section class="card">
            <div class="top">
                <div>
                    <p class="muted">Post: {{ $comment->post->title }}</p>
                    <h1>Editar comentario</h1>
                </div>
                <a class="btn" href="{{ route('posts.show', $comment->post_id) }}">Volver</a>
            </div>

            <form method="POST" action="{{ route('comments.update', $comment) }}">
                @csrf
                @method('PATCH')
                <textarea name="content" required>{{ old('content', $comment->content) }}</textarea>
                @error('content') <small class="error">{{ $message }}</small> @enderror
                <button type="submit" class="action">Guardar comentario</button>
            </form>
        </section>
    </main>
</body>
</html>
