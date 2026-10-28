<script>
  import './TarjetaPost.css'
  import ImagenPost from '../imagenPost/ImagenPost.svelte'
  import MetaPost from '../metaPost/MetaPost.svelte'
  import { rutaPost } from '../../models/rutas/rutas.js'

  /** @type {{ post: import('../../models/api/tipos.js').PostResumen }} */
  let { post } = $props()
</script>

<article class="tarjeta">
  <a class="tarjeta__imagen" href={'#' + rutaPost(post.id)}>
    <ImagenPost url={post.image_url} titulo={post.title} />
  </a>
  <MetaPost autor={post.author} fecha={post.published_at} />
  <h3 class="tarjeta__titulo">
    <a href={'#' + rutaPost(post.id)}>{post.title}</a>
  </h3>
  <p class="tarjeta__resumen">{post.excerpt}</p>
  <p class="tarjeta__numeros">
    {post.comments_count} comentarios · ★ {post.rating_avg.toFixed(1)}
  </p>
</article>
