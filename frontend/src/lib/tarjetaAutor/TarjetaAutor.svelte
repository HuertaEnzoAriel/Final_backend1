<script>
  import './TarjetaAutor.css'
  import { rutaPost } from '../../models/rutas/rutas.js'

  /** @type {{ autor: import('../../models/api/tipos.js').Autor }} */
  let { autor } = $props()

  let inicial = $derived(autor.name.charAt(0).toUpperCase())
</script>

<article class="tarjeta-autor">
  <header class="tarjeta-autor__cabecera">
    <span class="tarjeta-autor__avatar" aria-hidden="true">{inicial}</span>
    <div>
      <h2 class="tarjeta-autor__nombre">{autor.name}</h2>
      <p class="tarjeta-autor__numeros">
        {autor.posts_count} artículos · {autor.comments_count} comentarios · ★ {autor.rating_avg.toFixed(1)}
      </p>
    </div>
  </header>

  <ul class="tarjeta-autor__posts">
    {#each autor.posts as post (post.id)}
      <li class="tarjeta-autor__item">
        <a class="tarjeta-autor__post" href={'#' + rutaPost(post.id)}>{post.title}</a>
      </li>
    {/each}
  </ul>
</article>
