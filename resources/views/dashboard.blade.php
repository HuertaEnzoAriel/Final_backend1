<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Cuaderno Naranja</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <style>
        body { margin:0; min-height:100vh; font-family:'Space Grotesk',system-ui,sans-serif; color:#1f2528; background:linear-gradient(180deg,#f7efe4 0%,#f6f0e7 100%); }
        .container { width:min(1120px, calc(100% - 2.2rem)); margin:0 auto; }
        .header { padding:1rem 0; border-bottom:1px solid rgba(214,207,194,.8); background:rgba(246,240,231,.9); position:sticky; top:0; backdrop-filter: blur(10px); }
        .head-inner { display:flex; justify-content:space-between; gap:.8rem; align-items:center; }
        .brand { font-family:'Fraunces',Georgia,serif; font-weight:700; font-size:1.3rem; text-decoration:none; color:#1f2528; }
        .brand span { color:#e16a2d; }
        .actions { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; }
        .btn {
            border:1px solid #d6cfc2;
            background:#fffdf9;
            color:#1f2528;
            padding:.55rem .85rem;
            border-radius:.7rem;
            text-decoration:none;
            font: inherit;
            font-weight:600;
            line-height:1.1;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            cursor:pointer;
        }
        .btn.primary { background:#e16a2d; border-color:#e16a2d; color:#fff; }
        main { padding:1.5rem 0 2rem; }
        .hero { background:#fffdf9; border:1px solid #d6cfc2; border-radius:1rem; box-shadow:0 14px 28px rgba(31,37,40,.12); padding:1.2rem; margin-bottom:1rem; }
        h1 { margin:.2rem 0 .6rem; font-family:'Fraunces',Georgia,serif; font-size:2rem; }
        .muted { color:#5f666c; }
        .metrics { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.75rem; margin-top:1rem; }
        .metric { border:1px dashed #d6cfc2; border-radius:.8rem; background:#fff; padding:.75rem; }
        .metric strong { display:block; font-size:1.2rem; }
        .table-wrap { background:#fffdf9; border:1px solid #d6cfc2; border-radius:1rem; box-shadow:0 14px 28px rgba(31,37,40,.12); overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:.8rem .9rem; border-bottom:1px solid rgba(214,207,194,.7); }
        th { font-size:.86rem; color:#5f666c; background:#faf5ee; }
        .status { display:inline-block; font-size:.78rem; padding:.2rem .5rem; border-radius:999px; border:1px solid #d6cfc2; background:#fff; }
        .ok { border-color:rgba(31,122,140,.35); background:rgba(31,122,140,.12); color:#124c57; }
        .draft { border-color:rgba(225,106,45,.35); background:rgba(225,106,45,.12); color:#8c3e12; }
        .flash { margin-bottom:1rem; border:1px solid rgba(31,122,140,.2); background:rgba(31,122,140,.1); color:#124c57; padding:.8rem 1rem; border-radius:.85rem; }
        @media (max-width:900px) {
            .metrics { grid-template-columns:repeat(2,minmax(0,1fr)); }
            table, thead, tbody, th, td, tr { display:block; }
            thead { display:none; }
            td { border-bottom:0; padding:.45rem .9rem; }
            tr { border-bottom:1px solid rgba(214,207,194,.7); padding:.4rem 0; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container head-inner">
            <a class="brand" href="{{ route('blog.index') }}">Cuaderno <span>Naranja</span></a>
            <div class="actions">
                <a class="btn primary" href="{{ route('posts.create') }}">Nuevo post</a>
                <a class="btn" href="{{ route('blog.index') }}">Ver blog</a>
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
