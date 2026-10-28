import { RUTAS } from '../rutas/rutas.js'

// Textos fijos de la página "Acerca de".
export const ACERCA = {
  TITULO: 'Acerca de',
  BAJADA: 'Qué es Tech Blog y cómo participar.',
  PARRAFOS: [
    'Tech Blog es un blog para desarrolladores curiosos: artículos sobre programación, herramientas y todo lo que aprendemos en el camino.',
    'Cualquiera puede leer. Con una cuenta podés comentar y calificar los artículos; si querés escribir, podés pedir ser editor.',
  ],
  TITULO_ROLES: 'Quién puede hacer qué',
  ROLES: [
    { nombre: 'Invitado', texto: 'Lee todos los artículos publicados, el ranking y los autores.' },
    { nombre: 'Usuario', texto: 'Comenta y califica artículos. Puede pedir ser editor desde su dashboard.' },
    { nombre: 'Editor', texto: 'Escribe artículos. Cada uno pasa por moderación antes de publicarse.' },
    { nombre: 'Administrador', texto: 'Aprueba o rechaza artículos y gestiona los roles de los usuarios.' },
  ],
  ENLACES: [
    { texto: 'Crear cuenta', ruta: RUTAS.REGISTRO },
    { texto: 'Leer el blog', ruta: RUTAS.INICIO },
  ],
}
