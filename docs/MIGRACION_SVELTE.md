# Migrar el front end de Blade a Svelte

## Contexto
El proyecto es Laravel 13 + Vite 8 + Tailwind 4, con 12 vistas Blade (~1300 líneas) y controladores que devuelven `view(...)` / `redirect()->with('status')`. Auth por sesión (`AuthController`), roles (admin/editor). `resources/js/app.js` está vacío. No hay API JSON.

## Recomendación: Inertia.js + Svelte (no SPA con API separada)
Mantiene rutas, controladores, sesión, CSRF y validación de Laravel. Solo cambia lo que devuelven los controladores (`Inertia::render` en vez de `view`). Alternativa (API REST + Sanctum + SvelteKit) implica reescribir casi todo el backend: innecesario para este proyecto.

## Qué necesitás instalar
**Composer:** `composer require inertiajs/inertia-laravel`
**npm:** `npm i -D @inertiajs/svelte svelte @sveltejs/vite-plugin-svelte`
(Tailwind y los CSS actuales se siguen usando tal cual.)

## Cambios de configuración
1. `vite.config.js`: agregar plugin `svelte()` e `input: 'resources/js/app.js'` (los CSS se importan desde JS o se mantienen como inputs).
2. `resources/views/app.blade.php` (nuevo): layout raíz con `@vite`, `@inertiaHead`, `@inertia`.
3. `php artisan inertia:middleware` → `HandleInertiaRequests`, registrarlo en `bootstrap/app.php` (web). Ahí se comparten globalmente: `auth.user` (con rol), `flash.status`, `errors`.
4. `resources/js/app.js`: `createInertiaApp` con resolver de `./Pages/**/*.svelte`.

## Migración de vistas (Blade → `resources/js/Pages/`)
| Blade | Svelte page |
|---|---|
| welcome / blog | `Blog.svelte` (+ componentes `PostCard`, `Comments`, `RatingForm`) |
| auth/login, register | `Auth/Login.svelte`, `Auth/Register.svelte` |
| dashboard | `Dashboard.svelte` |
| posts/create, edit | `Posts/Create.svelte`, `Posts/Edit.svelte` |
| comments/edit, ratings/edit | `Comments/Edit.svelte`, `Ratings/Edit.svelte` |
| admin/posts/pending, preview | `Admin/Pending.svelte`, `Admin/Preview.svelte` |

Layout compartido `Layouts/App.svelte` (nav, flash de `status`, links según rol).

## Cambios en controladores
- `BlogController`, `AuthController`, `AdminPostController`: `return view('x', $data)` → `return Inertia::render('X', $data)`. Los redirects con `->with('status')` y `validate()` funcionan sin cambios.
- Pasar a props datos serializables (modelos/colecciones con relaciones ya cargadas; lo que hoy el Blade calcula inline, p. ej. promedios o permisos, calcularlo en el controlador).
- En Svelte: formularios con `useForm`/`router` y `<Link>` en lugar de `<form method>` y `<a href>`; para PATCH/DELETE usar `router.patch/delete` (reemplaza `@method`/`@csrf`).
- `route()` de Blade no existe en JS: pasar URLs como props o instalar `tightenco/ziggy`.

## Pasos sugeridos
1. Instalar dependencias y dejar andando Inertia con una página mínima.
2. Layout + login/register.
3. Blog público (la más grande, 357 líneas).
4. Dashboard, posts CRUD, comentarios/ratings.
5. Panel admin.
6. Borrar Blade viejo y CSS no usado; `npm run build`.

## Verificación
- `composer dev`, recorrer: registro → login → ver blog → crear post (editor) → comentar/calificar → aprobar como admin → logout.
- Validaciones de formulario muestran errores; mensajes flash aparecen.
- `php artisan test` y `npm run build` sin errores.

## Preguntas abiertas
- ¿Confirmás Inertia (recomendado) vs. SPA con API?
- ¿JS o TypeScript? ¿Svelte 5 (runes)?
- ¿Migrar todo de una vez o página por página?
