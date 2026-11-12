<script>
  import NoEncontrado from '../../page/noEncontrado/NoEncontrado.svelte'
  import Inicio from '../../page/inicio/Inicio.svelte'
  import Login from '../../page/login/Login.svelte'
  import Registro from '../../page/registro/Registro.svelte'
  import Ranking from '../../page/ranking/Ranking.svelte'
  import Autores from '../../page/autores/Autores.svelte'
  import Acerca from '../../page/acerca/Acerca.svelte'
  import { RUTAS } from '../../models/rutas/rutas.js'
  import Posts from '../../page/posts/Posts.svelte'
  import Actividad from '../../page/actividad/Actividad.svelte'
  import Publicar from '../../page/publicar/Publicar.svelte'
  import Moderacion from '../../page/moderacion/Moderacion.svelte'
  import Dashboard from '../../page/dashboard/Dashboard.svelte'

  // Tabla de páginas: { ruta, componente }. Se completa al migrar cada vista.
  // Una ruta puede tener parámetros, p. ej. '/posts/:id'.
  /** @type {{ ruta: string, componente: any }[]} */
  const paginas = [
    { ruta: RUTAS.INICIO, componente: Inicio },
    { ruta: RUTAS.LOGIN, componente: Login },
    { ruta: RUTAS.REGISTRO, componente: Registro },
    { ruta: RUTAS.RANKING, componente: Ranking },
    { ruta: RUTAS.AUTORES, componente: Autores },
    { ruta: RUTAS.ACERCA, componente: Acerca },
    { ruta: RUTAS.PUBLICAR, componente: Publicar },
    { ruta: RUTAS.MODERACION, componente: Moderacion },
    { ruta: RUTAS.DASHBOARD, componente: Dashboard },
    { ruta: RUTAS.POST, componente: Posts },
    { ruta: RUTAS.ACTIVIDAD, componente: Actividad },
  ]

  let hash = $state(window.location.hash)

  /** @param {string} valor */
  function rutaDesdeHash(valor) {
    return valor.replace(/^#/, '') || RUTAS.INICIO
  }

  /**
   * Compara '/posts/:id' con '/posts/5' y devuelve { id: '5' }, o null si no coincide.
   * @param {string} patron
   * @param {string} ruta
   */
  function coincidir(patron, ruta) {
    const partesPatron = patron.split('/')
    const partesRuta = ruta.split('/')
    if (partesPatron.length !== partesRuta.length) return null

    /** @type {Record<string, string>} */
    const params = {}
    for (let i = 0; i < partesPatron.length; i++) {
      if (partesPatron[i].startsWith(':')) {
        params[partesPatron[i].slice(1)] = decodeURIComponent(partesRuta[i])
      } else if (partesPatron[i] !== partesRuta[i]) {
        return null
      }
    }
    return params
  }

  // Página actual derivada del hash (apunte §4.2).
  let actual = $derived.by(() => {
    const ruta = rutaDesdeHash(hash)
    for (const pagina of paginas) {
      const params = coincidir(pagina.ruta, ruta)
      if (params) return { componente: pagina.componente, params }
    }
    return null
  })
</script>

<!-- Escucha los cambios de URL sin addEventListener manual (apunte §10.1). -->
<svelte:window onhashchange={() => (hash = window.location.hash)} />

{#if actual}
  <actual.componente params={actual.params} />
{:else}
  <NoEncontrado />
{/if}
