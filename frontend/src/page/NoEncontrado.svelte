<script>
  import './NoEncontrado.css'
  import { RUTAS } from '../models/rutas/rutas.js'
</script>

<section class="no-encontrado">
  <p class="no-encontrado__codigo">404</p>
  <h1 class="no-encontrado__titulo">Esta página no existe</h1>
  <p class="no-encontrado__texto">El enlace puede estar roto o la página todavía no fue migrada.</p>
  <a class="no-encontrado__enlace" href={'#' + RUTAS.INICIO}>Volver al inicio</a>
</section>
