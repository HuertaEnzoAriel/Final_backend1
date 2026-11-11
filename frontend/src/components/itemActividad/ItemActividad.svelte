<script>
  import './ItemActividad.css'
  import { rutaPost } from '../../models/rutas/rutas.js'
  import { ACTIVIDAD } from '../../models/textos/actividad.js'

  /** @type {{ item: import('../../models/api/tipos.js').ItemActividad }} */
  let { item } = $props()

  let fecha = $derived(
    new Date(item.created_at).toLocaleString('es-AR', { dateStyle: 'medium', timeStyle: 'short' }),
  )
</script>

<li class="item-actividad" class:item-actividad--nuevo={item.is_new}>
  <a class="item-actividad__enlace" href={'#' + rutaPost(item.post_id)}>
    <p class="item-actividad__linea">
      <strong>{item.author ?? 'Alguien'}</strong>
      {#if item.type === 'comment'}
        {ACTIVIDAD.COMENTO}
      {:else if item.type === 'reply'}
        {ACTIVIDAD.RESPONDIO}
      {:else}
        {ACTIVIDAD.CALIFICO}
        <span class="item-actividad__estrellas" aria-label="{item.score} de 5">
          {'★'.repeat(item.score ?? 0)}{'☆'.repeat(5 - (item.score ?? 0))}
        </span>
        en
      {/if}
      <em>{item.post_title ?? 'un artículo'}</em>
      {#if item.is_new}<span class="item-actividad__etiqueta">{ACTIVIDAD.NUEVO}</span>{/if}
    </p>
    {#if item.type === 'comment' || item.type === 'reply'}
      <p class="item-actividad__texto">{item.content}</p>
    {/if}
    {#if item.type === 'reply' && item.original}
      <p class="item-actividad__cita">{ACTIVIDAD.TU_COMENTARIO}: “{item.original}”</p>
    {/if}
    <time class="item-actividad__fecha" datetime={item.created_at}>{fecha}</time>
  </a>
</li>
