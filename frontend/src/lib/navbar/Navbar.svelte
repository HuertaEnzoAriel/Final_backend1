<script>
  import './Navbar.css'
  import BotonTema from '../botonTema/BotonTema.svelte'
  import MenuUsuario from '../menuUsuario/MenuUsuario.svelte'
  import { sesion } from '../../state/sesion.svelte.js'
  import { ENLACES, puedeVer } from '../../models/navegacion/enlaces.js'
  import { RUTAS } from '../../models/rutas/rutas.js'
  import { SITIO } from '../../models/textos/sitio.js'

  let abierto = $state(false)

  // Enlaces que corresponden a quien está mirando (apunte §4.2).
  let enlaces = $derived(ENLACES.filter((enlace) => puedeVer(enlace, sesion.usuario)))
</script>

<header class="navbar">
  <!-- Una sola fila: marca a la izquierda; enlaces, tema y menú a la derecha. -->
  <div class="navbar__barra">
    <a class="navbar__marca" href={'#' + RUTAS.INICIO}>
      {SITIO.NOMBRE} <span class="navbar__marca-acento">{SITIO.NOMBRE_ACENTO}</span>
    </a>

    <nav class="navbar__nav" class:navbar__nav--abierto={abierto}>
      <ul class="navbar__lista">
        {#each enlaces as enlace (enlace.ruta)}
          <li>
            <a class="navbar__enlace" href={'#' + enlace.ruta} onclick={() => (abierto = false)}>
              {enlace.texto}
            </a>
          </li>
        {/each}
      </ul>
    </nav>

    <div class="navbar__acciones">
      <BotonTema />
      <MenuUsuario />
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
</header>
