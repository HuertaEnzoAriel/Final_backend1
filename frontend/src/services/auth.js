import { pedir } from './api.js'
import { ENDPOINTS } from '../models/api/endpoints.js'
import { iniciarSesion, cerrarSesion } from '../state/sesion.svelte.js'

/** @param {{ email: string, password: string }} credenciales */
export async function entrar(credenciales) {
  const { user, token } = await pedir(ENDPOINTS.LOGIN, { metodo: 'POST', datos: credenciales })
  iniciarSesion(user, token)
}

/** @param {{ name: string, email: string, password: string, password_confirmation: string }} datos */
export async function registrarse(datos) {
  const { user, token } = await pedir(ENDPOINTS.REGISTRO, { metodo: 'POST', datos })
  iniciarSesion(user, token)
}

// Borra el token en Laravel y cierra la sesión del front aunque el pedido falle.
export async function salir() {
  await pedir(ENDPOINTS.LOGOUT, { metodo: 'POST' }).catch(() => {})
  cerrarSesion()
}
