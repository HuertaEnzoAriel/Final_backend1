<script>
  import './PanelEstadisticas.css'
  import Panel from '../panel/Panel.svelte'

  /** @type {{ stats: import('../../models/api/tipos.js').Estadisticas }} */
  let { stats } = $props()

  let metricas = $derived([
    { valor: stats.published_posts, etiqueta: 'Posts publicados' },
    { valor: stats.total_comments, etiqueta: 'Comentarios' },
    { valor: stats.total_ratings, etiqueta: 'Ratings' },
    { valor: stats.average_rating.toFixed(1), etiqueta: 'Media global' },
  ])
</script>

<Panel titulo="En números">
  <dl class="estadisticas">
    {#each metricas as metrica (metrica.etiqueta)}
      <div class="estadisticas__item">
        <dt class="estadisticas__etiqueta">{metrica.etiqueta}</dt>
        <dd class="estadisticas__valor">{metrica.valor}</dd>
      </div>
    {/each}
  </dl>
</Panel>
