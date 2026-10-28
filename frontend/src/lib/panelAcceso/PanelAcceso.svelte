<script>
  import './PanelAcceso.css'
  import Panel from '../panel/Panel.svelte'
  import Boton from '../boton/Boton.svelte'
  import { sesion } from '../../state/sesion.svelte.js'
  import { RUTAS } from '../../models/rutas/rutas.js'
  import { ROLES_PUBLICAN } from '../../models/roles/roles.js'
  import { INICIO } from '../../models/textos/inicio.js'

  let puedePublicar = $derived(!!sesion.usuario && ROLES_PUBLICAN.includes(sesion.usuario.role))
</script>

{#if sesion.usuario}
  <Panel titulo={INICIO.TITULO_AUTOR}>
    <p class="acceso__texto">Sesión iniciada como <strong>{sesion.usuario.name}</strong>.</p>
    <div class="acceso__acciones">
      {#if puedePublicar}
        <Boton href={'#' + RUTAS.PUBLICAR}>Publicar</Boton>
      {/if}
      <Boton href={'#' + RUTAS.DASHBOARD} variante="secundario">Dashboard</Boton>
    </div>
  </Panel>
{:else}
  <Panel titulo={INICIO.TITULO_INVITADO}>
    <p class="acceso__texto">{INICIO.TEXTO_INVITADO}</p>
    <div class="acceso__acciones">
      <Boton href={'#' + RUTAS.LOGIN}>Entrar</Boton>
      <Boton href={'#' + RUTAS.REGISTRO} variante="secundario">Crear cuenta</Boton>
    </div>
  </Panel>
{/if}
