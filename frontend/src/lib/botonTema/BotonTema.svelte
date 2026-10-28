<script>
  import './BotonTema.css'
  import { tema, alternarTema } from '../../state/tema.svelte.js'
  import { TEMA } from '../../models/tema/tema.js'

  let oscuro = $derived(tema.actual === TEMA.OSCURO)
</script>

<button
  class="boton-tema"
  type="button"
  aria-label={oscuro ? 'Activar modo claro' : 'Activar modo oscuro'}
  title={oscuro ? 'Modo claro' : 'Modo oscuro'}
  onclick={alternarTema}
>
  {oscuro ? '☀' : '☾'}
</button>
