<script>
  import './Moderacion.css'
  import Aviso from '../../components/aviso/Aviso.svelte'
  import Boton from '../../components/boton/Boton.svelte'
  import EncabezadoSeccion from '../../components/encabezadoSeccion/EncabezadoSeccion.svelte'
  import ImagenPost from '../../components/imagenPost/ImagenPost.svelte'
  import MetaPost from '../../components/metaPost/MetaPost.svelte'
  import { pedir } from '../../services/api.js'
  import { ENDPOINTS, conId } from '../../models/api/endpoints.js'
  import { RUTAS } from '../../models/rutas/rutas.js'
  import { ROLES, ETIQUETAS_ROL } from '../../models/roles/roles.js'
  import { MODERACION } from '../../models/textos/moderacion.js'
  import { sesion } from '../../state/sesion.svelte.js'

  // Solo admin: un invitado va al login.
  if (!sesion.usuario) window.location.hash = RUTAS.LOGIN
  const puede = sesion.usuario?.role === ROLES.ADMIN

  let estado = $state(
    /** @type {{ posts: any[], cargando: boolean, error: string | null }} */ ({ posts: [], cargando: puede, error: null }),
  )
  /** Post que se está rechazando (muestra el campo de motivo). */
  let rechazando = $state(/** @type {number | null} */ (null))
  let motivo = $state('')
  /** Post con una acción en curso. */
  let ocupado = $state(/** @type {number | null} */ (null))
  /** Post sobre el que falló la última acción, y el mensaje. */
  let errorEn = $state(/** @type {number | null} */ (null))
  let errorAccion = $state('')

  async function cargar() {
    estado = { posts: [], cargando: true, error: null }
    try {
      const datos = await pedir(ENDPOINTS.PENDIENTES)
      estado = { posts: datos.posts, cargando: false, error: null }
    } catch (e) {
      estado = { posts: [], cargando: false, error: /** @type {Error} */ (e).message }
    }
  }

  /**
   * @param {number} id
   * @param {string} endpoint
   * @param {object | null} datos
   */
  async function resolver(id, endpoint, datos) {
    ocupado = id
    errorEn = null
    try {
      await pedir(conId(endpoint, id), { metodo: 'POST', datos })
      estado.posts = estado.posts.filter((p) => p.id !== id)
      rechazando = null
      motivo = ''
    } catch (e) {
      const err = /** @type {{ message: string, errores?: Record<string, string[]> }} */ (e)
      errorEn = id
      errorAccion = err.errores?.rejection_reason?.[0] ?? err.message ?? MODERACION.ERROR_ACCION
    } finally {
      ocupado = null
    }
  }

  /** @param {number} id */
  function empezarRechazo(id) {
    rechazando = id
    motivo = ''
    errorEn = null
  }

  // --- Usuarios y roles ---
  let usuarios = $state(
    /** @type {{ lista: any[], cargando: boolean, error: string | null }} */ ({ lista: [], cargando: puede, error: null }),
  )
  /** Usuario cuyo rol se está guardando, y el que falló con su mensaje. */
  let guardando = $state(/** @type {number | null} */ (null))
  let errorRol = $state(/** @type {{ id: number, texto: string } | null} */ (null))

  async function cargarUsuarios() {
    usuarios = { lista: [], cargando: true, error: null }
    try {
      const datos = await pedir(ENDPOINTS.USUARIOS_ADMIN)
      usuarios = { lista: datos.users, cargando: false, error: null }
    } catch (e) {
      usuarios = { lista: [], cargando: false, error: /** @type {Error} */ (e).message }
    }
  }

  /**
   * @param {any} usuario
   * @param {string} rol
   */
  async function cambiarRol(usuario, rol) {
    if (rol === usuario.role) return
    guardando = usuario.id
    errorRol = null
    try {
      await pedir(conId(ENDPOINTS.ROL_USUARIO, usuario.id), { metodo: 'POST', datos: { role: rol } })
      usuario.role = rol
      usuario.requested_editor = false
    } catch (e) {
      errorRol = { id: usuario.id, texto: /** @type {Error} */ (e).message || MODERACION.ERROR_ACCION }
      await cargarUsuarios()
    } finally {
      guardando = null
    }
  }

  // --- Pestañas y filtros ---
  let pestana = $state('posts')
  let busqueda = $state('')
  let filtro = $state('todos')

  const solicitudes = $derived(usuarios.lista.filter((u) => u.requested_editor).length)
  const filtrados = $derived(
    usuarios.lista.filter((u) => {
      const q = busqueda.trim().toLowerCase()
      if (q && !u.name.toLowerCase().includes(q) && !u.email.toLowerCase().includes(q)) return false
      if (filtro === 'solicitudes') return u.requested_editor
      if (filtro !== 'todos') return u.role === filtro
      return true
    }),
  )
  const filtros = $derived([
    { valor: 'todos', texto: MODERACION.FILTRO_TODOS },
    { valor: 'solicitudes', texto: `${MODERACION.FILTRO_SOLICITUDES} (${solicitudes})` },
    ...Object.entries(ETIQUETAS_ROL).map(([valor, texto]) => ({ valor, texto })),
  ])

  if (puede) {
    cargar()
    cargarUsuarios()
  }
</script>

<EncabezadoSeccion titulo={MODERACION.TITULO} bajada={MODERACION.BAJADA} />

