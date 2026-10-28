<script>
  import './Boton.css'

  // Con `href` es un enlace; sin `href` es un <button>.
  /**
   * @type {{
   *   href?: string,
   *   variante?: 'primario' | 'secundario',
   *   type?: 'button' | 'submit',
   *   onclick?: () => void,
   *   disabled?: boolean,
   *   children?: import('svelte').Snippet,
   * }}
   */
  let { href = '', variante = 'primario', type = 'button', onclick = undefined, disabled = false, children } = $props()
</script>

{#if href}
  <a class="boton boton--{variante}" {href}>{@render children?.()}</a>
{:else}
  <button class="boton boton--{variante}" {type} {onclick} {disabled}>{@render children?.()}</button>
{/if}
