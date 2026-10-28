<script>
  import './FilaRanking.css'
  import ImagenPost from '../imagenPost/ImagenPost.svelte'
  import MetaPost from '../metaPost/MetaPost.svelte'
  import { rutaPost } from '../../models/rutas/rutas.js'

  /** @type {{ post: import('../../models/api/tipos.js').PostResumen, posicion: number }} */
  let { post, posicion } = $props()
</script>

<li class="fila-ranking">
  <span class="fila-ranking__numero">{String(posicion).padStart(2, '0')}</span>
  <a class="fila-ranking__imagen" href={'#' + rutaPost(post.id)}>
    <ImagenPost url={post.image_url} titulo={post.title} />
  </a>
  <div class="fila-ranking__cuerpo">
    <h2 class="fila-ranking__titulo">
      <a href={'#' + rutaPost(post.id)}>{post.title}</a>
    </h2>
    <MetaPost autor={post.author} fecha={post.published_at} />
    <p class="fila-ranking__numeros">
      ★ {post.rating_avg.toFixed(1)} · {post.comments_count} comentarios
    </p>
  </div>
</li>
