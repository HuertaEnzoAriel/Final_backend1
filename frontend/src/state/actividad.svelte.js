import { sesion } from './sesion.svelte.js'
import { pedir } from '../services/api.js'
import { ENDPOINTS } from '../models/api/endpoints.js'

// Cantidad de novedades sin ver; la lee el badge del menú de usuario.
export const actividad = $state({ noVistos: 0 })

// Consulta el contador. Falla en silencio: es un adorno, no debe romper nada.
export async function refrescarNoVistos() {
  if (!sesion.token) {
    actividad.noVistos = 0
    return
  }
  try {
    const resumen = await pedir(ENDPOINTS.ACTIVIDAD_RESUMEN)
    actividad.noVistos = resumen.unread
  } catch {
    /* sin badge */
  }
}

// Marca todo como visto (al abrir la página de actividad).
export async function marcarVisto() {
  try {
    await pedir(ENDPOINTS.ACTIVIDAD_VISTA, { metodo: 'POST' })
    actividad.noVistos = 0
  } catch {
    /* se reintenta en la próxima visita */
  }
}
