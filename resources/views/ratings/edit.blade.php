<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Rating | Cuaderno Naranja</title>
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
                    <p class="muted">Post: {{ $rating->post->title }}</p>
                    <h1>Editar rating</h1>
                </div>
                <a class="btn" href="{{ route('posts.show', $rating->post_id) }}">Volver</a>
            </div>

            <form method="POST" action="{{ route('ratings.update', $rating) }}">
                @csrf
                @method('PATCH')
                <div class="field">
                    <label for="score">Puntaje</label>
                    <select id="score" name="score" required>
                        @foreach (range(1, 5) as $score)
                            <option value="{{ $score }}" @selected((string) old('score', $rating->score) === (string) $score)>{{ $score }}</option>
                        @endforeach
                    </select>
                </div>
                @error('score') <small class="error">{{ $message }}</small> @enderror
                <button type="submit" class="action">Guardar rating</button>
            </form>
        </section>
    </main>
</body>
</html>
