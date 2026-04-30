<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuevo Post | Cuaderno Naranja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/panel.css'])
</head>
<body>
    <header>
        <div class="container head-inner">
            <a class="brand" href="{{ route('blog.index') }}">Cuaderno <span>Naranja</span></a>
            <div style="display:flex; gap:.5rem;">
                <a class="btn" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="btn" href="{{ route('blog.index') }}">Volver al blog</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="card">
            <p class="muted">Publicando como {{ $user->name }}</p>
            <h1>Nueva publicacion</h1>
            <p class="muted">Pantalla dedicada para redactar y publicar con tu usuario.</p>

            <form method="POST" action="{{ route('posts.store') }}" class="form-grid" enctype="multipart/form-data">
                @csrf
                <div class="field">
                    <label for="title">Titulo</label>
                    <input id="title" name="title" value="{{ old('title') }}" maxlength="180" required>
                    @error('title') <small class="error">{{ $message }}</small> @enderror
                </div>

                <div class="field">
                    <label for="content">Contenido</label>
                    <textarea id="content" name="content" required>{{ old('content') }}</textarea>
                    @error('content') <small class="error">{{ $message }}</small> @enderror
                </div>

                <div class="field">
                    <label for="image">Imagen (opcional)</label>
                    <input id="image" name="image" type="file" accept="image/*">
                    <small class="muted">Formatos: JPG, PNG o WEBP. Maximo 2MB.</small>
                    @error('image') <small class="error">{{ $message }}</small> @enderror
                </div>

                <button type="submit" class="action">Publicar</button>
            </form>
        </section>
    </main>
</body>
</html>
