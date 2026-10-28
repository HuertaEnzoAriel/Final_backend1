import { SESION } from '../models/sesion/sesion.js'
import { RUTAS } from '../models/rutas/rutas.js'

/**
 * @typedef {{ id: number, name: string, email: string, role: string }} Usuario
 */

// Estado global compartido (apunte §6.2): cualquier componente lo importa y lo lee.
export const sesion = $state(
  /** @type {{ usuario: Usuario | null, token: string | null }} */ ({ usuario: null, token: null }),
)

/** @type {ReturnType<typeof setTimeout> | undefined} */
let temporizador

/**
 * @param {Usuario} usuario
 * @param {string} token
 */
export function iniciarSesion(usuario, token) {
  sesion.usuario = usuario
  sesion.token = token
  registrarActividad()
}

export function cerrarSesion() {
  clearTimeout(temporizador)
  sesion.usuario = null
  sesion.token = null
  localStorage.removeItem(SESION.CLAVE_STORAGE)
}

// Cierra la sesión por inactividad y manda al login.
export function expirarSesion() {
  cerrarSesion()
  window.location.hash = RUTAS.LOGIN
}

// Reinicia la cuenta regresiva de inactividad y guarda la sesión con la hora actual.
export function registrarActividad() {
  if (!sesion.token) return
  localStorage.setItem(
    SESION.CLAVE_STORAGE,
    JSON.stringify({ usuario: sesion.usuario, token: sesion.token, ultimaActividad: Date.now() }),
  )
  armarTemporizador(SESION.MS_INACTIVIDAD)
}

/** @param {number} ms */
function armarTemporizador(ms) {
  clearTimeout(temporizador)
  temporizador = setTimeout(expirarSesion, ms)
}

// Al cargar la app, recupera la sesión guardada si no venció.
function restaurarSesion() {
  let guardada = null
  try {
    guardada = JSON.parse(localStorage.getItem(SESION.CLAVE_STORAGE) ?? 'null')
  } catch {
    guardada = null
  }
  if (!guardada) return

  const restante = SESION.MS_INACTIVIDAD - (Date.now() - guardada.ultimaActividad)
  if (restante <= 0) {
    localStorage.removeItem(SESION.CLAVE_STORAGE)
    return
  }

  sesion.usuario = guardada.usuario
  sesion.token = guardada.token
  armarTemporizador(restante)
}

restaurarSesion()
