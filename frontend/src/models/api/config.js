// Configuración de la conexión con la API de Laravel.
export const API_CONFIG = {
  URL_BASE: 'http://localhost:8000/api',
  // Corta un pedido colgado (apunte §8.7).
  MS_TIMEOUT: 10000,
  // Cada cuánto, como máximo, se avisa al backend que el usuario sigue activo.
  MS_ENTRE_AVISOS: 60000,
}
