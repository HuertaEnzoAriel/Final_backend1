<script>
  import './MenuUsuario.css'
  import { sesion } from '../../state/sesion.svelte.js'
  import { salir } from '../../services/auth.js'
  import { ENLACES_CUENTA, puedeVer } from '../../models/navegacion/enlaces.js'
  import { RUTAS } from '../../models/rutas/rutas.js'

  let abierto = $state(false)
  /** @type {HTMLElement | undefined} */
  let contenedor = $state()

  let enlaces = $derived(ENLACES_CUENTA.filter((enlace) => puedeVer(enlace, sesion.usuario)))

  // Cierra el menú al hacer clic fuera de él (apunte §10.1).
  /** @param {MouseEvent} event */
  function alClicVentana(event) {
    if (abierto && contenedor && !contenedor.contains(/** @type {Node} */ (event.target))) abierto = false
  }

  /** @param {KeyboardEvent} event */
  function alTeclaVentana(event) {
    if (event.key === 'Escape') abierto = false
  }

  async function alSalir() {
    abierto = false
    await salir()
    window.location.hash = RUTAS.INICIO
  }
</script>

<svelte:window onclick={alClicVentana} onkeydown={alTeclaVentana} />

<div class="menu-usuario" bind:this={contenedor}>
  <button
    class="menu-usuario__boton"
    type="button"
    aria-label="Menú de cuenta"
    aria-haspopup="menu"
    aria-expanded={abierto}
    onclick={() => (abierto = !abierto)}
  >
    <!-- currentColor: el ícono toma el color del texto, así sigue al tema claro u oscuro. -->
    <svg class="menu-usuario__icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <circle cx="12" cy="8" r="4" />
      <path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8" />
    </svg>
  </button>

  {#if abierto}
    <div class="menu-usuario__panel" role="menu">
      {#if sesion.usuario}
        <p class="menu-usuario__nombre">{sesion.usuario.name}</p>
      {/if}

      {#each enlaces as enlace (enlace.ruta)}
        <a class="menu-usuario__enlace" role="menuitem" href={'#' + enlace.ruta} onclick={() => (abierto = false)}>
          {enlace.texto}
        </a>
      {/each}

      {#if sesion.usuario}
        <button class="menu-usuario__salir" type="button" role="menuitem" onclick={alSalir}>Salir</button>
      {/if}
    </div>
  {/if}
</div>
