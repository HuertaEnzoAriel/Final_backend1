<script>
  import './Autores.css'
  import Aviso from '../lib/aviso/Aviso.svelte'
  import EncabezadoSeccion from '../lib/encabezadoSeccion/EncabezadoSeccion.svelte'
  import TarjetaAutor from '../lib/tarjetaAutor/TarjetaAutor.svelte'
  import { pedir } from '../services/api.js'
  import { ENDPOINTS } from '../models/api/endpoints.js'
  import { AUTORES } from '../models/textos/autores.js'

  // Los tres estados: cargando, error y datos (apunte §8.1 y §8.2).
  let estado = $state(
    /** @type {{ datos: import('../models/api/tipos.js').RespuestaAutores | null, cargando: boolean, error: string | null }} */ ({
      datos: null,
      cargando: true,
      error: null,
    }),
  )

  async function cargar() {
    estado = { datos: null, cargando: true, error: null }
    try {
      const datos = await pedir(ENDPOINTS.AUTORES)
      estado = { datos, cargando: false, error: null }
    } catch (e) {
      estado = { datos: null, cargando: false, error: /** @type {Error} */ (e).message }
    }
  }

  cargar()
</script>

<EncabezadoSeccion titulo={AUTORES.TITULO} bajada={AUTORES.BAJADA} />

{#if estado.cargando}
  <Aviso texto={AUTORES.CARGANDO} />
{:else if estado.error}
  <Aviso tipo="error" texto="{AUTORES.ERROR_CARGA} ({estado.error})" alReintentar={cargar} />
{:else if estado.datos?.authors.length}
  <div class="autores">
    {#each estado.datos.authors as autor (autor.id)}
      <TarjetaAutor {autor} />
    {/each}
  </div>
{:else}
  <Aviso texto={AUTORES.SIN_AUTORES} />
{/if}
