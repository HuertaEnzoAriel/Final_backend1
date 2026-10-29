<script>
  import './Panel.css'

  // Caja con título para la columna lateral.
  let { titulo = '', children } = $props()
</script>

<section class="panel">
  <h2 class="panel__titulo">{titulo}</h2>
  {@render children?.()}
</section>
