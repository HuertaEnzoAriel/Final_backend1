<script>
  import './Footer.css'
  import { SITIO } from '../../models/textos/sitio.js'

  const anio = new Date().getFullYear()
</script>

<footer class="footer">
  <div class="footer__contenido">
    <p class="footer__marca">
      {SITIO.NOMBRE} <span class="footer__marca-acento">{SITIO.NOMBRE_ACENTO}</span>
    </p>
    <p class="footer__texto">{SITIO.LEMA} · {anio}</p>
  </div>
</footer>
