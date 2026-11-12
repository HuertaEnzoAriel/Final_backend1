<script>
  import './Actividad.css'
  import Aviso from '../../components/aviso/Aviso.svelte'
  import EncabezadoSeccion from '../../components/encabezadoSeccion/EncabezadoSeccion.svelte'
  import ItemActividad from '../../components/itemActividad/ItemActividad.svelte'
  import { pedir } from '../../services/api.js'
  import { ENDPOINTS } from '../../models/api/endpoints.js'
  import { ACTIVIDAD } from '../../models/textos/actividad.js'
  import { marcarVisto } from '../../state/actividad.svelte.js'

  let filtro = $state('all')
  /** @type {import('../models/api/tipos.js').ResumenActividad | null} */
  let resumen = $state(null)
  let estado = $state(
    /** @type {{ items: import('../models/api/tipos.js').ItemActividad[], siguiente: string | null, cargando: boolean, cargandoMas: boolean, error: string | null }} */ ({
      items: [],
      siguiente: null,
      cargando: true,
      cargandoMas: false,
      error: null,
    }),
  )

  // Evita que una respuesta lenta pise a una más nueva al cambiar de filtro.
  let ultimaCarga = 0

  /** @param {string | null} antes */
  function urlActividad(antes) {
    const params = new URLSearchParams({ type: filtro })
    if (antes) params.set('before', antes)
    return `${ENDPOINTS.ACTIVIDAD}?${params}`
  }

  async function cargar() {
    const id = ++ultimaCarga
    estado = { items: [], siguiente: null, cargando: true, cargandoMas: false, error: null }
    try {
      const [feed, res] = await Promise.all([pedir(urlActividad(null)), pedir(ENDPOINTS.ACTIVIDAD_RESUMEN)])
      if (id !== ultimaCarga) return
      resumen = res
      estado = { items: feed.data, siguiente: feed.next_before, cargando: false, cargandoMas: false, error: null }
      // Ya se mostró: los "nuevos" quedan resaltados en esta visita y vistos para la próxima.
      marcarVisto()
    } catch (e) {
      if (id !== ultimaCarga) return
      estado = { items: [], siguiente: null, cargando: false, cargandoMas: false, error: /** @type {Error} */ (e).message }
    }
  }

  async function cargarMas() {
    if (!estado.siguiente || estado.cargandoMas) return
    const id = ultimaCarga
    estado.cargandoMas = true
    try {
      const feed = await pedir(urlActividad(estado.siguiente))
      if (id !== ultimaCarga) return
      estado.items = [...estado.items, ...feed.data]
      estado.siguiente = feed.next_before
    } catch {
      /* queda el botón para reintentar */
    } finally {
      if (id === ultimaCarga) estado.cargandoMas = false
    }
  }

  /** @param {string} valor */
  function elegirFiltro(valor) {
    if (valor === filtro) return
    filtro = valor
    cargar()
  }

  cargar()
</script>

<EncabezadoSeccion titulo={ACTIVIDAD.TITULO} bajada={ACTIVIDAD.BAJADA} />

{#if resumen && (resumen.comments_total || resumen.ratings_total)}
  <dl class="actividad__resumen">
    <div class="actividad__dato">
      <dt>{ACTIVIDAD.RESUMEN_COMENTARIOS}</dt>
      <dd>{resumen.comments_total}</dd>
    </div>
    <div class="actividad__dato">
      <dt>{ACTIVIDAD.RESUMEN_CALIFICACIONES}</dt>
      <dd>{resumen.ratings_total}</dd>
    </div>
    <div class="actividad__dato">
      <dt>{ACTIVIDAD.RESUMEN_PROMEDIO}</dt>
      <dd>{resumen.ratings_total ? resumen.average.toFixed(1) : '—'}</dd>
    </div>
  </dl>
{/if}

<div class="actividad__filtros" role="tablist">
  {#each ACTIVIDAD.FILTROS as f (f.valor)}
    <button
      class="actividad__filtro"
      class:actividad__filtro--activo={filtro === f.valor}
      type="button"
      role="tab"
      aria-selected={filtro === f.valor}
      onclick={() => elegirFiltro(f.valor)}
    >
      {f.texto}
    </button>
  {/each}
</div>

{#if estado.cargando}
  <Aviso texto={ACTIVIDAD.CARGANDO} />
{:else if estado.error}
  <Aviso tipo="error" texto="{ACTIVIDAD.ERROR_CARGA} ({estado.error})" alReintentar={cargar} />
{:else if estado.items.length}
  <ul class="actividad__lista">
    {#each estado.items as item (item.type + item.id)}
      <ItemActividad {item} />
    {/each}
  </ul>
  {#if estado.siguiente}
    <button class="actividad__mas" type="button" disabled={estado.cargandoMas} onclick={cargarMas}>
      {estado.cargandoMas ? ACTIVIDAD.CARGANDO : ACTIVIDAD.CARGAR_MAS}
    </button>
  {/if}
{:else}
  <Aviso texto={ACTIVIDAD.SIN_ACTIVIDAD} />
{/if}
