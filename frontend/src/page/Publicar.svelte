<script>
  import './Publicar.css'
  import '../components/campoTexto/CampoTexto.css'
  import Aviso from '../components/aviso/Aviso.svelte'
  import Boton from '../components/boton/Boton.svelte'
  import EncabezadoSeccion from '../components/encabezadoSeccion/EncabezadoSeccion.svelte'
  import { pedir } from '../services/api.js'
  import { ENDPOINTS } from '../models/api/endpoints.js'
  import { RUTAS } from '../models/rutas/rutas.js'
  import { ROLES_PUBLICAN } from '../models/roles/roles.js'
  import { PUBLICAR } from '../models/textos/publicar.js'
  import { AUTH } from '../models/textos/auth.js'
  import { sesion } from '../state/sesion.svelte.js'

  // Solo editores y admins: el resto vuelve al login o al inicio.
  if (!sesion.usuario) window.location.hash = RUTAS.LOGIN
  const puede = !!sesion.usuario && ROLES_PUBLICAN.includes(sesion.usuario.role)

  let titulo = $state('')
  let contenido = $state('')
  /** @type {File | null} */
  let imagen = $state(null)
  let enviando = $state(false)
  let error = $state('')
  let enviado = $state(false)
  /** @type {Record<string, string[]>} */
  let errores = $state({})

  /** @param {Event} event */
  function elegirImagen(event) {
    imagen = /** @type {HTMLInputElement} */ (event.currentTarget).files?.[0] ?? null
  }

  /** @param {SubmitEvent} event */
  async function enviar(event) {
    event.preventDefault()
    enviando = true
    error = ''
    errores = {}
    try {
      const datos = new FormData()
      datos.append('title', titulo)
      datos.append('content', contenido)
      if (imagen) datos.append('image', imagen)
      await pedir(ENDPOINTS.POSTS, { metodo: 'POST', datos })
      enviado = true
      titulo = ''
      contenido = ''
      imagen = null
    } catch (e) {
      errores = /** @type {{ errores?: Record<string, string[]> }} */ (e).errores ?? {}
      if (!Object.keys(errores).length) error = /** @type {Error} */ (e).message || AUTH.ERROR_GENERAL
    } finally {
      enviando = false
    }
  }
</script>

<EncabezadoSeccion titulo={PUBLICAR.TITULO} bajada={PUBLICAR.BAJADA} />

{#if !puede}
  <Aviso tipo="error" texto={PUBLICAR.SIN_PERMISO} />
{:else}
  {#if enviado}
    <Aviso texto={PUBLICAR.ENVIADO} />
  {/if}
  {#if error}
    <Aviso tipo="error" texto={error} />
  {/if}

  <form class="publicar" onsubmit={enviar}>
    <div class="campo">
      <label class="campo__etiqueta" for="title">{PUBLICAR.TITULO_CAMPO}</label>
      <input class="campo__control" class:campo__control--error={!!errores.title} id="title" name="title" maxlength="180" required bind:value={titulo} />
      {#if errores.title}<p class="campo__error">{errores.title[0]}</p>{/if}
    </div>

    <div class="campo">
      <label class="campo__etiqueta" for="content">{PUBLICAR.CONTENIDO}</label>
      <textarea class="campo__control publicar__contenido" class:campo__control--error={!!errores.content} id="content" name="content" rows="10" required bind:value={contenido}></textarea>
      {#if errores.content}<p class="campo__error">{errores.content[0]}</p>{/if}
    </div>

    <div class="campo">
      <label class="campo__etiqueta" for="image">{PUBLICAR.IMAGEN}</label>
      <input class="campo__control" class:campo__control--error={!!errores.image} id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" onchange={elegirImagen} />
      <p class="publicar__ayuda">{PUBLICAR.AYUDA_IMAGEN}</p>
      {#if errores.image}<p class="campo__error">{errores.image[0]}</p>{/if}
    </div>

    <Boton type="submit" disabled={enviando}>{enviando ? AUTH.ENVIANDO : PUBLICAR.BOTON}</Boton>
  </form>
{/if}
