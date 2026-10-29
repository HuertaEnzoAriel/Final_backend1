<script>
  import './Logo.css'
  import { SITIO } from '../../models/textos/sitio.js'

  // `chico` achica el logo (se usa en el footer).
  let { chico = false } = $props()
</script>

<span class="logo" class:logo--chico={chico}>
  <!-- Isotipo: chevrons < > en acento y la "P" en el color del texto, así sigue al tema. -->
  <svg class="logo__icono" viewBox="370 170 655 512" aria-hidden="true">
    <path
      class="logo__acento"
      d="M582 200 L392 333 Q377 345 377 366 Q377 386 392 398 L582 535 L582 470 Q582 462 575 457 L449 368 L575 278 Q582 273 582 265 Z"
    />
    <path
      class="logo__acento"
      d="M624 178 L847 334 Q862 346 862 366 Q862 386 847 398 L624 557 L624 492 Q624 484 632 479 L790 368 L632 254 Q624 249 624 240 Z"
    />
    <path class="logo__base" d="M625 287 L689 332 L689 402 L625 448 Z" />
    <path
      class="logo__base"
      d="M703 194 L848 194 A165 173 0 0 1 848 540 L703 540 L790 477 L845 477 A95 110 0 0 0 845 258 L790 258 Z"
    />
    <path class="logo__base" d="M625 595 L686 550 L689 552 L689 676 L625 676 Z" />
  </svg>

  <span class="logo__texto">
    <span class="logo__palabra logo__palabra--acento">{SITIO.MARCA_ACENTO}</span>
    <span class="logo__palabra">{SITIO.MARCA_BASE}</span>
  </span>
</span>
