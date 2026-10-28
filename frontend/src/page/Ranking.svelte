<script>
  import Aviso from '../lib/aviso/Aviso.svelte'
  import EncabezadoSeccion from '../lib/encabezadoSeccion/EncabezadoSeccion.svelte'
  import FilaRanking from '../lib/filaRanking/FilaRanking.svelte'
  import { pedir } from '../services/api.js'
  import { ENDPOINTS } from '../models/api/endpoints.js'
  import { RANKING } from '../models/textos/ranking.js'

  // Los tres estados: cargando, error y datos (apunte §8.1 y §8.2).
  let estado = $state(
    /** @type {{ datos: import('../models/api/tipos.js').RespuestaRanking | null, cargando: boolean, error: string | null }} */ ({
      datos: null,
      cargando: true,
      error: null,
    }),
  )

  async function cargar() {
    estado = { datos: null, cargando: true, error: null }
    try {
      const datos = await pedir(ENDPOINTS.RANKING)
      estado = { datos, cargando: false, error: null }
    } catch (e) {
      estado = { datos: null, cargando: false, error: /** @type {Error} */ (e).message }
    }
  }

  cargar()
</script>

<EncabezadoSeccion titulo={RANKING.TITULO} bajada={RANKING.BAJADA} />

{#if estado.cargando}
  <Aviso texto={RANKING.CARGANDO} />
{:else if estado.error}
  <Aviso tipo="error" texto="{RANKING.ERROR_CARGA} ({estado.error})" alReintentar={cargar} />
{:else if estado.datos?.posts.length}
  <ol>
    {#each estado.datos.posts as post, i (post.id)}
      <FilaRanking {post} posicion={i + 1} />
    {/each}
  </ol>
{:else}
  <Aviso texto={RANKING.SIN_POSTS} />
{/if}
