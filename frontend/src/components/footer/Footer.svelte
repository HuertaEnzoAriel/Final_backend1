<script>
  import './Footer.css'
  import Logo from '../logo/Logo.svelte'
  import { SITIO } from '../../models/textos/sitio.js'

  const anio = new Date().getFullYear()
</script>

<footer class="footer">
  <div class="footer__contenido">
    <p class="footer__marca" aria-label={SITIO.NOMBRE}>
      <Logo chico />
    </p>
    <p class="footer__texto">{SITIO.LEMA} · {anio}</p>
  </div>
</footer>
