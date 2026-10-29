<script>
  import FormularioAuth from '../components/formularioAuth/FormularioAuth.svelte'
  import CampoTexto from '../components/campoTexto/CampoTexto.svelte'
  import { entrar } from '../services/auth.js'
  import { sesion } from '../state/sesion.svelte.js'
  import { RUTAS } from '../models/rutas/rutas.js'
  import { AUTH } from '../models/textos/auth.js'

  // Solo para invitados: con sesión iniciada vuelve al inicio.
  if (sesion.usuario) window.location.hash = RUTAS.INICIO

  let email = $state('')
  let password = $state('')
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
      await entrar({ email, password })
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
  titulo={AUTH.LOGIN.TITULO}
  bajada={AUTH.LOGIN.BAJADA}
  textoBoton={AUTH.LOGIN.BOTON}
  enlaces={AUTH.LOGIN.ENLACES}
  onsubmit={enviar}
  {enviando}
  {error}
>
  <CampoTexto 
    nombre="email" 
    etiqueta="Email" 
    type="email" 
    autocomplete="email" 
    error={errores.email?.[0]} 
    bind:value={email} />
  <CampoTexto
    nombre="password"
    etiqueta="Contraseña"
    type="password"
    autocomplete="current-password"
    error={errores.password?.[0]}
    bind:value={password}
  />
</FormularioAuth>
