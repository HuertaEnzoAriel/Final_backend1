<script>
  import './EncabezadoSeccion.css'

  // Título grande con bajada, al comienzo de cada sección del blog.
  /** @type {{ titulo: string, bajada?: string }} */
  let { titulo, bajada = '' } = $props()
</script>

<header class="encabezado-seccion">
  <h1 class="encabezado-seccion__titulo">{titulo}</h1>
  {#if bajada}
    <p class="encabezado-seccion__bajada">{bajada}</p>
  {/if}
</header>
