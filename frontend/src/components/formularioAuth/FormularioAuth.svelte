<script>
  import './FormularioAuth.css'
  import Aviso from '../aviso/Aviso.svelte'
  import Boton from '../boton/Boton.svelte'
  import { AUTH } from '../../models/textos/auth.js'

  // Molde común de login y registro: los campos llegan como children (apunte §5.3).
  /**
   * @type {{
   *   titulo: string,
   *   bajada: string,
   *   textoBoton: string,
   *   enlaces: { texto: string, ruta: string }[],
   *   onsubmit: (event: SubmitEvent) => void,
   *   enviando?: boolean,
   *   error?: string,
   *   children?: import('svelte').Snippet,
   * }}
   */
  let { titulo, bajada, textoBoton, enlaces, onsubmit, enviando = false, error = '', children } = $props()
</script>

<section class="form-auth">
  <header>
    <h1 class="form-auth__titulo">{titulo}</h1>
    <p class="form-auth__bajada">{bajada}</p>
  </header>

  {#if error}
    <Aviso tipo="error" texto={error} />
  {/if}

  <form class="form-auth__form" {onsubmit}>
    {@render children?.()}
    <Boton type="submit" disabled={enviando}>{enviando ? AUTH.ENVIANDO : textoBoton}</Boton>
  </form>

  <nav class="form-auth__pie">
    {#each enlaces as enlace (enlace.ruta)}
      <a class="form-auth__enlace" href={'#' + enlace.ruta}>{enlace.texto}</a>
    {/each}
  </nav>
</section>
