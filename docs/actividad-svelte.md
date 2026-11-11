# Cómo funciona la pantalla de Actividad (solo Svelte)

Esta guía explica el front de la bandeja de **Actividad**: una pantalla donde el usuario ve comentarios, calificaciones y respuestas, con un contador de novedades en el menú. Usa **Svelte 5 con runes** (`$state`, `$derived`, `$effect`, `$props`).

## 1. Mapa de archivos

| Archivo | Rol |
|---|---|
| `models/rutas/rutas.js` | Define la ruta `ACTIVIDAD: '/actividad'` |
| `components/router/Router.svelte` | Decide qué página mostrar según la URL |
| `models/navegacion/enlaces.js` | Agrega el link "Actividad" al menú de cuenta |
| `state/actividad.svelte.js` | Estado **compartido** (el contador de no vistos) y las funciones que lo actualizan |
| `components/menuUsuario/MenuUsuario.svelte` | Muestra el contador (badge) y lo mantiene actualizado |
| `page/Actividad.svelte` | La página: filtros, lista, "Cargar más" |
| `components/itemActividad/ItemActividad.svelte` | Un renglón de la lista |
| `models/textos/actividad.js` | Todos los textos de la pantalla |
| `models/api/endpoints.js` / `tipos.js` | URLs de la API y forma de los datos (JSDoc) |

Convención del proyecto: cada componente o página tiene su `.css` al lado, que empieza con `@reference "../App.css";` y usa `@apply` de Tailwind con clases tipo BEM (`item-actividad__fecha`).

## 2. Flujo de rutas

La app usa un router casero basado en el **hash** de la URL (lo que viene después del `#`). No hay librería de rutas.

```
Usuario hace clic en "Actividad" (menú)
        │
        ▼
<a href="#/actividad">                 ← enlace normal, solo cambia el hash
        │
        ▼
window.location.hash = "#/actividad"   ← el navegador dispara el evento hashchange
        │
        ▼
Router.svelte:  <svelte:window onhashchange={() => (hash = window.location.hash)} />
        │         actualiza el $state "hash"
        ▼
let actual = $derived.by(...)          ← se recalcula solo porque "hash" cambió
        │         recorre el arreglo "paginas" y prueba coincidir(patron, ruta)
        ▼
{ ruta: RUTAS.ACTIVIDAD, componente: Actividad }   ← coincide
        │
        ▼
<actual.componente params={actual.params} />       ← Svelte monta Actividad.svelte
```

Puntos clave:

- **`rutas.js`** es la única fuente de verdad de las URLs. El menú, el Router y los links usan `RUTAS.ACTIVIDAD`, nunca el texto `'/actividad'` escrito a mano.
- **`enlaces.js`**: el link "Actividad" es `acceso: ACCESO.USUARIO`, así que aparece solo con sesión iniciada. `puedeVer(enlace, usuario)` filtra la lista y `MenuUsuario` la dibuja con `{#each}`.
- **`Router.svelte`**: el arreglo `paginas` es una tabla `{ ruta, componente }`. Para sumar una pantalla se agrega una fila (así se registró `Actividad`).
- **`coincidir`**: compara patrones con parámetros. `'/posts/:id'` contra `'/posts/5'` devuelve `{ id: '5' }`. Esa es la forma en que el Router entrega parámetros a una página.
- **Links a otros posts**: cada ítem de actividad enlaza con `href={'#' + rutaPost(item.post_id)}`. `rutaPost(5)` devuelve `'/posts/5'`, y de ahí el Router monta `Posts.svelte`.
- `<actual.componente ... />` es un componente **dinámico**: la variable guarda el componente y Svelte lo renderiza.

## 3. Estado compartido: `state/actividad.svelte.js`

```js
export const actividad = $state({ noVistos: 0 })
```

- La extensión **`.svelte.js`** es obligatoria: sin ella el compilador no procesa los runes fuera de un `.svelte`.
- Es un objeto reactivo exportado. Cualquier componente que lo importe y lea `actividad.noVistos` se actualiza solo cuando cambia.
- Se **muta** (`actividad.noVistos = 3`), no se reasigna. Un `export let` reasignado no sería reactivo para quien lo importa; un objeto con propiedades sí. Mismo patrón que `sesion` en `state/sesion.svelte.js`.
- Las dos funciones del archivo:
  - `refrescarNoVistos()`: pide el resumen a la API y guarda `unread` en `actividad.noVistos`.
  - `marcarVisto()`: avisa a la API que ya viste todo y pone el contador en 0.
