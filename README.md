# Blog — Examen Final (Desarrollo Backend)

Blog con moderación de publicaciones, comentarios y calificaciones (1 a 5 estrellas).

El proyecto tiene **dos frontends** que comparten el mismo backend y la misma base de datos:

| Parte | Carpeta | Tecnología | Cómo se comunica |
|---|---|---|---|
| Backend + vistas Blade | raíz del repo | Laravel 13 (PHP) | Sesión web (cookies) |
| API REST | `routes/api.php` | Laravel Sanctum | Token Bearer |
| SPA | `frontend/` | Svelte 5 + Vite + Tailwind 4 | Consume la API en `http://localhost:8000/api` |

---

## 1. Versiones necesarias

| Tecnología | Versión mínima | Motivo |
|---|---|---|
| PHP | **8.3** o superior | Requerido por Laravel 13 (`composer.json`) |
| Composer | **2.x** | Gestor de dependencias PHP |
| MySQL | **8.0** o superior (o MariaDB 10.6+) | Base de datos `bdblogs` |
| Node.js | **20.19+** o **22.12+** | Requerido por Vite 8 |
| npm | **10** o superior | Viene con Node |

Extensiones de PHP necesarias (en XAMPP/Laragon ya vienen activas): `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `bcmath`.

Dependencias principales del proyecto:

| Paquete | Versión |
|---|---|
| laravel/framework | ^13.0 |
| laravel/sanctum | ^4.0 |
| phpunit/phpunit | ^12.5 |
| svelte | ^5.57 |
| vite | ^8.3 |
| tailwindcss / @tailwindcss/vite | ^4.3 |

Para verificar lo instalado:

```bash
php -v
composer -V
node -v
npm -v
mysql --version
```

---

## 2. Orden de ejecución para levantar el proyecto

> Los comandos se ejecutan desde la **raíz del repo** salvo que se indique lo contrario.

### Paso 1 — Instalar dependencias de PHP

```bash
composer install
```

### Paso 2 — Crear el archivo `.env`

```bash
# Linux / macOS / Git Bash
cp .env.example .env

# Windows (PowerShell)
Copy-Item .env.example .env
```

Editar `.env` y dejar estas variables así (el `.env.example` viene con SQLite, hay que pasarlo a MySQL):

```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bdblogs
DB_USERNAME=root
DB_PASSWORD=

# No hay tabla "jobs", así que las colas se procesan en el momento.
QUEUE_CONNECTION=sync

# Usuario administrador que crea el seeder. ADMIN_PASSWORD NO puede quedar vacío.
ADMIN_NAME="Admin"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="admin1234"
```

### Paso 3 — Generar la clave de la aplicación

```bash
php artisan key:generate
```

### Paso 4 — Crear la base de datos

Con MySQL corriendo, ejecutar en phpMyAdmin, MySQL Workbench o la consola:

```sql
CREATE DATABASE IF NOT EXISTS bdblogs
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

### Paso 5 — Crear las tablas y cargar datos de prueba

```bash
php artisan migrate --seed
```

