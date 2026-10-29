<script>
  import './ImagenPost.css'

  // Imagen del post o, si no tiene, la inicial del título. Ocupa todo su contenedor.
  let { url = null, titulo = '' } = $props()
</script>

{#if url}
  <img class="imagen-post" src={url} alt="Imagen de {titulo}" loading="lazy" />
{:else}
  <div class="imagen-post imagen-post--vacia" aria-hidden="true">{titulo.charAt(0)}</div>
{/if}
