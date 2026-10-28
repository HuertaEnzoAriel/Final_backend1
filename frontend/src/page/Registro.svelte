<script>
  import FormularioAuth from '../lib/formularioAuth/FormularioAuth.svelte'
  import CampoTexto from '../lib/campoTexto/CampoTexto.svelte'
  import { registrarse } from '../services/auth.js'
  import { sesion } from '../state/sesion.svelte.js'
  import { RUTAS } from '../models/rutas/rutas.js'
  import { AUTH } from '../models/textos/auth.js'

  // Solo para invitados: con sesión iniciada vuelve al inicio.
  if (sesion.usuario) window.location.hash = RUTAS.INICIO

  let name = $state('')
  let email = $state('')
  let password = $state('')
  let password_confirmation = $state('')
  let enviando = $state(false)
  let error = $state('')
  /** @type {Record<string, string[]>} */
  let errores = $state({})

  /** @param {SubmitEvent} event */
  async function enviar(event) {
    event.preventDefault()
    enviando = true
    error = ''
    errores = {}
    try {
      await registrarse({ name, email, password, password_confirmation })
      window.location.hash = RUTAS.INICIO
    } catch (e) {
      // 422: errores por campo. Sin errores por campo: falla de red o del servidor.
      errores = /** @type {{ errores?: Record<string, string[]> }} */ (e).errores ?? {}
      if (!Object.keys(errores).length) error = AUTH.ERROR_GENERAL
    } finally {
      enviando = false
    }
  }
</script>

<FormularioAuth
  titulo={AUTH.REGISTRO.TITULO}
  bajada={AUTH.REGISTRO.BAJADA}
  textoBoton={AUTH.REGISTRO.BOTON}
  enlaces={AUTH.REGISTRO.ENLACES}
  onsubmit={enviar}
  {enviando}
  {error}
>
  <CampoTexto nombre="name" etiqueta="Nombre" autocomplete="name" error={errores.name?.[0]} bind:value={name} />
  <CampoTexto nombre="email" etiqueta="Email" type="email" autocomplete="email" error={errores.email?.[0]} bind:value={email} />
  <CampoTexto
    nombre="password"
    etiqueta="Contraseña"
    type="password"
    autocomplete="new-password"
    error={errores.password?.[0]}
    bind:value={password}
  />
  <CampoTexto
    nombre="password_confirmation"
    etiqueta="Confirmar contraseña"
    type="password"
    autocomplete="new-password"
    bind:value={password_confirmation}
  />
</FormularioAuth>