- Ambas tienen `try/catch` que **ignora el error**: el contador es un adorno y no debe romper la app si falla.

## 4. El badge del menú: `MenuUsuario.svelte`

```js
let recibeActividad = $derived(!!sesion.usuario)
$effect(() => {
  if (recibeActividad) refrescarNoVistos()
  else actividad.noVistos = 0
})
```

- **`$derived`**: valor calculado que se recalcula solo. Acá es `true` mientras haya sesión.
- **`$effect`**: código que corre después de montar el componente y de nuevo cada vez que cambia algo que leyó adentro. Al iniciar sesión se consulta el contador; al cerrarla, se vuelve a 0.
- Detalle sutil: `refrescarNoVistos()` lee `sesion.token` antes de su primer `await`, y eso ocurre dentro del efecto, así que Svelte también lo toma como dependencia.
- **`<svelte:window onfocus={...} />`**: escucha eventos de la ventana sin `addEventListener` manual. Cuando volvés a la pestaña, se refresca el contador. No hay consultas periódicas.
- **Render condicional** con `{#if actividad.noVistos > 0}`: el badge solo existe en el DOM si hay novedades. Se muestra `9+` si pasa de 9.
- El mismo contador aparece al lado del link "Actividad" dentro del menú, con otro `{#if}` que compara `enlace.ruta === RUTAS.ACTIVIDAD`.

## 5. La página: `page/Actividad.svelte`

### 5.1 Estado local

```js
let filtro = $state('all')
let resumen = $state(null)
let estado = $state({ items: [], siguiente: null, cargando: true, cargandoMas: false, error: null })
```

El patrón de los **tres estados** (cargando / error / datos) es el mismo que usa `Ranking.svelte`. Todo lo de la lista va junto en un objeto `estado`.

Dos formas de actualizarlo, y conviene distinguirlas:

- **Reasignar el objeto entero** (`estado = { ... }`): se usa al empezar una carga desde cero.
- **Mutar una propiedad** (`estado.items = [...estado.items, ...nuevos]`): se usa al "cargar más". `$state` hace los objetos reactivos en profundidad (usa proxies), por eso funciona.

### 5.2 Flujo de la carga

```
cargar()  ← se llama una vez al crear el componente (línea suelta al final del <script>)
  │
  ├─ estado = { cargando: true, ... }        → la vista muestra <Aviso "Cargando…">
  ├─ await Promise.all([ feed, resumen ])    → las dos consultas corren en paralelo
  ├─ estado = { items, siguiente, cargando: false }   → la vista muestra la lista
  └─ marcarVisto()                           → pone el contador en 0
```

- `marcarVisto()` se llama **después** de recibir el feed. Los ítems ya traen su marca `is_new`, así que siguen resaltados en esta visita, y en la próxima ya no.
- Si falla algo, se cae al `catch` y `estado.error` muestra el `<Aviso tipo="error" alReintentar={cargar}>`.

### 5.3 Evitar que una respuesta vieja pise a una nueva

```js
let ultimaCarga = 0
async function cargar() {
  const id = ++ultimaCarga
  ...
  const [feed, res] = await Promise.all(...)
  if (id !== ultimaCarga) return   // llegó tarde: ya hay una carga más nueva
```

Si el usuario cambia de filtro rápido, las respuestas pueden llegar en desorden. Cada carga se anota con un número y solo la última puede escribir el resultado. `ultimaCarga` es un `let` común (no `$state`) porque la vista no lo necesita: no hay nada que redibujar cuando cambia.

### 5.4 Filtros

```svelte
{#each ACTIVIDAD.FILTROS as f (f.valor)}
  <button class:actividad__filtro--activo={filtro === f.valor} ... onclick={() => elegirFiltro(f.valor)}>
```

- Los filtros salen de un arreglo en `textos/actividad.js`, así que agregar uno nuevo (como "Respuestas") es sumar una línea.
- **`class:nombre={condicion}`**: agrega esa clase CSS solo cuando la condición es verdadera.
- **`(f.valor)`** en el `{#each}` es la **clave**: le dice a Svelte cómo identificar cada elemento al reordenar o actualizar la lista.
- Los eventos son atributos normales: `onclick={...}`, no `on:click` (eso era Svelte 4).
- `elegirFiltro` cambia `filtro` y llama a `cargar()` otra vez con el nuevo valor.

