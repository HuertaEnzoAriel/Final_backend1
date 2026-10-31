<script>
  import './Posts.css'
  import Aviso from '../components/aviso/Aviso.svelte'
  import Boton from '../components/boton/Boton.svelte'
  import ImagenPost from '../components/imagenPost/ImagenPost.svelte'
  import MetaPost from '../components/metaPost/MetaPost.svelte'
  import { pedir } from '../services/api.js'
  import { ENDPOINTS, conId } from '../models/api/endpoints.js'
  import { RUTAS } from '../models/rutas/rutas.js'
  import { POST } from '../models/textos/post.js'
  import { sesion } from '../state/sesion.svelte.js'

  // El Router pasa los parámetros de la URL: #/posts/5 → { id: '5' }.
  /** @type {{ params: { id: string } }} */
  let { params } = $props()

  // Los tres estados: cargando, error y datos (apunte §8.1 y §8.2).
  let estado = $state(
    /** @type {{ datos: import('../models/api/tipos.js').RespuestaPost | null, cargando: boolean, error: string | null }} */ ({
      datos: null,
      cargando: true,
      error: null,
    }),
  )

  async function cargar() {
    estado = { datos: null, cargando: true, error: null }
    try {
      const datos = await pedir(conId(ENDPOINTS.POST, params.id))
      estado = { datos, cargando: false, error: null }
    } catch (e) {
      estado = { datos: null, cargando: false, error: /** @type {Error} */ (e).message }
    }
  }

  // Vuelve a pedir los datos sin mostrar "Cargando…" (después de comentar o calificar).
  async function refrescar() {
    estado.datos = await pedir(conId(ENDPOINTS.POST, params.id))
  }

  // Corre de nuevo si cambia params.id: de #/posts/1 a #/posts/2 Svelte reutiliza el componente.
  $effect(() => {
    cargar()
  })

  let post = $derived(estado.datos?.post)
  let comentarios = $derived(estado.datos?.comments ?? [])

  // Calificación
  let errorCalificacion = $state('')

  /** @param {number} score */
  async function calificar(score) {
    errorCalificacion = ''
    try {
      await pedir(conId(ENDPOINTS.CALIFICACIONES, params.id), { metodo: 'POST', datos: { score } })
      await refrescar()
    } catch {
      errorCalificacion = POST.ERROR_ENVIO
    }
  }

  // Comentario
  let comentario = $state('')
  let enviando = $state(false)
  let errorComentario = $state('')

  /** @param {SubmitEvent} event */
  async function comentar(event) {
    event.preventDefault()
    enviando = true
    errorComentario = ''
    try {
      await pedir(conId(ENDPOINTS.COMENTARIOS, params.id), { metodo: 'POST', datos: { content: comentario } })
      comentario = ''
      await refrescar()
    } catch (e) {
      // 422: muestra el error de Laravel; si no, un mensaje general.
      errorComentario = /** @type {{ errores?: Record<string, string[]> }} */ (e).errores?.content?.[0] ?? POST.ERROR_ENVIO
    } finally {
      enviando = false
    }
  }
</script>

{#if estado.cargando}
  <Aviso texto={POST.CARGANDO} />
{:else if estado.error}
  <Aviso tipo="error" texto="{POST.ERROR_CARGA} ({estado.error})" alReintentar={cargar} />
{:else if post}
  <article class="post">
    <a class="post__volver" href={'#' + RUTAS.INICIO}>{POST.VOLVER}</a>
    <h1 class="post__titulo">{post.title}</h1>
    <MetaPost autor={post.author} fecha={post.published_at} />

    <div class="post__imagen">
      <ImagenPost url={post.image_url} titulo={post.title} />
    </div>

    <p class="post__contenido">{post.content}</p>
    <p class="post__numeros">
      ★ {post.rating_avg.toFixed(1)} · {post.ratings_count} votos · {post.comments_count} comentarios
    </p>
  </article>

  <section class="post__seccion">
    <h2 class="post__subtitulo">{POST.TITULO_CALIFICAR}</h2>
    {#if sesion.usuario}
      <div class="post__estrellas">
        {#each [1, 2, 3, 4, 5] as n (n)}
          <button
            type="button"
            class="post__estrella"
            class:post__estrella--activa={n <= (estado.datos?.my_rating ?? 0)}
            aria-label="{n} de 5"
            onclick={() => calificar(n)}>★</button
          >
        {/each}
      </div>
      {#if estado.datos?.my_rating}
        <p class="post__nota">{POST.TU_NOTA}: {estado.datos.my_rating}/5</p>
      {/if}
      {#if errorCalificacion}
        <p class="post__error">{errorCalificacion}</p>
      {/if}
    {:else}
      <p class="post__invitado">
        {POST.INVITADO} <a href={'#' + RUTAS.LOGIN}>{POST.ENLACE_LOGIN}</a>.
      </p>
    {/if}
  </section>

  <section class="post__seccion">
    <h2 class="post__subtitulo">{POST.TITULO_COMENTARIOS} ({comentarios.length})</h2>

    {#if sesion.usuario}
      <form class="post__form" onsubmit={comentar}>
        <textarea
          class="post__textarea"
          rows="4"
          minlength="8"
          maxlength="1000"
          required
          placeholder={POST.PLACEHOLDER}
          bind:value={comentario}
        ></textarea>
        {#if errorComentario}
          <p class="post__error">{errorComentario}</p>
        {/if}
        <div>
          <Boton type="submit" disabled={enviando}>{enviando ? POST.ENVIANDO : POST.BOTON_COMENTAR}</Boton>
        </div>
      </form>
    {/if}

    <div class="post__comentarios">
      {#each comentarios as c (c.id)}
        <article class="post__comentario">
          <MetaPost autor={c.author} fecha={c.created_at} />
          <p>{c.content}</p>
        </article>
      {:else}
        <Aviso texto={POST.SIN_COMENTARIOS} />
      {/each}
    </div>
  </section>
{/if}
