<script>
  import './App.css'
  import Layout from './components/layout/Layout.svelte'
  import Router from './components/router/Router.svelte'
  import { avisarActividad } from './services/api.js'
</script>

<!-- Cualquier clic o tecla cuenta como actividad (apunte §10.1). -->
<svelte:window onclick={avisarActividad} onkeydown={avisarActividad} />

<div class="app">
  <Layout>
    <Router />
  </Layout>
</div>
