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
                                    <button
                                        class="btn"
                                        type="button"
                                        data-reject-id="{{ $post->id }}"
                                        data-reject-title="{{ $post->title }}"
                                        data-reject-action="{{ route('admin.posts.reject', $post) }}"
                                    >Rechazar</button>
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
                        <th>Cambiar a</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $managedUser)
                        @php $isSelf = $managedUser->id === $user->id; @endphp
                        <tr class="{{ $isSelf ? 'row-self' : '' }}">
                            <td>
                                {{ $managedUser->name }}
                                @if ($isSelf)
                                    <span class="badge-you">Tú</span>
                                @endif
                            </td>
                            <td class="muted-cell">{{ $managedUser->email }}</td>
                            <td>
                                <span class="status role-{{ $managedUser->role }}">{{ ucfirst($managedUser->role) }}</span>
                            </td>
                            <td>
                                @if ($isSelf)
                                    <select disabled aria-label="Rol bloqueado — cuenta propia" class="select-disabled">
                                        <option>{{ ucfirst($managedUser->role) }}</option>
                                    </select>
                                    <span class="muted" style="font-size:.8rem; display:block; margin-top:.3rem;">Tu propia cuenta</span>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.role', $managedUser) }}"
                                        class="role-form"
                                        data-current-role="{{ $managedUser->role }}"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <select
                                            name="role"
                                            aria-label="Nuevo rol para {{ $managedUser->name }}"
                                            class="role-select"
                                        >
                                            @foreach ($roles as $role)
                                                <option value="{{ $role }}" @selected($managedUser->role === $role)>{{ ucfirst($role) }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn primary role-save-btn" type="submit" disabled>Guardar</button>
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

        {{-- Modal de rechazo --}}
        <div id="reject-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:100; align-items:center; justify-content:center;">
            <div style="background:#fff; border-radius:1rem; padding:1.5rem; width:100%; max-width:480px; margin:1rem; box-shadow:0 8px 32px rgba(0,0,0,0.18);">
                <p class="muted" style="margin:0 0 .25rem;">Rechazar publicacion</p>
                <h2 style="margin:0 0 1rem; font-family:'Fraunces', Georgia, serif; font-size:1.3rem;" id="reject-modal-title"></h2>
                <form method="POST" id="reject-form">
                    @csrf
                    @method('PATCH')
                    <div class="field" style="margin-bottom:1rem;">
                        <label for="rejection_reason">Motivo del rechazo <span style="color:#c0392b;">*</span></label>
                        <textarea
                            id="rejection_reason"
                            name="rejection_reason"
                            rows="4"
                            maxlength="500"
                            required
                            placeholder="Explicá al autor por qué se rechaza su publicacion..."
                            style="margin-top:.35rem;"
                        ></textarea>
                        <small class="muted" style="display:block; margin-top:.3rem;">Maximo 500 caracteres. El autor podrá ver este motivo.</small>
                    </div>
                    <div style="display:flex; gap:.6rem; justify-content:flex-end;">
                        <button type="button" class="btn" id="reject-cancel">Cancelar</button>
                        <button type="submit" class="btn" style="background:#b33232; border-color:#b33232; color:#fff;">Confirmar rechazo</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        (function () {
            var modal = document.getElementById('reject-modal');
            var form  = document.getElementById('reject-form');
            var title = document.getElementById('reject-modal-title');

            document.querySelectorAll('[data-reject-id]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    title.textContent = btn.dataset.rejectTitle;
                    form.action = btn.dataset.rejectAction;
                    document.getElementById('rejection_reason').value = '';
                    modal.style.display = 'flex';
                });
            });

            document.getElementById('reject-cancel').addEventListener('click', function () {
                modal.style.display = 'none';
            });

            modal.addEventListener('click', function (e) {
                if (e.target === modal) modal.style.display = 'none';
            });
        })();
        </script>

        <script>
        document.querySelectorAll('.role-form').forEach(function (form) {
            var currentRole = form.dataset.currentRole;
            var select = form.querySelector('.role-select');
            var btn = form.querySelector('.role-save-btn');

            select.addEventListener('change', function () {
                btn.disabled = select.value === currentRole;
            });

            form.addEventListener('submit', function (e) {
                if (select.value === currentRole) {
                    e.preventDefault();
                    return;
                }
                var roleName = select.options[select.selectedIndex].text;
                if (!confirm('¿Cambiar el rol de "' + form.closest('tr').querySelector('td').textContent.trim().split('\n')[0].trim() + '" a ' + roleName + '?')) {
                    e.preventDefault();
                }
            });
        });
        </script>
    </main>
</body>
</html>
