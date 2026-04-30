<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Comentario | Cuaderno Naranja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/panel.css'])
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
