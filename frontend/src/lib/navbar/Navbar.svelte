<script>
  import './Navbar.css'
  import { slide } from 'svelte/transition'
  import { cubicOut } from 'svelte/easing'
  import BotonTema from '../botonTema/BotonTema.svelte'
  import MenuUsuario from '../menuUsuario/MenuUsuario.svelte'
  import Logo from '../logo/Logo.svelte'
  import { sesion } from '../../state/sesion.svelte.js'
  import { ENLACES, puedeVer } from '../../models/navegacion/enlaces.js'
  import { RUTAS } from '../../models/rutas/rutas.js'
  import { SITIO } from '../../models/textos/sitio.js'

  let abierto = $state(false)

  // Enlaces que corresponden a quien está mirando (apunte §4.2).
  let enlaces = $derived(ENLACES.filter((enlace) => puedeVer(enlace, sesion.usuario)))
</script>

<!-- La misma lista sirve para escritorio y para el panel móvil (apunte §5.3). -->
{#snippet lista()}
  <ul class="navbar__lista">
    {#each enlaces as enlace, i (enlace.ruta)}
      <!-- --i escalona la entrada de cada enlace en el panel móvil. -->
      <li class="navbar__item" style:--i={i}>
        <a class="navbar__enlace" href={'#' + enlace.ruta} onclick={() => (abierto = false)}>
          {enlace.texto}
        </a>
      </li>
    {/each}
  </ul>
{/snippet}

<header class="navbar">
  <!-- Una sola fila: marca a la izquierda; enlaces, tema y menú a la derecha. -->
  <div class="navbar__barra">
    <a class="navbar__marca" href={'#' + RUTAS.INICIO} aria-label={SITIO.NOMBRE}>
      <Logo />
    </a>

    <nav class="navbar__nav">
      {@render lista()}
    </nav>

    <div class="navbar__acciones">
      <BotonTema />
      <MenuUsuario />
      <button
        class="navbar__menu"
        class:navbar__menu--abierto={abierto}
        type="button"
        aria-label={abierto ? 'Cerrar menú' : 'Abrir menú'}
        aria-expanded={abierto}
        onclick={() => (abierto = !abierto)}
      >
        <!-- Tres líneas que se cruzan en una X al abrir. -->
        <span class="navbar__linea"></span>
        <span class="navbar__linea"></span>
        <span class="navbar__linea"></span>
      </button>
    </div>

    <!-- En móvil el menú baja deslizándose en una fila propia (apunte §9.1 y §9.3). -->
    {#if abierto}
      <nav class="navbar__movil" transition:slide={{ duration: 300, easing: cubicOut }}>
        {@render lista()}
      </nav>
    {/if}
  </div>
</header>
