<script>
  import './Aviso.css'
  import Boton from '../boton/Boton.svelte'

  // Mensaje de estado (cargando, vacío o error). Con `alReintentar` muestra el botón (apunte §8.2).
  let { texto = '', tipo = 'info', alReintentar = undefined } = $props()
</script>

<div class="aviso aviso--{tipo}" role={tipo === 'error' ? 'alert' : 'status'}>
  <p>{texto}</p>
  {#if alReintentar}
    <Boton variante="secundario" onclick={alReintentar}>Reintentar</Boton>
  {/if}
</div>
