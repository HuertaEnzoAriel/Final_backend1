import { RUTAS } from '../rutas/rutas.js'

// Textos y enlaces de las páginas de login y registro.
export const AUTH = {
  ERROR_GENERAL: 'No se pudo conectar con el servidor. Probá de nuevo.',
  ENVIANDO: 'Enviando…',
  LOGIN: {
    TITULO: 'Entrar',
    BAJADA: 'Usá tu cuenta para comentar, calificar y publicar.',
    BOTON: 'Entrar',
    ENLACES: [
      { texto: 'Crear cuenta', ruta: RUTAS.REGISTRO },
      { texto: 'Volver al blog', ruta: RUTAS.INICIO },
    ],
  },
  REGISTRO: {
    TITULO: 'Crear cuenta',
    BAJADA: 'Registrate para participar con tu propio nombre.',
    BOTON: 'Crear cuenta',
    ENLACES: [
      { texto: 'Ya tengo cuenta', ruta: RUTAS.LOGIN },
      { texto: 'Volver al blog', ruta: RUTAS.INICIO },
    ],
  },
}
