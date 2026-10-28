import { RUTAS } from '../rutas/rutas.js'
import { ROLES, ROLES_PUBLICAN } from '../roles/roles.js'

// Quién ve cada enlace de la barra de navegación.
export const ACCESO = {
  TODOS: 'todos',
  INVITADO: 'invitado',
  USUARIO: 'usuario',
}

// Secciones del blog, visibles en la barra. `roles` limita el enlace a esos roles.
export const ENLACES = [
  { texto: 'Inicio', ruta: RUTAS.INICIO, acceso: ACCESO.TODOS },
  { texto: 'Ranking', ruta: RUTAS.RANKING, acceso: ACCESO.TODOS },
  { texto: 'Autores', ruta: RUTAS.AUTORES, acceso: ACCESO.TODOS },
  { texto: 'Acerca de', ruta: RUTAS.ACERCA, acceso: ACCESO.TODOS },
]

// Opciones de cuenta, dentro del menú del ícono de usuario.
export const ENLACES_CUENTA = [
  { texto: 'Entrar', ruta: RUTAS.LOGIN, acceso: ACCESO.INVITADO },
  { texto: 'Crear cuenta', ruta: RUTAS.REGISTRO, acceso: ACCESO.INVITADO },
  { texto: 'Publicar', ruta: RUTAS.PUBLICAR, acceso: ACCESO.USUARIO, roles: ROLES_PUBLICAN },
  { texto: 'Dashboard', ruta: RUTAS.DASHBOARD, acceso: ACCESO.USUARIO },
  { texto: 'Moderación', ruta: RUTAS.MODERACION, acceso: ACCESO.USUARIO, roles: [ROLES.ADMIN] },
]

/**
 * Indica si un enlace corresponde a quien está mirando.
 * @param {{ acceso: string, roles?: string[] }} enlace
 * @param {{ role: string } | null} usuario
 */
export function puedeVer(enlace, usuario) {
  if (enlace.acceso === ACCESO.INVITADO) return !usuario
  if (enlace.acceso === ACCESO.USUARIO) return !!usuario && (!enlace.roles || enlace.roles.includes(usuario.role))
  return true
}
