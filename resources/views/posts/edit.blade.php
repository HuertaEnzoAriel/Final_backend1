<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Post | Cuaderno Naranja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin:0; min-height:100vh; font-family:'Space Grotesk',system-ui,sans-serif; color:#1f2528; background:linear-gradient(180deg,#f7efe4 0%,#f6f0e7 100%); }
        .container { width:min(860px, calc(100% - 2rem)); margin:0 auto; }
        header { padding:1rem 0; border-bottom:1px solid rgba(214,207,194,.8); background:rgba(246,240,231,.9); position:sticky; top:0; backdrop-filter: blur(10px); }
        .head-inner { display:flex; justify-content:space-between; align-items:center; gap:.7rem; }
        .brand { font-family:'Fraunces',Georgia,serif; font-size:1.3rem; font-weight:700; text-decoration:none; color:#1f2528; }
        .brand span { color:#e16a2d; }
        .btn { border:1px solid #d6cfc2; background:#fffdf9; color:#1f2528; padding:.55rem .85rem; border-radius:.7rem; text-decoration:none; font-weight:600; }
        main { padding:1.6rem 0 2rem; }
        .card { background:#fffdf9; border:1px solid #d6cfc2; border-radius:1rem; box-shadow:0 14px 28px rgba(31,37,40,.12); padding:1.25rem; }
        h1 { margin:.2rem 0 .5rem; font-family:'Fraunces',Georgia,serif; font-size:2rem; }
        .muted { color:#5f666c; }
        .form-grid { display:grid; gap:.9rem; margin-top:1rem; }
        .field { display:grid; gap:.35rem; }
        label { font-size:.88rem; color:#5f666c; }
        input, textarea, select { width:100%; border:1px solid #d6cfc2; border-radius:.7rem; padding:.75rem .8rem; font:inherit; background:#fff; }
        textarea { min-height:220px; resize:vertical; }
        .action { border:0; background:#e16a2d; color:#fff; border-radius:.8rem; padding:.8rem 1rem; font-weight:700; cursor:pointer; }
        .error { color:#b42318; font-size:.85rem; }
    </style>
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