> Alternativa sin migraciones: ejecutar [database/sql/bdblogs.sql](database/sql/bdblogs.sql) (ver [sección 3](#3-script-sql-de-la-base-de-datos)) y después correr solo `php artisan db:seed`. En ese caso el paso 4 no hace falta, porque el script ya crea la base.

Para empezar de cero en cualquier momento (borra todo y vuelve a cargar los datos):

```bash
php artisan migrate:fresh --seed
```

### Paso 6 — Habilitar las imágenes de los posts

Las imágenes se guardan en `storage/app/public/posts`. Para que sean accesibles desde el navegador:

```bash
php artisan storage:link
```

### Paso 7 — Compilar los estilos de las vistas Blade

```bash
npm install
npm run build
```

> Si se va a modificar el CSS de `resources/css/`, usar `npm run dev` en lugar de `npm run build` (queda corriendo).

### Paso 8 — Levantar el servidor de Laravel

```bash
php artisan serve
```

Queda disponible en **http://localhost:8000** (vistas Blade + API en `/api`).

### Paso 9 — Levantar la SPA en Svelte (otra terminal)

```bash
cd frontend
npm install
npm run dev
```

Queda disponible en **http://localhost:5173** (si el puerto está ocupado, Vite usa el 5174 y lo muestra en consola).

> **Importante:** la SPA necesita que `php artisan serve` esté corriendo en el puerto 8000. La URL de la API está fija en [frontend/src/models/api/config.js](frontend/src/models/api/config.js).

### Resumen rápido

```bash
composer install
cp .env.example .env            # y editar DB_* / ADMIN_* / QUEUE_CONNECTION
php artisan key:generate
# CREATE DATABASE bdblogs ...   (en MySQL)
php artisan migrate --seed
php artisan storage:link
npm install && npm run build
php artisan serve               # terminal 1

cd frontend                     # terminal 2
npm install
npm run dev
```

---

## 3. Script SQL de la base de datos

El script está en [database/sql/bdblogs.sql](database/sql/bdblogs.sql).

### Qué hace

1. Crea la base `bdblogs` (si no existe) con `utf8mb4`.
2. **Borra** las tablas del proyecto si ya existen.
3. Crea todas las tablas con sus columnas, índices y claves foráneas, igual que las migraciones de `database/migrations/`.
4. Carga la tabla `migrations` para que Laravel considere las migraciones como ya ejecutadas y `php artisan migrate` no intente crear las tablas otra vez.

**No carga datos.** Los usuarios, posts, comentarios y calificaciones de prueba se cargan después con `php artisan db:seed`, porque las contraseñas tienen que guardarse con bcrypt y eso lo hace Laravel.

### Para qué sirve

El camino normal para armar la base es `php artisan migrate --seed` (paso 5). El script es una **alternativa** para estos casos:

- **Crear la base sin usar Laravel:** por ejemplo, importándola desde phpMyAdmin o Workbench en una máquina donde todavía no está instalado PHP o Composer.
- **Ver el esquema completo en un solo archivo:** sirve para revisar o corregir el modelo de datos sin abrir nueve migraciones distintas, o como entregable del examen.
- **Recuperar la base si se rompe:** si `migrate` falla porque la tabla `migrations` quedó desincronizada con las tablas reales, el script deja todo limpio y consistente.
- **Usarlo en otro servidor:** se puede pasar a un administrador de base de datos o a un hosting que solo acepta archivos `.sql`.

### Cómo ejecutarlo

Con phpMyAdmin: pestaña **Importar**, elegir `database/sql/bdblogs.sql` y continuar.

Por consola, desde la raíz del repo:

```bash
# Linux / macOS / Git Bash / CMD
mysql -u root -p < database/sql/bdblogs.sql

# PowerShell (no soporta "<")
Get-Content database/sql/bdblogs.sql | mysql -u root -p
```

Y después cargar los datos de prueba:

```bash
php artisan db:seed
```

> **Ojo:** la fuente de verdad son las migraciones. Si se agrega o modifica una migración, también hay que actualizar este script, porque si no los dos quedan distintos.

### Diagrama de relaciones

```
users 1───N posts 1───N comments N───1 users
              │
              └──1───N ratings  N───1 users   (único por post + usuario)

users 1───N personal_access_tokens   (tokens de la API)
```

Todas las claves foráneas tienen `ON DELETE CASCADE`: al borrar un usuario se borran sus posts, comentarios y calificaciones; al borrar un post se borran sus comentarios y calificaciones.

---

## 4. Usuarios de prueba

Los crea `php artisan db:seed` (ver [database/seeders/UserSeeder.php](database/seeders/UserSeeder.php)).

| Nombre | Email | Contraseña | Rol |
|---|---|---|---|
| *(ADMIN_NAME)* | *(ADMIN_EMAIL)* | *(ADMIN_PASSWORD)* | admin |
| Ana Torres | ana@example.com | password | admin |
| Luis Moreno | luis@example.com | password | editor |
| Marta Rivas | marta@example.com | password | user |
| Sergio Vidal | sergio@example.com | password | user |
| Paula Gil | paula@example.com | password | user |

Además se cargan 4 posts (3 publicados y 1 rechazado), comentarios y calificaciones.

---

## 5. Roles y flujo de publicación

| Rol | Puede |
|---|---|
| **user** | Leer, comentar, calificar y solicitar ser editor |
| **editor** | Lo anterior + crear, editar y borrar sus posts |
| **admin** | Lo anterior + aprobar/rechazar posts y cambiar roles de usuarios |

Estados de un post (columna `is_published`):

```
 crear post ──► 0 Pendiente ──► admin aprueba ──► 1 Publicado
                     │
                     └──► admin rechaza (con motivo) ──► 2 Rechazado
```

Solo los posts **publicados** se pueden ver, comentar y calificar.

---

## 6. Endpoints de la API

Base: `http://localhost:8000/api`

| Método | Ruta | Auth | Descripción |
|---|---|---|---|
| GET | `/posts` | — | Posts publicados, estadísticas generales y top 3 |
| GET | `/posts/ranking` | — | Posts ordenados por promedio de calificación (desempata por comentarios) |
| GET | `/authors` | — | Autores con cantidad de posts, comentarios, promedio y sus posts |
| POST | `/register` | — | Crear cuenta, devuelve token |
| POST | `/login` | — | Iniciar sesión, devuelve token |
| POST | `/logout` | Bearer | Revoca el token actual |
| GET | `/user` | Bearer | Datos del usuario logueado |

Las rutas protegidas se llaman con el header:

```
Authorization: Bearer <token>
Accept: application/json
```

---

## 7. Estructura del proyecto

```
Blog-1/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/                 # Controladores de la API (Sanctum)
│   │   ├── AuthController.php   # Login/registro web
│   │   ├── BlogController.php   # Posts, comentarios, ratings (web)
│   │   └── AdminPostController.php  # Moderación
│   └── Models/                  # User, Post, Comment, Rating
├── database/
│   ├── migrations/
│   ├── factories/
│   ├── seeders/
│   └── sql/                     # Script SQL equivalente a las migraciones
├── resources/
│   ├── css/                     # Estilos de las vistas Blade
│   └── views/                   # Vistas Blade
├── routes/
│   ├── web.php                  # Rutas con sesión
│   └── api.php                  # Rutas de la API
└── frontend/                    # SPA en Svelte 5
    └── src/
        ├── lib/                 # Componentes (un .css por componente)
        ├── page/                # Páginas
        ├── models/              # Constantes: API, rutas, textos, roles
        ├── services/            # Llamadas a la API
        └── state/               # Estado global (sesión, tema)
```

La SPA usa rutas con hash (`#/login`, `#/ranking`, `#/posts/5`), así que no necesita configuración extra del servidor.

---

## 8. Comandos útiles

| Comando | Para qué sirve |
|---|---|
| `php artisan migrate:fresh --seed` | Reinicia la base y recarga los datos de prueba |
| `php artisan route:list` | Lista todas las rutas web y API |
| `php artisan optimize:clear` | Limpia caché de config, rutas y vistas |
| `php artisan test` | Ejecuta los tests |
| `php artisan tinker` | Consola interactiva para probar modelos |
| `npm run check` *(en `frontend/`)* | Revisa tipos y errores de Svelte |
| `npm run build` *(en `frontend/`)* | Genera la SPA lista para producción en `frontend/dist` |

---

## 9. Problemas frecuentes

| Problema | Solución |
|---|---|
| `SQLSTATE[HY000] [1049] Unknown database 'bdblogs'` | Falta crear la base (paso 4) |
| `could not find driver` | Activar `extension=pdo_mysql` en `php.ini` |
| `No application encryption key has been specified` | Ejecutar `php artisan key:generate` |
| `Vite manifest not found` al abrir una vista Blade | Ejecutar `npm install && npm run build` en la raíz |
| Las imágenes de los posts no se ven | Ejecutar `php artisan storage:link` |
| No se puede entrar con el usuario admin del `.env` | `ADMIN_PASSWORD` estaba vacío al correr el seeder: completarlo y volver a correr `php artisan db:seed` |
| La SPA muestra error de conexión | Verificar que `php artisan serve` esté corriendo en el puerto 8000 |
| Error con la tabla `jobs` | Poner `QUEUE_CONNECTION=sync` en `.env` |
| Cambios en `.env` que no se aplican | Ejecutar `php artisan config:clear` |
