// Rutas del front (van después del # en la URL: #/login, #/posts/5).
export const RUTAS = {
  INICIO: '/',
  LOGIN: '/login',
  REGISTRO: '/registro',
  DASHBOARD: '/dashboard',
  PUBLICAR: '/posts/nuevo',
  POST: '/posts/:id',
  MODERACION: '/admin/moderacion',
}

// Arma la ruta de un post concreto: rutaPost(5) → '/posts/5'.
/** @param {number} id */
export const rutaPost = (id) => RUTAS.POST.replace(':id', String(id))
