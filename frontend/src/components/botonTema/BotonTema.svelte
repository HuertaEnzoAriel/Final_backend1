<script>
  import './BotonTema.css'
  import { backOut } from 'svelte/easing'
  import { tema, alternarTema } from '../../state/tema.svelte.js'
  import { TEMA } from '../../models/tema/tema.js'

  let oscuro = $derived(tema.actual === TEMA.OSCURO)

  // Transición personalizada: el ícono entra girando y creciendo (apunte §9.6).
  /** @param {Element} _nodo */
  function girar(_nodo, { duration = 450 } = {}) {
    return {
      duration,
      easing: backOut,
      css: (/** @type {number} */ t, /** @type {number} */ u) =>
        `transform: rotate(${u * -120}deg) scale(${0.4 + t * 0.6}); opacity: ${t}`,
    }
  }
</script>

<button
  class="boton-tema"
  type="button"
  aria-label={oscuro ? 'Activar modo claro' : 'Activar modo oscuro'}
  title={oscuro ? 'Modo claro' : 'Modo oscuro'}
  onclick={alternarTema}
>
  <!-- {#key} vuelve a montar el ícono en cada cambio de tema, así la entrada se repite (apunte §3). -->
  {#key oscuro}
    <span class="boton-tema__icono" in:girar>{oscuro ? '☀' : '☾'}</span>
  {/key}
</button>
