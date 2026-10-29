<script>
  import './Acerca.css'
  import EncabezadoSeccion from '../components/encabezadoSeccion/EncabezadoSeccion.svelte'
  import { sesion } from '../state/sesion.svelte.js'
  import { RUTAS } from '../models/rutas/rutas.js'
  import { ACERCA } from '../models/textos/acerca.js'

  // Con sesión iniciada no tiene sentido invitar a crear cuenta.
  let enlaces = $derived(ACERCA.ENLACES.filter((enlace) => !sesion.usuario || enlace.ruta !== RUTAS.REGISTRO))
</script>

<EncabezadoSeccion titulo={ACERCA.TITULO} bajada={ACERCA.BAJADA} />

<div class="acerca">
  {#each ACERCA.PARRAFOS as parrafo, i (i)}
    <p class="acerca__parrafo">{parrafo}</p>
  {/each}

  <h2 class="acerca__subtitulo">{ACERCA.TITULO_ROLES}</h2>
  <dl class="acerca__roles">
    {#each ACERCA.ROLES as rol (rol.nombre)}
      <div class="acerca__rol">
        <dt class="acerca__rol-nombre">{rol.nombre}</dt>
        <dd class="acerca__rol-texto">{rol.texto}</dd>
      </div>
    {/each}
  </dl>

  <nav class="acerca__enlaces">
    {#each enlaces as enlace (enlace.ruta)}
      <a class="acerca__enlace" href={'#' + enlace.ruta}>{enlace.texto}</a>
    {/each}
  </nav>
</div>
