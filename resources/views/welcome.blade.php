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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: 'Space Grotesk', system-ui, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 7% 8%, rgba(31, 122, 140, 0.1) 0 20%, transparent 60%),
                radial-gradient(circle at 95% 18%, rgba(225, 106, 45, 0.18) 0 15%, transparent 55%),
                linear-gradient(180deg, #f7efe4 0%, var(--paper) 58%, #ece4d7 100%);
        }

        .container {
            width: min(1120px, calc(100% - 2.2rem));
            margin: 0 auto;
        }

        .chip {
            display: inline-block;
            padding: 0.35rem 0.8rem;
            border-radius: 999px;
            font-size: 0.78rem;
            letter-spacing: 0.03em;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.7);
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(10px);
            background: rgba(246, 240, 231, 0.88);
            border-bottom: 1px solid rgba(214, 207, 194, 0.7);
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.9rem 0;
        }

        .brand {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            color: var(--ink);
            text-decoration: none;
        }

        .brand span {
            color: var(--accent);
        }

        .menu-toggle {
            display: none;
            border: 1px solid var(--line);
            background: var(--card);
            border-radius: 0.6rem;
            padding: 0.45rem 0.6rem;
            font-size: 1.05rem;
        }

        .nav-list {
            list-style: none;
            display: flex;
            gap: 0.9rem;
            align-items: center;
        }

        .nav-list a {
            text-decoration: none;
            color: var(--ink);
            font-size: 0.95rem;
            padding: 0.45rem 0.7rem;
            border-radius: 999px;
            transition: background 0.2s ease;
        }

        .nav-list a:hover {
            background: rgba(225, 106, 45, 0.14);
        }

        .hero {
            padding: 3.8rem 0 2.4rem;
            display: grid;
            grid-template-columns: 1.2fr 0.9fr;
            gap: 1.2rem;
            align-items: stretch;
        }

        .hero-copy,
        .hero-side {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 1.1rem;
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }

        .hero-copy {
            position: relative;
            overflow: hidden;
        }

        .hero-copy::after {
            content: "";
            position: absolute;
            right: -90px;
            bottom: -80px;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(31, 122, 140, 0.19), rgba(31, 122, 140, 0));
            pointer-events: none;
        }

        .hero h1 {
            font-family: 'Fraunces', Georgia, serif;
            font-size: clamp(2rem, 4.5vw, 3.3rem);
            line-height: 1.08;
            margin: 0.95rem 0;
            max-width: 17ch;
        }

        .hero p {
            color: var(--muted);
            max-width: 46ch;
            line-height: 1.6;
            margin-bottom: 1.4rem;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .btn {
            border: 0;
            border-radius: 0.8rem;
            padding: 0.72rem 1rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 0.2s ease, filter 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }

        .btn:hover {
            transform: translateY(-1px);
            filter: brightness(0.98);
        }

        .btn-primary {
            color: #fff;
            background: var(--accent);
        }

        .btn-secondary {
            color: var(--ink);
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.7);
        }

        .hero-side {
            display: grid;
            align-content: space-between;
            gap: 1rem;
            background:
                linear-gradient(145deg, rgba(31, 122, 140, 0.05), rgba(225, 106, 45, 0.07)),
                var(--card);
        }

        .hero-side .metric-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.8rem;
        }

        .metric {
            border: 1px dashed var(--line);
            border-radius: 0.8rem;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.75);
        }

        .metric strong {
            display: block;
            font-size: 1.25rem;
            margin-bottom: 0.1rem;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.8rem;
            margin: 1rem 0;
        }

        .section-head h2 {
            font-family: 'Fraunces', Georgia, serif;
            font-size: clamp(1.45rem, 2vw, 2.1rem);
            line-height: 1.2;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 1rem;
            padding-bottom: 2.8rem;
        }

        .post-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .post-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 0.95rem;
            overflow: hidden;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        .post-visual {
            height: 160px;
            background-size: cover;
            background-position: center;
        }

        .p1 { background-image: linear-gradient(130deg, #1f7a8c, #48a5b8); }
        .p2 { background-image: linear-gradient(130deg, #f1b83b, #e16a2d); }
        .p3 { background-image: linear-gradient(130deg, #0d3b66, #f95738); }
        .p4 { background-image: linear-gradient(130deg, #6a994e, #a7c957); }

        .post-body {
            padding: 1rem;
            display: grid;
            gap: 0.7rem;
        }

        .post-meta {
            color: var(--muted);
            font-size: 0.83rem;
        }

        .post-title {
            font-size: 1.05rem;
            line-height: 1.35;
            font-weight: 700;
        }

        .post-text {
            color: var(--muted);
            line-height: 1.55;
            font-size: 0.94rem;
        }

        .post-actions {
            margin-top: 0.4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--muted);
        }

        .like-btn {
            border: 1px solid var(--line);
            border-radius: 0.6rem;
            background: #fff;
            padding: 0.35rem 0.6rem;
            font-size: 0.83rem;
            cursor: pointer;
        }

        .like-btn.is-active {
            border-color: rgba(225, 106, 45, 0.4);
            background: rgba(225, 106, 45, 0.12);
            color: #9f420f;
        }

        .sidebar {
            display: grid;
            gap: 1rem;
            align-content: start;
        }

        .panel {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 0.95rem;
            padding: 1rem;
            box-shadow: var(--shadow);
        }

        .panel h3 {
            font-family: 'Fraunces', Georgia, serif;
            margin-bottom: 0.7rem;
            font-size: 1.15rem;
        }

        .category-list,
        .mini-list {
            list-style: none;
            display: grid;
            gap: 0.55rem;
        }

        .category-list button {
            width: 100%;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.82);
            padding: 0.55rem;
            border-radius: 0.6rem;
            text-align: left;
            cursor: pointer;
            font-weight: 500;
        }

        .newsletter {
            background: linear-gradient(140deg, rgba(225, 106, 45, 0.12), rgba(31, 122, 140, 0.08));
        }

        .newsletter p {
            color: var(--muted);
            margin-bottom: 0.8rem;
            line-height: 1.45;
            font-size: 0.92rem;
        }

        .newsletter input,
        .newsletter button {
            width: 100%;
            border-radius: 0.6rem;
            border: 1px solid var(--line);
            padding: 0.58rem 0.7rem;
            font: inherit;
        }

        .newsletter button {
            margin-top: 0.55rem;
            font-weight: 700;
            border: none;
            background: var(--ink);
            color: #fff;
            cursor: pointer;
        }

        footer {
            border-top: 1px dashed var(--line);
            padding: 1.3rem 0 2rem;
            color: var(--muted);
            font-size: 0.9rem;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(18px);
            animation: fade-up 0.62s ease forwards;
        }

        .delay-1 { animation-delay: 0.08s; }
        .delay-2 { animation-delay: 0.16s; }
        .delay-3 { animation-delay: 0.24s; }
        .delay-4 { animation-delay: 0.32s; }

        @keyframes fade-up {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 980px) {
            .hero,
            .main-grid {
                grid-template-columns: 1fr;
            }

            .post-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .site-nav {
                position: relative;
            }

            .nav-list {
                display: none;
                position: absolute;
                right: 0;
                top: calc(100% + 0.55rem);
                border: 1px solid var(--line);
                background: var(--card);
                border-radius: 0.9rem;
                box-shadow: var(--shadow);
                min-width: 200px;
                padding: 0.45rem;
                flex-direction: column;
                align-items: stretch;
            }

            .nav-list.is-open {
                display: flex;
            }

            .nav-list a {
                border-radius: 0.55rem;
            }

            .hero {
                padding-top: 2.3rem;
            }
        }
    </style>
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
                <span class="chip">Edicion primavera 2026</span>
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
