<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cuaderno Naranja | Blog</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --paper: #f6f0e7;
            --ink: #1f2528;
            --muted: #5f666c;
            --card: #fffdf9;
            --line: #d6cfc2;
            --accent: #e16a2d;
            --accent-2: #1f7a8c;
            --accent-3: #f1b83b;
            --shadow: 0 14px 28px rgba(31, 37, 40, 0.12);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            min-height: 100vh;
            font-family: 'Space Grotesk', system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 7% 8%, rgba(31, 122, 140, 0.1) 0 20%, transparent 60%),
                radial-gradient(circle at 95% 18%, rgba(225, 106, 45, 0.18) 0 15%, transparent 55%),
                linear-gradient(180deg, #f7efe4 0%, var(--paper) 58%, #ece4d7 100%);
        }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }
        button, input, select, textarea { font: inherit; }
        form { margin: 0; }
        .container { width: min(1120px, calc(100% - 2.2rem)); margin: 0 auto; }
        .site-header {
            position: sticky; top: 0; z-index: 20;
            backdrop-filter: blur(10px);
            background: rgba(246, 240, 231, 0.88);
            border-bottom: 1px solid rgba(214, 207, 194, 0.7);
        }
        .header-inner { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.9rem 0; }
        .brand { font-family: 'Fraunces', Georgia, serif; font-size: 1.35rem; font-weight: 700; letter-spacing: 0.01em; }
        .brand span { color: var(--accent); }
        .menu-toggle { display: none; border: 1px solid var(--line); background: var(--card); border-radius: 0.6rem; padding: 0.45rem 0.6rem; font-size: 1.05rem; }
        .nav-list { list-style: none; display: flex; gap: 0.9rem; align-items: center; }
        .nav-list a { padding: 0.45rem 0.7rem; border-radius: 999px; transition: background 0.2s ease; }
        .nav-list a:hover { background: rgba(225, 106, 45, 0.14); }
        .hero { padding: 3.2rem 0 1.7rem; display: grid; grid-template-columns: 1.2fr 0.9fr; gap: 1.2rem; align-items: stretch; }
        .hero-copy, .hero-side, .panel, .post-card { background: var(--card); border: 1px solid var(--line); border-radius: 1.1rem; box-shadow: var(--shadow); }
        .hero-copy, .hero-side, .panel { padding: 1.4rem; }
        .hero-copy { position: relative; overflow: hidden; }
        .hero-copy::after {
            content: ""; position: absolute; right: -90px; bottom: -80px; width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(31, 122, 140, 0.19), rgba(31, 122, 140, 0)); pointer-events: none;
        }
        .chip { display: inline-block; padding: 0.35rem 0.8rem; border-radius: 999px; font-size: 0.78rem; letter-spacing: 0.03em; border: 1px solid var(--line); background: rgba(255, 255, 255, 0.7); }
        .hero h1, .section-head h2, .panel h3, .detail-title { font-family: 'Fraunces', Georgia, serif; }
        .hero h1 { font-size: clamp(2rem, 4.5vw, 3.3rem); line-height: 1.08; margin: 0.95rem 0; max-width: 17ch; }
        .hero p { color: var(--muted); max-width: 46ch; line-height: 1.6; margin-bottom: 1.4rem; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 0.8rem; }
        .btn {
            border: 0;
            border-radius: 0.8rem;
            padding: 0.72rem 1rem;
            font-weight: 700;
            line-height: 1.1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            transition: transform 0.2s ease, filter 0.2s ease;
        }
        .btn:hover { transform: translateY(-1px); filter: brightness(0.98); }
        .btn-primary { color: #fff; background: var(--accent); }
        .btn-secondary { color: var(--ink); border: 1px solid var(--line); background: rgba(255, 255, 255, 0.7); }
        .hero-side { display: grid; align-content: space-between; gap: 1rem; background: linear-gradient(145deg, rgba(31, 122, 140, 0.05), rgba(225, 106, 45, 0.07)), var(--card); }
        .metric-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.8rem; }
        .metric { border: 1px dashed var(--line); border-radius: 0.8rem; padding: 0.75rem; background: rgba(255, 255, 255, 0.75); }
        .metric strong { display: block; font-size: 1.25rem; margin-bottom: 0.1rem; }
        .section-head { display: flex; justify-content: space-between; align-items: center; gap: 0.8rem; margin: 1rem 0; }
        .section-head h2 { font-size: clamp(1.45rem, 2vw, 2.1rem); line-height: 1.2; }
        .main-grid { display: grid; grid-template-columns: 1fr 320px; gap: 1rem; padding-bottom: 2.8rem; }
        .post-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .post-card { overflow: hidden; display: flex; flex-direction: column; min-height: 100%; }
        .post-visual { height: 160px; background-size: cover; background-position: center; }
        .post-visual-img { height: 160px; width: 100%; object-fit: cover; display: block; }
        .p1 { background-image: linear-gradient(130deg, #1f7a8c, #48a5b8); }
        .p2 { background-image: linear-gradient(130deg, #f1b83b, #e16a2d); }
        .p3 { background-image: linear-gradient(130deg, #0d3b66, #f95738); }
        .p4 { background-image: linear-gradient(130deg, #6a994e, #a7c957); }
        .post-body { padding: 1rem; display: grid; gap: 0.7rem; }
        .post-meta, .muted { color: var(--muted); font-size: 0.9rem; }
        .post-title { font-size: 1.05rem; line-height: 1.35; font-weight: 700; }
        .post-text, .detail-text, .panel p, .comment-content { color: var(--muted); line-height: 1.6; }
        .post-actions { margin-top: 0.4rem; display: flex; justify-content: space-between; align-items: center; color: var(--muted); gap: 0.75rem; }
        .like-btn { border: 1px solid var(--line); border-radius: 0.6rem; background: #fff; padding: 0.35rem 0.6rem; font-size: 0.83rem; cursor: pointer; }
        .like-btn.is-active { border-color: rgba(225, 106, 45, 0.4); background: rgba(225, 106, 45, 0.12); color: #9f420f; }
        .sidebar { display: grid; gap: 1rem; align-content: start; }
        .panel h3 { margin-bottom: 0.7rem; font-size: 1.15rem; }
        .category-list, .mini-list, .comment-list { list-style: none; display: grid; gap: 0.65rem; }
        .category-list button { width: 100%; border: 1px solid var(--line); background: rgba(255, 255, 255, 0.82); padding: 0.55rem; border-radius: 0.6rem; text-align: left; cursor: pointer; font-weight: 500; }
        .newsletter { background: linear-gradient(140deg, rgba(225, 106, 45, 0.12), rgba(31, 122, 140, 0.08)); }
        .newsletter input, .newsletter select, .newsletter textarea, .newsletter button, .form-field, .form-select, .form-textarea {
            width: 100%; border-radius: 0.7rem; border: 1px solid var(--line); padding: 0.68rem 0.75rem; background: #fff;
        }
        .newsletter button, .action-btn { margin-top: 0.55rem; font-weight: 700; border: none; background: var(--ink); color: #fff; cursor: pointer; }
        .flash { border: 1px solid rgba(31, 122, 140, 0.2); background: rgba(31, 122, 140, 0.1); color: #124c57; padding: 0.8rem 1rem; border-radius: 0.85rem; margin: 0.8rem 0 1rem; }
        .detail-layout { display: grid; grid-template-columns: 1fr 320px; gap: 1rem; padding-bottom: 2.8rem; }
        .detail-card { padding: 1.4rem; }
        .detail-meta { display: flex; flex-wrap: wrap; gap: 0.6rem; align-items: center; margin-bottom: 0.85rem; }
        .detail-title { font-size: clamp(2rem, 3vw, 2.7rem); line-height: 1.05; margin-bottom: 0.75rem; max-width: 18ch; }
        .detail-hero {
            margin: 1rem 0 1.25rem; height: 260px; border-radius: 1rem; background-size: cover; background-position: center; border: 1px solid var(--line);
        }
        .detail-hero-img {
            margin: 1rem 0 1.25rem;
            height: 260px;
            width: 100%;
            border-radius: 1rem;
            object-fit: cover;
            border: 1px solid var(--line);
            display: block;
        }
        .detail-body { display: grid; gap: 1rem; }
        .detail-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between; padding-top: 0.25rem; }
        .form-grid { display: grid; gap: 0.8rem; }
        .form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.8rem; }
        .form-group { display: grid; gap: 0.35rem; }
        .label { font-size: 0.86rem; color: var(--muted); }
        .helper { font-size: 0.84rem; color: var(--muted); }
        .rating-row { display: flex; align-items: center; gap: 0.8rem; flex-wrap: wrap; }
        .comment { border-top: 1px solid rgba(214, 207, 194, 0.55); padding-top: 0.9rem; }
        .comment-head { display: flex; justify-content: space-between; gap: 0.8rem; margin-bottom: 0.3rem; }
        .avatar { width: 2rem; height: 2rem; border-radius: 999px; background: linear-gradient(135deg, var(--accent-2), var(--accent)); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 700; }
        .stars { color: #a06a00; letter-spacing: 0.07em; }
        .empty-state { border: 1px dashed var(--line); padding: 1rem; border-radius: 0.85rem; background: rgba(255, 255, 255, 0.68); color: var(--muted); }
        footer { border-top: 1px dashed var(--line); padding: 1.3rem 0 2rem; color: var(--muted); font-size: 0.9rem; }
        .fade-up { opacity: 0; transform: translateY(18px); animation: fade-up 0.62s ease forwards; }
        .delay-1 { animation-delay: 0.08s; }
        .delay-2 { animation-delay: 0.16s; }
        .delay-3 { animation-delay: 0.24s; }
        .delay-4 { animation-delay: 0.32s; }
        @keyframes fade-up { to { opacity: 1; transform: translateY(0); } }
        @media (max-width: 980px) {
            .hero, .main-grid, .detail-layout { grid-template-columns: 1fr; }
            .post-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 760px) {
            .menu-toggle { display: inline-flex; align-items: center; justify-content: center; }
            .site-nav { position: relative; }
            .nav-list {
                display: none; position: absolute; right: 0; top: calc(100% + 0.55rem);
                border: 1px solid var(--line); background: var(--card); border-radius: 0.9rem; box-shadow: var(--shadow);
                min-width: 200px; padding: 0.45rem; flex-direction: column; align-items: stretch;
            }
            .nav-list.is-open { display: flex; }
            .nav-list a { border-radius: 0.55rem; }
            .hero { padding-top: 2.2rem; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ route('blog.index') }}" class="brand">Cuaderno <span>Naranja</span></a>
            <nav class="site-nav" aria-label="Navegacion principal">
                <button class="menu-toggle" type="button" aria-label="Abrir menu" data-menu-toggle>☰</button>
                <ul class="nav-list" data-menu-list>
                    <li><a href="{{ route('blog.index') }}">Inicio</a></li>
                    <li><a href="#posts">Articulos</a></li>
                    <li><a href="#community">Comunidad</a></li>
                    <li><a href="#contact">Contacto</a></li>
                    @if ($currentUser)
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('posts.create') }}">Publicar</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary" style="padding:0.45rem 0.7rem;">Salir</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}">Entrar</a></li>
                        <li><a href="{{ route('register') }}">Crear cuenta</a></li>
                    @endif
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        @if (session('status'))
            <div class="flash fade-up">
                {{ session('status') }}
            </div>
        @endif

        @if ($selectedPost)
            <section class="hero">
                <article class="hero-copy fade-up">
                    <span class="chip">Lectura destacada</span>
                    <h1>{{ $selectedPost->title }}</h1>
                    <p>
                        {{ str($selectedPost->content)->limit(180) }}
                    </p>
                    <div class="hero-actions">
                        <a href="#comentarios" class="btn btn-primary">Ver comentarios</a>
                        <a href="{{ route('blog.index') }}" class="btn btn-secondary">Volver al inicio</a>
                    </div>
                </article>

                <aside class="hero-side fade-up delay-1">
                    <div>
                        <span class="chip">Resumen del post</span>
                        <p style="margin-top: 0.65rem; line-height: 1.55; color: var(--muted);">
                            Autor: {{ $selectedPost->user->name }} · Publicado {{ optional($selectedPost->published_at)->format('d/m/Y') ?? 'sin fecha' }}
                        </p>
                    </div>
                    <div class="metric-grid">
                        <div class="metric"><strong>{{ $selectedPost->comments_count }}</strong><span>Comentarios</span></div>
                        <div class="metric"><strong>{{ number_format((float) ($selectedPost->ratings_avg_score ?? 0), 1) }}</strong><span>Rating medio</span></div>
                        <div class="metric"><strong>{{ $selectedPost->ratings_count }}</strong><span>Votos</span></div>
                        <div class="metric"><strong>{{ $selectedPost->is_published ? 'SI' : 'NO' }}</strong><span>Visible</span></div>
                    </div>
                </aside>
            </section>

            <section class="detail-layout">
                <article class="hero-copy detail-card fade-up delay-1">
                    <div class="detail-meta">
                        <span class="chip">Articulo</span>
                        <span class="muted">Por {{ $selectedPost->user->name }}</span>
                        <span class="muted">{{ optional($selectedPost->published_at)->format('d M, Y') ?? 'Sin publicar' }}</span>
                    </div>
                    @if ($currentUser && $selectedPost->user_id === $currentUser->id)
                        <div style="display:flex; gap:0.5rem; margin-bottom:0.9rem; flex-wrap:wrap;">
                            <a href="{{ route('posts.edit', $selectedPost) }}" class="btn btn-secondary" style="padding:0.45rem 0.7rem;">Editar post</a>
                            <form method="POST" action="{{ route('posts.destroy', $selectedPost) }}" onsubmit="return confirm('¿Eliminar este post?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary" style="padding:0.45rem 0.7rem;">Eliminar post</button>
                            </form>
                        </div>
                    @endif
                    <h2 class="detail-title">{{ $selectedPost->title }}</h2>
                    @if ($selectedPost->image_path)
                        <img class="detail-hero-img" src="{{ asset('storage/' . $selectedPost->image_path) }}" alt="Imagen de {{ $selectedPost->title }}">
                    @else
                        <div class="detail-hero p1"></div>
                    @endif
                    <div class="detail-body">
                        <p class="detail-text">{{ $selectedPost->content }}</p>
                        <div class="detail-actions">
                            <div class="rating-row">
                                <span class="chip">Promedio: {{ number_format((float) ($selectedPost->ratings_avg_score ?? 0), 1) }}/5</span>
                                <span class="stars">{{ str_repeat('★', (int) round((float) ($selectedPost->ratings_avg_score ?? 0))) }}</span>
                            </div>
                            <span class="muted">{{ $comments->count() }} comentarios visibles</span>
                        </div>
                    </div>
                </article>

                <aside class="sidebar">
                    <section class="panel fade-up delay-1">
                        <h3>Publicar comentario</h3>
                        <form method="POST" action="{{ route('posts.comments.store', $selectedPost) }}" class="form-grid">
                            @csrf
                            @if ($currentUser)
                                <div class="empty-state">Comentando como {{ $currentUser->name }}.</div>
                            @else
                                <div class="form-group">
                                    <label class="label" for="user_id_comment">Escribe como</label>
                                    <select id="user_id_comment" name="user_id" class="form-select" required>
                                        <option value="">Selecciona un usuario</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('user_id') <small class="helper">{{ $message }}</small> @enderror
                                </div>
                            @endif
                            <div class="form-group">
                                <label class="label" for="content">Comentario</label>
                                <textarea id="content" name="content" rows="4" class="form-textarea" required>{{ old('content') }}</textarea>
                                @error('content') <small class="helper">{{ $message }}</small> @enderror
                            </div>
                            <button class="action-btn" type="submit">Enviar comentario</button>
                        </form>
                    </section>

                    <section class="panel fade-up delay-2">
                        <h3>Calificar post</h3>
                        @if ($currentUserRating)
                            <div class="empty-state" style="margin-bottom:0.8rem;">
                                Tu rating actual: {{ $currentUserRating->score }}/5
                            </div>
                            <div style="display:flex; gap:0.5rem; margin-bottom:0.8rem; flex-wrap:wrap;">
                                <a href="{{ route('ratings.edit', $currentUserRating) }}" class="btn btn-secondary" style="padding:0.45rem 0.7rem;">Editar rating</a>
                                <form method="POST" action="{{ route('ratings.destroy', $currentUserRating) }}" onsubmit="return confirm('¿Eliminar tu rating?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-secondary" type="submit" style="padding:0.45rem 0.7rem;">Eliminar rating</button>
                                </form>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('posts.ratings.store', $selectedPost) }}" class="form-grid">
                            @csrf
                            <div class="form-row">
                                <div class="form-group">
                                    @if ($currentUser)
                                        <label class="label">Usuario</label>
                                        <div class="empty-state" style="padding:0.7rem 0.75rem;">Calificando como {{ $currentUser->name }}.</div>
                                    @else
                                        <label class="label" for="user_id_rating">Usuario</label>
                                        <select id="user_id_rating" name="user_id" class="form-select" required>
                                            <option value="">Selecciona un usuario</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('user_id') <small class="helper">{{ $message }}</small> @enderror
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="label" for="score">Puntaje</label>
                                    <select id="score" name="score" class="form-select" required>
                                        <option value="">Selecciona</option>
                                        @foreach (range(1, 5) as $score)
                                            <option value="{{ $score }}" @selected((string) old('score') === (string) $score)>{{ $score }}</option>
                                        @endforeach
                                    </select>
                                    @error('score') <small class="helper">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <button class="action-btn" type="submit">Guardar rating</button>
                        </form>
                    </section>
                </aside>
            </section>

            <section id="comentarios" class="main-grid">
                <div class="panel fade-up delay-1">
                    <div class="section-head" style="margin-top:0;">
                        <h2>Comentarios</h2>
                        <span class="chip">{{ $comments->count() }} publicados</span>
                    </div>

                    <ul class="comment-list">
                        @forelse ($comments as $comment)
                            <li class="comment">
                                <div class="comment-head">
                                    <div style="display:flex; gap:0.75rem; align-items:center;">
                                        <div class="avatar">{{ mb_substr($comment->user->name, 0, 1) }}</div>
                                        <div>
                                            <strong>{{ $comment->user->name }}</strong><br>
                                            <small class="muted">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    @if ($currentUser && $comment->user_id === $currentUser->id)
                                        <div style="display:flex; gap:0.45rem; flex-wrap:wrap; align-items:center;">
                                            <a href="{{ route('comments.edit', $comment) }}" class="btn btn-secondary" style="padding:0.35rem 0.6rem;">Editar</a>
                                            <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('¿Eliminar comentario?');" style="display:flex; align-items:center; margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-secondary" style="padding:0.35rem 0.6rem;">Eliminar</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                                <p class="comment-content">{{ $comment->content }}</p>
                            </li>
                        @empty
                            <li class="empty-state">Aun no hay comentarios en este articulo.</li>
                        @endforelse
                    </ul>
                </div>

                <aside class="sidebar">
                    <section class="panel fade-up delay-2">
                        <h3>Top posts</h3>
                        <ul class="mini-list">
                            @foreach ($topPosts as $post)
                                <li>
                                    <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                                    <div class="muted">{{ $post->comments_count }} comentarios · {{ number_format((float) ($post->ratings_avg_score ?? 0), 1) }} rating</div>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                </aside>
            </section>
        @else
            <section class="hero">
                <article class="hero-copy fade-up">
                    <span class="chip">Edicion primavera 2026</span>
                    <h1>Un blog pequeno con historias grandes para devs curiosos.</h1>
                    <p>
                        Ahora el contenido sale desde la base de datos: posts, autores, comentarios y ratings reales de ejemplo.
                    </p>
                    <div class="hero-actions">
                        <a href="#posts" class="btn btn-primary">Explorar posts</a>
                        <a href="#community" class="btn btn-secondary">Ver comunidad</a>
                    </div>
                </article>

                <aside class="hero-side fade-up delay-1">
                    <div>
                        <span class="chip">Panel editorial</span>
                        <p style="margin-top: 0.65rem; line-height: 1.55; color: var(--muted);">
                            Resumen dinamico de actividad usando la informacion de la base de datos.
                        </p>
                    </div>
                    <div class="metric-grid">
                        <div class="metric"><strong>{{ $siteStats['published_posts'] }}</strong><span>Posts publicados</span></div>
                        <div class="metric"><strong>{{ $siteStats['total_comments'] }}</strong><span>Comentarios</span></div>
                        <div class="metric"><strong>{{ $siteStats['total_ratings'] }}</strong><span>Ratings</span></div>
                        <div class="metric"><strong>{{ number_format($siteStats['average_rating'], 1) }}</strong><span>Media global</span></div>
                    </div>
                </aside>
            </section>

            <section id="posts" class="section-head fade-up delay-1">
                <h2>Ultimos articulos</h2>
                <span class="chip">{{ $posts->count() }} visibles</span>
            </section>

            <section class="main-grid">
                <div class="post-grid">
                    @foreach ($posts as $post)
                        @php
                            $visual = ['p1', 'p2', 'p3', 'p4'][$loop->index % 4];
                        @endphp
                        <article class="post-card fade-up delay-{{ min($loop->index + 1, 4) }}">
                            @if ($post->image_path)
                                <img class="post-visual-img" src="{{ asset('storage/' . $post->image_path) }}" alt="Imagen de {{ $post->title }}">
                            @else
                                <div class="post-visual {{ $visual }}"></div>
                            @endif
                            <div class="post-body">
                                <span class="post-meta">{{ $post->user->name }} · {{ optional($post->published_at)->diffForHumans() ?? 'Sin fecha' }}</span>
                                <h3 class="post-title">{{ $post->title }}</h3>
                                <p class="post-text">{{ str($post->content)->limit(140) }}</p>
                                <div class="post-actions">
                                    <small>{{ $post->comments_count }} comentarios · {{ number_format((float) ($post->ratings_avg_score ?? 0), 1) }} rating</small>
                                    <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary" style="padding:0.45rem 0.7rem;">Leer</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="sidebar">
                    @if ($currentUser)
                        <section class="panel fade-up delay-1">
                            <h3>Tu espacio de autor</h3>
                            <p class="muted" style="margin-bottom:0.8rem;">Sesion iniciada como {{ $currentUser->name }}.</p>
                            <div class="hero-actions">
                                <a href="{{ route('posts.create') }}" class="btn btn-primary">Ir a publicar</a>
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Ver dashboard</a>
                            </div>
                        </section>
                    @else
                        <section class="panel fade-up delay-1">
                            <h3>Acceso</h3>
                            <p class="muted" style="margin-bottom:0.8rem;">Inicia sesion o crea una cuenta para publicar con tu propio usuario.</p>
                            <div class="hero-actions">
                                <a href="{{ route('login') }}" class="btn btn-primary">Entrar</a>
                                <a href="{{ route('register') }}" class="btn btn-secondary">Registrarme</a>
                            </div>
                        </section>
                    @endif

                    <section class="panel fade-up delay-2">
                        <h3>Categorias</h3>
                        <ul class="category-list">
                            <li><button type="button">Laravel y APIs</button></li>
                            <li><button type="button">Bases de datos</button></li>
                            <li><button type="button">Testing realista</button></li>
                            <li><button type="button">Productividad dev</button></li>
                        </ul>
                    </section>

                    <section id="community" class="panel fade-up delay-3">
                        <h3>Lo mas leido</h3>
                        <ul class="mini-list">
                            @foreach ($topPosts as $post)
                                <li>
                                    <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                                    <div class="muted">{{ $post->comments_count }} comentarios · {{ number_format((float) ($post->ratings_avg_score ?? 0), 1) }}/5</div>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                </aside>
            </section>
        @endif
    </main>

    <footer id="contact">
        <div class="container">Cuaderno Naranja • Frontend funcional conectado a BD • {{ date('Y') }}</div>
    </footer>

    <script>
        const toggleButton = document.querySelector('[data-menu-toggle]');
        const menuList = document.querySelector('[data-menu-list]');
        if (toggleButton && menuList) {
            toggleButton.addEventListener('click', () => {
                menuList.classList.toggle('is-open');
            });
        }
    </script>
</body>
</html>
