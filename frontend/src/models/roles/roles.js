// Roles de usuario (mismos valores que la columna users.role de Laravel).
export const ROLES = {
  ADMIN: 'admin',
  EDITOR: 'editor',
  USUARIO: 'user',
}

// Roles que pueden publicar posts (igual que User::canPublish()).
export const ROLES_PUBLICAN = [ROLES.ADMIN, ROLES.EDITOR]
