<script>
  import './MetaPost.css'
  import { SITIO } from '../../models/textos/sitio.js'

  /** @type {{ autor: string, fecha: string | null }} */
  let { autor, fecha } = $props()

  let fechaLegible = $derived(
    fecha
      ? new Date(fecha).toLocaleDateString(SITIO.IDIOMA_FECHAS, { day: 'numeric', month: 'long', year: 'numeric' })
      : 'Sin fecha',
  )
</script>

<p class="meta-post">
  Por <span class="meta-post__autor">{autor}</span> · {fechaLegible}
</p>
