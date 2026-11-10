// Forma de los datos que devuelve la API (solo documentación JSDoc, no exporta valores).

/**
 * @typedef {{
 *   id: number,
 *   title: string,
 *   excerpt: string,
 *   image_url: string | null,
 *   published_at: string | null,
 *   author: string,
 *   author_id: number,
 *   comments_count: number,
 *   rating_avg: number,
 * }} PostResumen
 */

/**
 * @typedef {{
 *   id: number,
 *   content: string,
 *   author: string,
 *   created_at: string | null,
 *   reply: string | null,
 *   reply_at: string | null,
 * }} Comentario
 */

/**
 * @typedef {{
 *   post: PostResumen & { content: string, ratings_count: number },
 *   comments: Comentario[],
 *   my_rating: number | null,
 * }} RespuestaPost
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

/**
 * @typedef {{ posts: PostResumen[] }} RespuestaRanking
 */

/**
 * @typedef {{
 *   id: number,
 *   name: string,
 *   posts_count: number,
 *   comments_count: number,
 *   rating_avg: number,
 *   posts: PostResumen[],
 * }} Autor
 */

/**
 * @typedef {{ authors: Autor[] }} RespuestaAutores
 */

export {}
