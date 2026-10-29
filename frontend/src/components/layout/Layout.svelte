<script>
  import './Layout.css'
  import Navbar from '../navbar/Navbar.svelte'
  import Footer from '../footer/Footer.svelte'

  // children: la página actual que pone App (apunte §5.3).
  let { children } = $props()
</script>

<div class="layout">
  <Navbar />
  <main class="layout__contenido">
    {@render children?.()}
  </main>
  <Footer />
</div>
