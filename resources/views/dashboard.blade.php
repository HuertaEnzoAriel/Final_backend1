<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Cuaderno Naranja</title>
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
                <a class="btn primary" href="{{ route('posts.create') }}">Nuevo post</a>
                <a class="btn" href="{{ route('blog.index') }}">Ver blog</a>
                @if ($user->role === 'admin')
                    <a class="btn" href="{{ route('admin.posts.pending') }}">Moderacion</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Salir</button>
                </form>
            </div>
        </div>
    </header>

    <main class="container">
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        <section class="hero">
            <p class="muted">Panel personal de {{ $user->name }}</p>
            <h1>Tu dashboard</h1>
            <p class="muted">Desde aqui puedes revisar tus publicaciones y abrir la pantalla dedicada de creacion.</p>
            <div class="metrics">
                <div class="metric"><strong>{{ $totalPosts }}</strong><span>Total posts</span></div>
                <div class="metric"><strong>{{ $publishedPosts }}</strong><span>Publicados</span></div>
                <div class="metric"><strong>{{ $totalComments }}</strong><span>Comentarios recibidos</span></div>
                <div class="metric"><strong>{{ number_format($averageRating, 1) }}</strong><span>Rating medio</span></div>
            </div>
        </section>

        <section class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Comentarios</th>
                        <th>Rating</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td>{{ $post->title }}</td>
                            <td>{{ optional($post->published_at)->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <span class="status {{ $post->is_published ? 'ok' : 'draft' }}">
                                    {{ $post->is_published ? 'Publicado' : 'Borrador' }}
                                </span>
                            </td>
                            <td>{{ $post->comments_count }}</td>
                            <td>{{ number_format((float) ($post->ratings_avg_score ?? 0), 1) }}</td>
                            <td>
                                <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
                                    <a class="btn" href="{{ route('posts.show', $post) }}">Abrir</a>
                                    <a class="btn" href="{{ route('posts.edit', $post) }}">Editar</a>
                                    <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('¿Eliminar este post?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn" type="submit">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="muted">Aun no tienes publicaciones. Crea la primera desde "Nuevo post".</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
