<script>
  import './Dashboard.css'
  import Aviso from '../../components/aviso/Aviso.svelte'
  import Boton from '../../components/boton/Boton.svelte'
  import EncabezadoSeccion from '../../components/encabezadoSeccion/EncabezadoSeccion.svelte'
  import { pedir } from '../../services/api.js'
  import { ENDPOINTS, conId } from '../../models/api/endpoints.js'
  import { RUTAS, rutaPost } from '../../models/rutas/rutas.js'
  import { ROLES_PUBLICAN } from '../../models/roles/roles.js'
  import { DASHBOARD } from '../../models/textos/dashboard.js'
  import { COMUN } from '../../models/textos/comun.js'
  import { sesion } from '../../state/sesion.svelte.js'

  // Solo con sesión: un invitado va al login.
  if (!sesion.usuario) window.location.hash = RUTAS.LOGIN

  let estado = $state(
    /** @type {{ datos: any | null, cargando: boolean, error: string | null }} */ ({
      datos: null,
      cargando: !!sesion.usuario,
      error: null,
    }),
  )
  let enviando = $state(false)
  let errorAccion = $state('')
  /** Post que se está eliminando. */
  let eliminando = $state(/** @type {number | null} */ (null))

  // El rol puede haber cambiado desde que se guardó la sesión: se lee de la sesión actual.
  let puedePublicar = $derived(!!sesion.usuario && ROLES_PUBLICAN.includes(sesion.usuario.role))

  async function cargar() {
    estado = { datos: null, cargando: true, error: null }
    try {
      estado = { datos: await pedir(ENDPOINTS.MIS_POSTS), cargando: false, error: null }
    } catch (e) {
      estado = { datos: null, cargando: false, error: /** @type {Error} */ (e).message }
    }
  }

  async function solicitarEditor() {
    enviando = true
    errorAccion = ''
    try {
      await pedir(ENDPOINTS.SOLICITAR_EDITOR, { metodo: 'POST' })
      if (estado.datos) estado.datos.requested_editor = true
    } catch (e) {
      errorAccion = /** @type {Error} */ (e).message || COMUN.ERROR_ACCION
    } finally {
      enviando = false
    }
  }

  /** @param {{ id: number }} post */
  async function eliminar(post) {
    if (!confirm(DASHBOARD.CONFIRMAR_ELIMINAR)) return
    eliminando = post.id
    errorAccion = ''
    try {
      await pedir(conId(ENDPOINTS.POST, post.id), { metodo: 'DELETE' })
      // Se recarga para que las métricas reflejen el post eliminado.
      estado.datos = await pedir(ENDPOINTS.MIS_POSTS)
    } catch (e) {
      errorAccion = /** @type {Error} */ (e).message || COMUN.ERROR_ACCION
    } finally {
      eliminando = null
    }
  }

  if (sesion.usuario) cargar()
</script>

<EncabezadoSeccion titulo={DASHBOARD.TITULO} bajada={DASHBOARD.BAJADA} />

{#if estado.cargando}
  <Aviso texto={DASHBOARD.CARGANDO} />
{:else if estado.error}
  <Aviso tipo="error" texto="{DASHBOARD.ERROR_CARGA} ({estado.error})" alReintentar={cargar} />
{:else if estado.datos}
  {@const { posts, stats } = estado.datos}

  <dl class="dashboard__metricas">
    <div class="dashboard__dato"><dt>{DASHBOARD.METRICAS.TOTAL}</dt><dd>{stats.total_posts}</dd></div>
    <div class="dashboard__dato"><dt>{DASHBOARD.METRICAS.PUBLICADOS}</dt><dd>{stats.published_posts}</dd></div>
    <div class="dashboard__dato"><dt>{DASHBOARD.METRICAS.COMENTARIOS}</dt><dd>{stats.total_comments}</dd></div>
    <div class="dashboard__dato">
      <dt>{DASHBOARD.METRICAS.PROMEDIO}</dt>
      <dd>{stats.average_rating ? stats.average_rating.toFixed(1) : '—'}</dd>
    </div>
  </dl>

  <div class="dashboard__acciones">
    {#if puedePublicar}
      <Boton href={'#' + RUTAS.PUBLICAR}>{DASHBOARD.NUEVO_POST}</Boton>
    {:else if estado.datos.requested_editor}
      <Boton variante="secundario" disabled>{DASHBOARD.SOLICITADO}</Boton>
    {:else}
      <Boton disabled={enviando} onclick={solicitarEditor}>{enviando ? COMUN.ENVIANDO : DASHBOARD.SOLICITAR}</Boton>
      <p class="dashboard__ayuda">{DASHBOARD.AYUDA_SOLICITUD}</p>
    {/if}
  </div>

  {#if errorAccion}<p class="dashboard__error" role="alert">{errorAccion}</p>{/if}

  {#if posts.length}
    <ul class="dashboard__lista">
      {#each posts as post (post.id)}
        <li class="dashboard__post">
          <div>
            <h2 class="dashboard__titulo">{post.title}</h2>
            <p class="dashboard__meta">
              <span class="dashboard__estado dashboard__estado--{post.status}">{/** @type {Record<string, string>} */ (DASHBOARD.ESTADOS)[post.status]}</span>
              <span>{DASHBOARD.COLUMNAS.COMENTARIOS}: {post.comments_count}</span>
              <span>{DASHBOARD.COLUMNAS.RATING}: {post.rating_avg ? post.rating_avg.toFixed(1) : '—'}</span>
            </p>
            {#if post.status === 'rejected' && post.rejection_reason}
              <p class="dashboard__motivo"><strong>{DASHBOARD.MOTIVO}:</strong> {post.rejection_reason}</p>
            {/if}
          </div>
          <div class="dashboard__botones">
            {#if post.status === 'published'}
              <Boton variante="secundario" href={'#' + rutaPost(post.id)}>{DASHBOARD.ABRIR}</Boton>
            {/if}
            <Boton variante="secundario" disabled={eliminando === post.id} onclick={() => eliminar(post)}>{DASHBOARD.ELIMINAR}</Boton>
          </div>
        </li>
      {/each}
    </ul>
  {:else}
    <Aviso texto={puedePublicar ? DASHBOARD.SIN_POSTS_PUBLICAR : DASHBOARD.SIN_POSTS} />
  {/if}
{/if}