### 5.5 Paginación ("Cargar más")

La API devuelve `next_before`. Si es `null`, no hay más. Si trae un valor, se muestra el botón y al hacer clic se pide la siguiente tanda con ese valor, que se **agrega al final** de `estado.items`. El botón queda deshabilitado mientras carga (`disabled={estado.cargandoMas}`) para evitar doble clic.

### 5.6 La vista

```svelte
{#if estado.cargando}        <Aviso ... />
{:else if estado.error}      <Aviso tipo="error" ... />
{:else if estado.items.length}  <ul> {#each ...} <ItemActividad {item} /> {/each} </ul>
{:else}                      <Aviso texto={ACTIVIDAD.SIN_ACTIVIDAD} />
{/if}
```

Es una cadena de condiciones donde **solo se dibuja un bloque**. `{item}` es la forma corta de `item={item}`.

## 6. El renglón: `ItemActividad.svelte`

```js
let { item } = $props()
let fecha = $derived(new Date(item.created_at).toLocaleString('es-AR', { ... }))
```

- **`$props()`**: así un componente recibe datos de su padre. Acá recibe un solo `item`.
- **`$derived`** formatea la fecha: se recalcula solo si `item` cambia.
- El tipo del ítem (`comment`, `rating`, `reply`) decide qué texto y qué bloques se dibujan, con `{#if}` / `{:else if}`:
  - comentario: "*X* comentó en *Post*" + texto del comentario
  - calificación: "*X* calificó ★★★★☆ en *Post*"
  - respuesta: "*X* respondió tu comentario en *Post*" + la respuesta + una cita de tu comentario
- `class:item-actividad--nuevo={item.is_new}` resalta lo no visto, y otro `{#if item.is_new}` pone la etiqueta "Nuevo".
- Las estrellas se arman con `'★'.repeat(score) + '☆'.repeat(5 - score)`.
- Todo el ítem es un `<a href="#/posts/ID">`, entonces hacer clic dispara el flujo de rutas de la sección 2.

## 7. Resumen del flujo completo

```
Inicio de sesión
   └─ MenuUsuario ($effect) → refrescarNoVistos() → actividad.noVistos = N
                                                     └─ badge visible en el ícono y en el link

Clic en "Actividad"  →  cambia el hash  →  Router monta Actividad.svelte
   └─ cargar(): feed + resumen en paralelo → lista con ítems resaltados
   └─ marcarVisto() → actividad.noVistos = 0 → el badge desaparece solo

Cambiar filtro  →  elegirFiltro()  →  cargar() de nuevo
Cargar más      →  cargarMas()     →  se agregan ítems al final

Clic en un ítem →  cambia el hash a #/posts/ID  →  Router monta Posts.svelte
```

## 8. Conceptos de Svelte que aparecen acá

| Concepto | Dónde verlo |
|---|---|
| `$state` | `Actividad.svelte` (`filtro`, `estado`), `actividad.svelte.js` |
| `$derived` | `ItemActividad` (`fecha`), `MenuUsuario` (`recibeActividad`) |
| `$effect` | `MenuUsuario` (consultar el contador) |
| `$props` | `ItemActividad` (`let { item } = $props()`) |
| Estado compartido en `.svelte.js` | `actividad.svelte.js`, `sesion.svelte.js` |
| `{#if}` / `{:else if}` / `{#each}` con clave | `Actividad.svelte`, `ItemActividad.svelte` |
| `class:` | filtros activos, ítems nuevos |
| Eventos como atributos (`onclick`) | filtros, "Cargar más" |
| `<svelte:window>` | `Router.svelte` (`onhashchange`), `MenuUsuario.svelte` (`onfocus`) |
| Componente dinámico | `<actual.componente />` en `Router.svelte` |

## 9. Para practicar

1. Agregá un filtro nuevo en `ACTIVIDAD.FILTROS` y mirá que aparezca sin tocar el HTML.
2. Probá quitar la clave `(f.valor)` del `{#each}` y pensá qué cambia.
3. Sacá `if (id !== ultimaCarga) return` y cambiá de filtro rápido con la red lenta (DevTools → Network → Slow 3G): vas a ver el problema que evita.
4. Agregá una ruta nueva (por ejemplo `#/prueba`) siguiendo la sección 2.
