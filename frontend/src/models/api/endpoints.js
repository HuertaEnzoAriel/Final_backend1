// Rutas de la API (se suman a API_CONFIG.URL_BASE).
export const ENDPOINTS = {
  USUARIO: '/user',
  POSTS: '/posts',
  RANKING: '/posts/ranking',
  AUTORES: '/authors',
  LOGIN: '/login',
  REGISTRO: '/register',
  LOGOUT: '/logout',
  POST: '/posts/:id',
  COMENTARIOS: '/posts/:id/comments',
  CALIFICACIONES: '/posts/:id/ratings',
}
export const conId = (endpoint, id) => endpoint.replace(':id', String(id))
