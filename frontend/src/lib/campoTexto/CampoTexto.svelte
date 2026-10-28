<script>
  import './CampoTexto.css'

  // Campo de formulario con etiqueta y error de validación. El valor se enlaza con bind:value.
  /**
   * @type {{
   *   nombre: string,
   *   etiqueta?: string,
   *   value?: string,
   *   type?: 'text' | 'email' | 'password',
   *   autocomplete?: import('svelte/elements').HTMLInputAttributes['autocomplete'],
   *   error?: string,
   * }}
   */
  let { nombre, etiqueta = '', value = $bindable(''), type = 'text', autocomplete = undefined, error = '' } = $props()
</script>

<div class="campo">
  <label class="campo__etiqueta" for={nombre}>{etiqueta}</label>
  <input
    class="campo__control"
    class:campo__control--error={!!error}
    id={nombre}
    name={nombre}
    {type}
    {autocomplete}
    aria-invalid={!!error}
    required
    bind:value
  />
  {#if error}
    <p class="campo__error">{error}</p>
  {/if}
</div>
