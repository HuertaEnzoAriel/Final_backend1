<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Moderacion de posts | Cuaderno Naranja</title>
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
                <a class="btn" href="{{ route('dashboard') }}">Dashboard</a>
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
            <p class="muted">Panel de moderacion</p>
            <h1>Posts pendientes</h1>
            <p class="muted">Publicaciones sin publicar listas para aprobación.</p>
        </section>

        <section class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Autor</th>
                        <th>Fecha de creacion</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td>{{ $post->title }}</td>
                            <td>{{ $post->user?->name ?? 'Sin autor' }}</td>
                            <td>{{ optional($post->created_at)->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <div style="display:flex; gap:.4rem; flex-wrap:wrap;">
                                    <a class="btn" href="{{ route('admin.posts.preview', $post) }}">Ver</a>
                                    <form method="POST" action="{{ route('admin.posts.approve', $post) }}" onsubmit="return confirm('¿Aprobar y publicar este post?');">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn primary" type="submit">Aprobar y Publicar</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.posts.reject', $post) }}" onsubmit="return confirm('¿Rechazar este post?');">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn" type="submit">Rechazar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted">No hay posts pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="table-wrap" style="margin-top:1rem;">
            <div style="padding:1rem 0.9rem 0.4rem;">
                <p class="muted" style="margin:0;">Usuarios</p>
                <h2 style="margin:0.2rem 0 0; font-family:'Fraunces', Georgia, serif;">Cambiar rol</h2>
                <p class="muted" style="margin:.35rem 0 0;">Administra el acceso de cada usuario directamente desde moderacion.</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol actual</th>
                        <th>Nuevo rol</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $managedUser)
                        <tr>
                            <td>{{ $managedUser->name }}</td>
                            <td>{{ $managedUser->email }}</td>
                            <td>
                                <span class="status role-{{ $managedUser->role }}">{{ ucfirst($managedUser->role) }}</span>
                            </td>
                            <td>
                                @if ($managedUser->id === $user->id)
                                    <span class="muted">No disponible para tu propia cuenta.</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.role', $managedUser) }}" class="role-form" onsubmit="return confirm('¿Actualizar el rol de este usuario?');">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" aria-label="Nuevo rol para {{ $managedUser->name }}">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role }}" @selected($managedUser->role === $role)>{{ ucfirst($role) }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn primary" type="submit">Guardar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted">No hay usuarios para administrar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
