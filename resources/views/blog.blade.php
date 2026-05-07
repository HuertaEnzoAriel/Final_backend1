<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cuaderno Naranja | Blog</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/blog.css'])
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ route('blog.index') }}" class="brand">Cuaderno <span>Naranja</span></a>
            <nav class="site-nav" aria-label="Navegacion principal">
                <button class="menu-toggle" type="button" aria-label="Abrir menu" data-menu-toggle>☰</button>
                <ul class="nav-list" data-menu-list>
                    <li><a href="{{ route('blog.index') }}">Inicio</a></li>
                    @if ($currentUser)
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        @if ($currentUser->role === 'admin')
                            <li><a href="{{ route('admin.posts.pending') }}">Moderacion</a></li>
                        @endif
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

                @if ($currentUser)
                    <form method="POST" action="{{ route('posts.comments.store', $selectedPost) }}" class="form-grid">
                        @csrf
                        <div class="empty-state">Comentando como {{ $currentUser->name }}.</div>

                        <div class="form-group">
                            <label class="label" for="content">Comentario</label>
                            <textarea id="content" name="content" rows="4" class="form-textarea" required>{{ old('content') }}</textarea>
                            @error('content') <small class="helper">{{ $message }}</small> @enderror
                        </div>
                        <button class="action-btn" type="submit">Enviar comentario</button>
                    </form>
                @else
                    <div class="empty-state">
                        <a href="{{ route('login') }}" style="color: inherit; text-decoration: underline;">Inicia sesión</a> para dejar un comentario.
                    </div>
                @endif
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

                        @if ($currentUser)
                            <form method="POST" action="{{ route('posts.ratings.store', $selectedPost) }}" class="form-grid">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="label">Usuario</label>
                                        <div class="empty-state" style="padding:0.7rem 0.75rem;">Calificando como {{ $currentUser->name }}.</div>
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
                        @else
                            <div class="empty-state">
                                <a href="{{ route('login') }}" style="color: inherit; text-decoration: underline;">Inicia sesión</a> para calificar este post.
                            </div>
                        @endif
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
                    <span class="chip">Edicion Otoño 2026</span>
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
