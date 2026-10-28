<script>
  import './Inicio.css'
  import Aviso from '../lib/aviso/Aviso.svelte'
  import PostDestacado from '../lib/postDestacado/PostDestacado.svelte'
  import TarjetaPost from '../lib/tarjetaPost/TarjetaPost.svelte'
  import PanelAcceso from '../lib/panelAcceso/PanelAcceso.svelte'
  import PanelEstadisticas from '../lib/panelEstadisticas/PanelEstadisticas.svelte'
  import ListaTopPosts from '../lib/listaTopPosts/ListaTopPosts.svelte'
  import { pedir } from '../services/api.js'
  import { ENDPOINTS } from '../models/api/endpoints.js'
  import { INICIO } from '../models/textos/inicio.js'

  // Los tres estados: cargando, error y datos (apunte §8.1 y §8.2).
  let estado = $state(
    /** @type {{ datos: import('../models/api/tipos.js').RespuestaInicio | null, cargando: boolean, error: string | null }} */ ({
      datos: null,
      cargando: true,
      error: null,
    }),
  )

  async function cargar() {
    estado = { datos: null, cargando: true, error: null }
    try {
      const datos = await pedir(ENDPOINTS.POSTS)
      estado = { datos, cargando: false, error: null }
    } catch (e) {
      estado = { datos: null, cargando: false, error: /** @type {Error} */ (e).message }
    }
  }

  cargar()

  // El primero va grande arriba; el resto, en la grilla (apunte §4.2).
  let destacado = $derived(estado.datos?.posts[0])
  let resto = $derived(estado.datos?.posts.slice(1) ?? [])
</script>

{#if estado.cargando}
  <Aviso texto={INICIO.CARGANDO} />
{:else if estado.error}
  <Aviso tipo="error" texto="{INICIO.ERROR_CARGA} ({estado.error})" alReintentar={cargar} />
{:else if estado.datos}
  {#if destacado}
    <PostDestacado post={destacado} />
  {:else}
    <Aviso texto={INICIO.SIN_POSTS} />
  {/if}

  <div class="inicio">
    <section class="inicio__ultimos">
      {#if resto.length}
        <header class="inicio__encabezado">
          <h2 class="inicio__titulo">{INICIO.TITULO_ULTIMOS}</h2>
          <span class="inicio__contador">{estado.datos.posts.length} publicados</span>
        </header>
        <div class="inicio__grilla">
          {#each resto as post (post.id)}
            <TarjetaPost {post} />
          {/each}
        </div>
      {/if}
    </section>

    <aside class="inicio__lateral">
      <PanelAcceso />
      <PanelEstadisticas stats={estado.datos.stats} />
      {#if estado.datos.top.length}
        <ListaTopPosts posts={estado.datos.top} />
      {/if}
    </aside>
  </div>
{/if}
