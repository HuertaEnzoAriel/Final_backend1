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
                <span class="user-name">{{ $user->name }}</span>
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
                                <span class="status {{ $post->publicationStatusClass() }}">
                                    {{ $post->publicationStatusLabel() }}
                                </span>
                                @if ($post->isRejected())
                                    <div class="muted" style="margin-top:.35rem; font-size:.82rem;">Tu post fue rechazado por moderacion.</div>
                                    @if ($post->rejection_reason)
                                        <div style="margin-top:.3rem; font-size:.82rem; color:#7f2020; background:rgba(179,50,50,0.07); border:1px solid rgba(179,50,50,0.2); border-radius:.5rem; padding:.4rem .6rem;">
                                            <strong>Motivo:</strong> {{ $post->rejection_reason }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td>{{ $post->comments_count }}</td>
                            <td>{{ number_format((float) ($post->ratings_avg_score ?? 0), 1) }}</td>
                            <td>
                                <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
                                    @if ($post->isRejected() && $user->id == $post->user_id)
                                        <a class="btn" href="{{ route('posts.open', $post->id) }}">Abrir</a>
                                    @else
                                        <a class="btn" href="{{ route('posts.show', $post) }}">Abrir</a>
                                    @endif
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
