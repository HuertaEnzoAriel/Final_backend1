<script>
  import './ItemActividad.css'
  import { RUTAS, rutaPost } from '../../models/rutas/rutas.js'
  import { ETIQUETAS_ROL } from '../../models/roles/roles.js'
  import { ACTIVIDAD } from '../../models/textos/actividad.js'

  /** @type {{ item: import('../../models/api/tipos.js').ItemActividad }} */
  let { item } = $props()

  let fecha = $derived(
    new Date(item.created_at).toLocaleString('es-AR', { dateStyle: 'medium', timeStyle: 'short' }),
  )

  // Lleva al post, salvo si no es visible (rechazado) o no hay post (cambio de rol).
  let destino = $derived(item.post_id && item.type !== 'post_rejected' ? rutaPost(item.post_id) : RUTAS.ACTIVIDAD)
</script>

<li class="item-actividad" class:item-actividad--nuevo={item.is_new}>
  <a class="item-actividad__enlace" href={'#' + destino}>
    <p class="item-actividad__linea">
      {#if item.type === 'post_approved'}
        {ACTIVIDAD.APROBADO} <em>{item.post_title ?? 'un artículo'}</em>
      {:else if item.type === 'post_rejected'}
        {ACTIVIDAD.RECHAZADO} <em>{item.post_title ?? 'un artículo'}</em>
      {:else if item.type === 'role_changed'}
        {ACTIVIDAD.ROL_CAMBIADO} <em>{ETIQUETAS_ROL[item.content ?? ''] ?? item.content}</em>
      {:else}
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
      {/if}
      {#if item.is_new}<span class="item-actividad__etiqueta">{ACTIVIDAD.NUEVO}</span>{/if}
    </p>
    {#if item.type === 'comment' || item.type === 'reply'}
      <p class="item-actividad__texto">{item.content}</p>
    {/if}
    {#if item.type === 'post_rejected' && item.content}
      <p class="item-actividad__cita">{ACTIVIDAD.MOTIVO}: “{item.content}”</p>
    {/if}
    {#if item.type === 'reply' && item.original}
      <p class="item-actividad__cita">{ACTIVIDAD.TU_COMENTARIO}: “{item.original}”</p>
    {/if}
    <time class="item-actividad__fecha" datetime={item.created_at}>{fecha}</time>
  </a>
</li>
