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
                <button
                    class="btn"
                    type="button"
                    data-reject-action="{{ route('admin.posts.reject', $post) }}"
                >Rechazar</button>
            </div>
        </section>
    </main>

    <div id="reject-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:100; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:1rem; padding:1.5rem; width:100%; max-width:480px; margin:1rem; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
            <p class="muted" style="margin:0 0 .25rem;">Rechazar publicacion</p>
            <h2 style="margin:0 0 1rem; font-family:'Fraunces', Georgia, serif; font-size:1.3rem;">{{ $post->title }}</h2>
            <form method="POST" id="reject-form" action="{{ route('admin.posts.reject', $post) }}">
                @csrf
                @method('PATCH')
                <div class="field" style="margin-bottom:1rem;">
                    <label for="rejection_reason">Motivo del rechazo <span style="color:#c0392b;">*</span></label>
                    <textarea
                        id="rejection_reason"
                        name="rejection_reason"
                        rows="4"
                        maxlength="500"
                        required
                        placeholder="Explicá al autor por qué se rechaza su publicacion..."
                        style="margin-top:.35rem;"
                    ></textarea>
                    <small class="muted" style="display:block; margin-top:.3rem;">Maximo 500 caracteres. El autor podrá ver este motivo.</small>
                </div>
                <div style="display:flex; gap:.6rem; justify-content:flex-end;">
                    <button type="button" class="btn" id="reject-cancel">Cancelar</button>
                    <button type="submit" class="btn" style="background:#b33232; border-color:#b33232; color:#fff;">Confirmar rechazo</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function () {
        var modal = document.getElementById('reject-modal');

        document.querySelector('[data-reject-action]').addEventListener('click', function () {
            modal.style.display = 'flex';
        });

        document.getElementById('reject-cancel').addEventListener('click', function () {
            modal.style.display = 'none';
        });

        modal.addEventListener('click', function (e) {
            if (e.target === modal) modal.style.display = 'none';
        });
    })();
    </script>
</body>
</html>