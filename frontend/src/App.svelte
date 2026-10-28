<script>
  import './App.css'
  import Layout from './lib/layout/Layout.svelte'
  import Router from './lib/router/Router.svelte'
  import { avisarActividad } from './services/api.js'
</script>

<!-- Cualquier clic o tecla cuenta como actividad (apunte §10.1). -->
<svelte:window onclick={avisarActividad} onkeydown={avisarActividad} />

<div class="app">
  <Layout>
    <Router />
  </Layout>
</div>
