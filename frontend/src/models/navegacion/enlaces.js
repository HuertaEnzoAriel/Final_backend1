import { RUTAS } from '../rutas/rutas.js'
import { ROLES, ROLES_PUBLICAN } from '../roles/roles.js'

// Quién ve cada enlace de la barra de navegación.
export const ACCESO = {
  TODOS: 'todos',
  INVITADO: 'invitado',
  USUARIO: 'usuario',
}

// Enlaces de la barra de navegación. `roles` limita el enlace a esos roles.
export const ENLACES = [
  { texto: 'Inicio', ruta: RUTAS.INICIO, acceso: ACCESO.TODOS },
  { texto: 'Publicar', ruta: RUTAS.PUBLICAR, acceso: ACCESO.USUARIO, roles: ROLES_PUBLICAN },
  { texto: 'Dashboard', ruta: RUTAS.DASHBOARD, acceso: ACCESO.USUARIO },
  { texto: 'Moderación', ruta: RUTAS.MODERACION, acceso: ACCESO.USUARIO, roles: [ROLES.ADMIN] },
  { texto: 'Entrar', ruta: RUTAS.LOGIN, acceso: ACCESO.INVITADO },
  { texto: 'Crear cuenta', ruta: RUTAS.REGISTRO, acceso: ACCESO.INVITADO },
]
