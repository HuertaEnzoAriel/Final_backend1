<script>
  import './Navbar.css'
  import BotonTema from '../botonTema/BotonTema.svelte'
  import { sesion } from '../../state/sesion.svelte.js'
  import { salir } from '../../services/auth.js'
  import { ACCESO, ENLACES } from '../../models/navegacion/enlaces.js'
  import { RUTAS } from '../../models/rutas/rutas.js'
  import { SITIO } from '../../models/textos/sitio.js'

  let abierto = $state(false)

  // Enlaces que corresponden a quien está mirando (apunte §4.2).
  let enlaces = $derived(
    ENLACES.filter((enlace) => {
      if (enlace.acceso === ACCESO.INVITADO) return !sesion.usuario
      if (enlace.acceso === ACCESO.USUARIO) {
        return !!sesion.usuario && (!enlace.roles || enlace.roles.includes(sesion.usuario.role))
      }
      return true
    }),
  )

  async function alSalir() {
    abierto = false
    await salir()
    window.location.hash = RUTAS.INICIO
  }
</script>

<header class="navbar">
  <div class="navbar__barra">
    <a class="navbar__marca" href={'#' + RUTAS.INICIO}>
      {SITIO.NOMBRE} <span class="navbar__marca-acento">{SITIO.NOMBRE_ACENTO}</span>
    </a>

    <div class="navbar__acciones">
      <BotonTema />
      <button
        class="navbar__menu"
        type="button"
        aria-label="Abrir menú"
        aria-expanded={abierto}
        onclick={() => (abierto = !abierto)}
      >
        ☰
      </button>
    </div>
  </div>

  <nav class="navbar__nav" class:navbar__nav--abierto={abierto}>
    <ul class="navbar__lista">
      {#each enlaces as enlace (enlace.ruta)}
        <li>
          <a class="navbar__enlace" href={'#' + enlace.ruta} onclick={() => (abierto = false)}>
            {enlace.texto}
          </a>
        </li>
      {/each}

      {#if sesion.usuario}
        <li class="navbar__usuario">{sesion.usuario.name}</li>
        <li>
          <button class="navbar__salir" type="button" onclick={alSalir}>Salir</button>
        </li>
      {/if}
    </ul>
  </nav>
</header>
