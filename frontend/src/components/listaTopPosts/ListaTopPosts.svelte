<script>
  import './ListaTopPosts.css'
  import Panel from '../panel/Panel.svelte'
  import { rutaPost } from '../../models/rutas/rutas.js'

  /** @type {{ posts: import('../../models/api/tipos.js').PostResumen[] }} */
  let { posts } = $props()
</script>

<Panel titulo="Lo más leído">
  <ol class="top">
    {#each posts as post, i (post.id)}
      <li class="top__item">
        <span class="top__numero">{String(i + 1).padStart(2, '0')}</span>
        <div>
          <a class="top__titulo" href={'#' + rutaPost(post.id)}>{post.title}</a>
          <p class="top__meta">{post.comments_count} comentarios · ★ {post.rating_avg.toFixed(1)}</p>
        </div>
      </li>
    {/each}
  </ol>
</Panel>
