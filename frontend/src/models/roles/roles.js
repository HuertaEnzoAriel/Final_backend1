// Roles de usuario (mismos valores que la columna users.role de Laravel).
export const ROLES = {
  ADMIN: 'admin',
  EDITOR: 'editor',
  USUARIO: 'user',
}

// Nombre legible de cada rol.
export const ETIQUETAS_ROL = {
  [ROLES.ADMIN]: 'Administrador',
  [ROLES.EDITOR]: 'Editor',
  [ROLES.USUARIO]: 'Usuario',
}

// Roles que pueden publicar posts (igual que User::canPublish()).
export const ROLES_PUBLICAN = [ROLES.ADMIN, ROLES.EDITOR]
