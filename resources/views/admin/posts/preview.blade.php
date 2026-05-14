<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vista previa | Cuaderno Naranja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/panel.css'])
</head>
<body>
    <header class="header">
        <div class="container head-inner">
            <a class="brand" href="{{ route('blog.index') }}">Cuaderno <span>Naranja</span></a>
            <div class="actions">
                <a class="btn" href="{{ route('admin.posts.pending') }}">Volver a moderacion</a>
                <a class="btn" href="{{ route('dashboard') }}">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Salir</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <p class="muted">Vista previa para moderacion</p>
            <h1>{{ $post->title }}</h1>
            <p class="muted">Autor: {{ $post->user?->name ?? 'Sin autor' }} · Creado el {{ optional($post->created_at)->format('d/m/Y H:i') ?? '-' }}</p>
            <div class="metrics">
                <div class="metric"><strong>{{ $post->publicationStatusLabel() }}</strong><span>Estado</span></div>
                <div class="metric"><strong>{{ $post->comments_count ?? 0 }}</strong><span>Comentarios</span></div>
                <div class="metric"><strong>{{ $post->ratings_count ?? 0 }}</strong><span>Ratings</span></div>
            </div>
        </section>

        <section class="card" style="display:grid; gap:1rem;">
            @if ($post->image_path)
                <img src="{{ asset('storage/' . $post->image_path) }}" alt="Imagen de {{ $post->title }}" style="width:100%; max-height:420px; object-fit:cover; border-radius:18px;">
            @endif

            <div>
                <p class="muted" style="margin-bottom:.5rem;">Contenido completo</p>
                <div style="white-space:pre-wrap; line-height:1.75;">{{ $post->content }}</div>
            </div>

            <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                <form method="POST" action="{{ route('admin.posts.approve', $post) }}" onsubmit="return confirm('¿Aprobar y publicar este post?');">
                    @csrf
                    @method('PATCH')
                    <button class="btn primary" type="submit">Aprobar y publicar</button>
                </form>
                <form method="POST" action="{{ route('admin.posts.reject', $post) }}" onsubmit="return confirm('¿Rechazar este post?');">
                    @csrf
                    @method('PATCH')
                    <button class="btn" type="submit">Rechazar</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>