{#if !puede}
  <Aviso tipo="error" texto={MODERACION.SIN_PERMISO} />
{:else}
  <div class="moderacion__pestanas" role="tablist">
    <button role="tab" aria-selected={pestana === 'posts'} class="moderacion__pestana" class:moderacion__pestana--activa={pestana === 'posts'} onclick={() => (pestana = 'posts')}>
      {MODERACION.PESTANA_POSTS}
      {#if estado.posts.length}<span class="moderacion__contador">{estado.posts.length}</span>{/if}
    </button>
    <button role="tab" aria-selected={pestana === 'usuarios'} class="moderacion__pestana" class:moderacion__pestana--activa={pestana === 'usuarios'} onclick={() => (pestana = 'usuarios')}>
      {MODERACION.PESTANA_USUARIOS}
      {#if solicitudes}<span class="moderacion__contador">{solicitudes}</span>{/if}
    </button>
  </div>

  {#if pestana === 'posts'}
    {#if estado.cargando}
      <Aviso texto={MODERACION.CARGANDO} />
    {:else if estado.error}
      <Aviso tipo="error" texto="{MODERACION.ERROR_CARGA} ({estado.error})" alReintentar={cargar} />
    {:else if estado.posts.length}
      <ul class="moderacion__lista">
        {#each estado.posts as post (post.id)}
          <li class="moderacion__item">
            {#if post.image_url}
              <div class="moderacion__imagen"><ImagenPost url={post.image_url} titulo={post.title} /></div>
            {/if}
            <h2 class="moderacion__titulo">{post.title}</h2>
            <MetaPost autor={post.author} fecha={post.created_at} />
            <p class="moderacion__contenido">{post.content}</p>

            {#if rechazando === post.id}
              <textarea class="moderacion__motivo" maxlength="500" placeholder={MODERACION.MOTIVO} bind:value={motivo}></textarea>
            {/if}
            {#if errorEn === post.id}<p class="moderacion__error">{errorAccion}</p>{/if}

            <div class="moderacion__acciones">
              {#if rechazando === post.id}
                <Boton disabled={ocupado === post.id || !motivo.trim()} onclick={() => resolver(post.id, ENDPOINTS.RECHAZAR, { rejection_reason: motivo.trim() })}>
                  {MODERACION.CONFIRMAR_RECHAZO}
                </Boton>
                <Boton variante="secundario" onclick={() => (rechazando = null)}>{MODERACION.CANCELAR}</Boton>
              {:else}
                <Boton disabled={ocupado === post.id} onclick={() => resolver(post.id, ENDPOINTS.APROBAR, null)}>{MODERACION.APROBAR}</Boton>
                <Boton variante="secundario" disabled={ocupado === post.id} onclick={() => empezarRechazo(post.id)}>{MODERACION.RECHAZAR}</Boton>
              {/if}
            </div>
          </li>
        {/each}
      </ul>
    {:else}
      <Aviso texto={MODERACION.SIN_PENDIENTES} />
    {/if}
  {:else}
    <p class="moderacion__bajada">{MODERACION.BAJADA_USUARIOS}</p>

    {#if usuarios.cargando}
      <Aviso texto={MODERACION.CARGANDO_USUARIOS} />
    {:else if usuarios.error}
      <Aviso tipo="error" texto="{MODERACION.ERROR_USUARIOS} ({usuarios.error})" alReintentar={cargarUsuarios} />
    {:else}
      <div class="moderacion__herramientas">
        <input class="moderacion__buscar" type="search" placeholder={MODERACION.BUSCAR} aria-label={MODERACION.BUSCAR} bind:value={busqueda} />
        <div class="moderacion__filtros">
          {#each filtros as f (f.valor)}
            <button class="moderacion__filtro" class:moderacion__filtro--activo={filtro === f.valor} onclick={() => (filtro = f.valor)}>{f.texto}</button>
          {/each}
        </div>
      </div>

      {#if filtrados.length}
        <ul class="moderacion__lista moderacion__lista--compacta">
          {#each filtrados as u (u.id)}
            <li class="moderacion__usuario" class:moderacion__usuario--solicita={u.requested_editor}>
              <div class="moderacion__datos">
                <p class="moderacion__nombre">
                  {u.name}
                  {#if u.requested_editor}<span class="moderacion__etiqueta">{MODERACION.SOLICITO}</span>{/if}
                </p>
                <p class="moderacion__correo">{u.email}</p>
                {#if errorRol && errorRol.id === u.id}<p class="moderacion__error">{errorRol.texto}</p>{/if}
              </div>
              {#if u.id === sesion.usuario?.id}
                <span class="moderacion__correo">{MODERACION.TU_CUENTA} · {ETIQUETAS_ROL[u.role]}</span>
              {:else}
                <select
                  class="moderacion__rol"
                  aria-label="Rol de {u.name}"
                  disabled={guardando === u.id}
                  value={u.role}
                  onchange={(e) => cambiarRol(u, e.currentTarget.value)}
                >
                  {#each Object.entries(ETIQUETAS_ROL) as [valor, texto] (valor)}
                    <option value={valor}>{texto}</option>
                  {/each}
                </select>
              {/if}
            </li>
          {/each}
        </ul>
      {:else}
        <Aviso texto={MODERACION.SIN_USUARIOS} />
      {/if}
    {/if}
  {/if}
{/if}
