import { API_CONFIG } from '../models/api/config.js'
import { ENDPOINTS } from '../models/api/endpoints.js'
import { sesion, expirarSesion, registrarActividad } from '../state/sesion.svelte.js'

/**
 * Única función para hablar con Laravel (apunte §8.7): agrega token y headers,
 * corta pedidos colgados, revisa res.ok y devuelve los errores de validación (422).
 *
 * @param {string} endpoint
 * @param {{ metodo?: string, datos?: object | FormData | null }} [opciones]
 */
export async function pedir(endpoint, { metodo = 'GET', datos = null } = {}) {
  /** @type {Record<string, string>} */
  const headers = { Accept: 'application/json' }
  if (sesion.token) headers.Authorization = `Bearer ${sesion.token}`

  /** @type {BodyInit | undefined} */
  let body
  if (datos instanceof FormData) {
    body = datos
  } else if (datos) {
    headers['Content-Type'] = 'application/json'
    body = JSON.stringify(datos)
  }

  const controller = new AbortController()
  const id = setTimeout(() => controller.abort(), API_CONFIG.MS_TIMEOUT)

  let res
  try {
    res = await fetch(API_CONFIG.URL_BASE + endpoint, {
      method: metodo,
      headers,
      body,
      signal: controller.signal,
    })
  } finally {
    clearTimeout(id)
  }

  // Token vencido o inválido: se cierra la sesión del front también.
  if (res.status === 401 && sesion.token) expirarSesion()

  const json = await res.json().catch(() => null)
  if (!res.ok) {
    throw Object.assign(new Error(json?.message ?? `HTTP ${res.status}`), {
      errores: json?.errors ?? {},
    })
  }
  return json
}

let ultimoAviso = 0

// Se llama en cada clic o tecla: reinicia los 5 minutos del front y, como mucho
// una vez por minuto, avisa al backend para que el token tampoco venza.
export function avisarActividad() {
  if (!sesion.token) return
  registrarActividad()

  if (Date.now() - ultimoAviso < API_CONFIG.MS_ENTRE_AVISOS) return
  ultimoAviso = Date.now()
  pedir(ENDPOINTS.USUARIO).catch(() => {})
}
