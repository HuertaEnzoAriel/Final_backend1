<script>
  import './PostDestacado.css'
  import ImagenPost from '../imagenPost/ImagenPost.svelte'
  import MetaPost from '../metaPost/MetaPost.svelte'
  import Boton from '../boton/Boton.svelte'
  import { rutaPost } from '../../models/rutas/rutas.js'
  import { INICIO } from '../../models/textos/inicio.js'

  /** @type {{ post: import('../../models/api/tipos.js').PostResumen }} */
  let { post } = $props()
</script>

<article class="destacado">
  <a class="destacado__imagen" href={'#' + rutaPost(post.id)}>
    <ImagenPost url={post.image_url} titulo={post.title} />
  </a>

  <div class="destacado__texto">
    <p class="destacado__etiqueta">{INICIO.ETIQUETA_DESTACADO}</p>
    <h1 class="destacado__titulo">
      <a href={'#' + rutaPost(post.id)}>{post.title}</a>
    </h1>
    <p class="destacado__resumen">{post.excerpt}</p>
    <MetaPost autor={post.author} fecha={post.published_at} />
    <div>
      <Boton href={'#' + rutaPost(post.id)}>Leer nota</Boton>
    </div>
  </div>
</article>
