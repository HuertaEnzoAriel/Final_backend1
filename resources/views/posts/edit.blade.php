<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Post | Cuaderno Naranja</title>
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
                <a class="btn" href="{{ route('posts.show', $post) }}">Volver al post</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="card">
            <p class="muted">Edicion de publicacion</p>
            <h1>Editar post</h1>

            <form method="POST" action="{{ route('posts.update', $post) }}" class="form-grid" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="field">
                    <label for="title">Titulo</label>
                    <input id="title" name="title" value="{{ old('title', $post->title) }}" maxlength="180" required>
                    @error('title') <small class="error">{{ $message }}</small> @enderror
                </div>

                <div class="field">
                    <label for="content">Contenido</label>
                    <textarea id="content" name="content" required>{{ old('content', $post->content) }}</textarea>
                    @error('content') <small class="error">{{ $message }}</small> @enderror
                </div>

                <div class="field">
                    <label for="image">Reemplazar imagen (opcional)</label>
                    <input id="image" name="image" type="file" accept="image/*">
                    <small class="muted">Formatos: JPG, PNG o WEBP. Maximo 2MB.</small>
                    @error('image') <small class="error">{{ $message }}</small> @enderror

                    @if ($post->image_path)
                        <label style="display:flex; align-items:center; gap:.45rem; margin-top:.5rem;">
                            <input type="checkbox" name="remove_image" value="1" style="width:auto;">
                            Quitar imagen actual
                        </label>
                        <small class="muted">Actual: {{ $post->image_path }}</small>
                    @endif
                </div>

                <div class="field">
                    <label for="is_published">Estado</label>
                    <select id="is_published" name="is_published">
                        <option value="1" @selected(old('is_published', $post->is_published ? '1' : '0') === '1')>Publicado</option>
                        <option value="0" @selected(old('is_published', $post->is_published ? '1' : '0') === '0')>Borrador</option>
                    </select>
                </div>

                <button type="submit" class="action">Guardar cambios</button>
            </form>
        </section>
    </main>
</body>
</html>
