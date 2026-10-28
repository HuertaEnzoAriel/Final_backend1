<script>
  import './Boton.css'

  // Con `href` es un enlace; sin `href` es un <button>.
  /**
   * @type {{
   *   href?: string,
   *   variante?: 'primario' | 'secundario',
   *   type?: 'button' | 'submit',
   *   onclick?: () => void,
   *   children?: import('svelte').Snippet,
   * }}
   */
  let { href = '', variante = 'primario', type = 'button', onclick = undefined, children } = $props()
</script>

{#if href}
  <a class="boton boton--{variante}" {href}>{@render children?.()}</a>
{:else}
  <button class="boton boton--{variante}" {type} {onclick}>{@render children?.()}</button>
{/if}
