<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cuaderno Naranja | Blog</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/blog.css', 'resources/css/welcome.css'])
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a href="#" class="brand">Cuaderno <span>Naranja</span></a>
            <nav class="site-nav" aria-label="Navegacion principal">
                <button class="menu-toggle" type="button" aria-label="Abrir menu" data-menu-toggle>
                    ☰
                </button>
                <ul class="nav-list" data-menu-list>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="#">Articulos</a></li>
                    <li><a href="#">Autores</a></li>
                    <li><a href="#">Sobre nosotros</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="hero">
            <article class="hero-copy fade-up">
                <span class="chip">Edicion Otoño 2026</span>
                <h1>Un blog pequeno con historias grandes para devs curiosos.</h1>
                <p>
                    Este frontend es una maqueta visual no funcional para tu blog. Tiene portada, tarjetas de posts,
                    categorias, seccion destacada y newsletter de ejemplo para que luego conectes todo con tu backend.
                </p>
                <div class="hero-actions">
                    <a href="#" class="btn btn-primary">Leer destacado</a>
                    <a href="#" class="btn btn-secondary">Ver ultimos posts</a>
                </div>
            </article>

            <aside class="hero-side fade-up delay-1">
                <div>
                    <span class="chip">Panel editorial</span>
                    <p style="margin-top: 0.65rem; line-height: 1.55; color: var(--muted);">
                        Espacio de referencia para un resumen visual de actividad semanal.
                    </p>
                </div>
                <div class="metric-grid">
                    <div class="metric">
                        <strong>24</strong>
                        <span>Posts del mes</span>
                    </div>
                    <div class="metric">
                        <strong>12</strong>
                        <span>Autores activos</span>
                    </div>
                    <div class="metric">
                        <strong>89%</strong>
                        <span>Lectura completa</span>
                    </div>
                    <div class="metric">
                        <strong>4.8</strong>
                        <span>Rating promedio</span>
                    </div>
                </div>
            </aside>
        </section>

        <section class="section-head fade-up delay-1">
            <h2>Ultimos articulos</h2>
            <span class="chip">Total: 4 visibles</span>
        </section>

        <section class="main-grid">
            <div class="post-grid">
                <article class="post-card fade-up delay-1">
                    <div class="post-visual p1"></div>
                    <div class="post-body">
                        <span class="post-meta">Backend • 8 min</span>
                        <h3 class="post-title">Como modelar comentarios anidados sin complicarte</h3>
                        <p class="post-text">Una guia practica para estructurar discusiones en cascada y mantener consultas claras.</p>
                        <div class="post-actions">
                            <small>Por Elena Ruiz</small>
                            <button type="button" class="like-btn" data-like>Me gusta</button>
                        </div>
                    </div>
                </article>

                <article class="post-card fade-up delay-2">
                    <div class="post-visual p2"></div>
                    <div class="post-body">
                        <span class="post-meta">Arquitectura • 6 min</span>
                        <h3 class="post-title">Cuatro errores comunes al separar capas en Laravel</h3>
                        <p class="post-text">Servicios demasiado grandes, controladores hinchados y como evitarlos con reglas simples.</p>
                        <div class="post-actions">
                            <small>Por Tomas Vega</small>
                            <button type="button" class="like-btn" data-like>Me gusta</button>
                        </div>
                    </div>
                </article>

                <article class="post-card fade-up delay-3">
                    <div class="post-visual p3"></div>
                    <div class="post-body">
                        <span class="post-meta">DevOps • 10 min</span>
                        <h3 class="post-title">Checklist de despliegue para no romper produccion</h3>
                        <p class="post-text">Un repaso de validaciones que te ahorran sustos antes de mandar una version al servidor.</p>
                        <div class="post-actions">
                            <small>Por Lucia Mir</small>
                            <button type="button" class="like-btn" data-like>Me gusta</button>
                        </div>
                    </div>
                </article>

                <article class="post-card fade-up delay-4">
                    <div class="post-visual p4"></div>
                    <div class="post-body">
                        <span class="post-meta">Tips • 5 min</span>
                        <h3 class="post-title">Escribir docs tecnicas que si se lean en tu equipo</h3>
                        <p class="post-text">Formato, tono y ritmo para documentos utiles cuando el proyecto crece de verdad.</p>
                        <div class="post-actions">
                            <small>Por Nadia Costa</small>
                            <button type="button" class="like-btn" data-like>Me gusta</button>
                        </div>
                    </div>
                </article>
            </div>

            <aside class="sidebar">
                <section class="panel fade-up delay-2">
                    <h3>Categorias</h3>
                    <ul class="category-list">
                        <li><button type="button">Laravel y APIs</button></li>
                        <li><button type="button">Bases de datos</button></li>
                        <li><button type="button">Testing realista</button></li>
                        <li><button type="button">Productividad dev</button></li>
                    </ul>
                </section>

                <section class="panel fade-up delay-3">
                    <h3>Lo mas leido</h3>
                    <ul class="mini-list">
                        <li>1. Migraciones limpias en equipos grandes</li>
                        <li>2. Paginar sin perder filtros</li>
                        <li>3. Validaciones de formularios complejos</li>
                    </ul>
                </section>

                <section class="panel newsletter fade-up delay-4">
                    <h3>Newsletter semanal</h3>
                    <p>
                        Formulario solo de muestra para diseño. No guarda datos ni envia correos.
                    </p>
                    <label for="mail" style="font-size: 0.84rem; color: var(--muted);">Email</label>
                    <input id="mail" type="email" placeholder="tu@email.com" />
                    <button type="button">Quiero suscribirme</button>
                </section>
            </aside>
        </section>
    </main>

    <footer>
        <div class="container">
            Cuaderno Naranja • Frontend de ejemplo (no funcional) • {{ date('Y') }}
        </div>
    </footer>

    <script>
        const toggleButton = document.querySelector('[data-menu-toggle]');
        const menuList = document.querySelector('[data-menu-list]');
        const likeButtons = document.querySelectorAll('[data-like]');

        if (toggleButton && menuList) {
            toggleButton.addEventListener('click', () => {
                menuList.classList.toggle('is-open');
            });
        }

        // Simula interaccion visual local sin persistencia ni llamadas al backend.
        likeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                button.classList.toggle('is-active');
                button.textContent = button.classList.contains('is-active') ? 'Te gusta' : 'Me gusta';
            });
        });
    </script>
</body>
</html>
