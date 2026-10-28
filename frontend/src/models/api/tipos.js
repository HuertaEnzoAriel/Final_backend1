// Forma de los datos que devuelve la API (solo documentación JSDoc, no exporta valores).

/**
 * @typedef {{
 *   id: number,
 *   title: string,
 *   excerpt: string,
 *   image_url: string | null,
 *   published_at: string | null,
 *   author: string,
 *   comments_count: number,
 *   rating_avg: number,
 * }} PostResumen
 */

/**
 * @typedef {{
 *   published_posts: number,
 *   total_comments: number,
 *   total_ratings: number,
 *   average_rating: number,
 * }} Estadisticas
 */

/**
 * @typedef {{ posts: PostResumen[], stats: Estadisticas, top: PostResumen[] }} RespuestaInicio
 */

export {}
