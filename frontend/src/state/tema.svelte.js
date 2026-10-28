import { TEMA } from '../models/tema/tema.js'

function temaInicial() {
  const guardado = localStorage.getItem(TEMA.CLAVE_STORAGE)
  if (guardado === TEMA.CLARO || guardado === TEMA.OSCURO) return guardado
  return window.matchMedia('(prefers-color-scheme: dark)').matches ? TEMA.OSCURO : TEMA.CLARO
}

// Estado global compartido (apunte §6.2).
export const tema = $state({ actual: temaInicial() })

/** @param {string} valor */
function aplicar(valor) {
  document.documentElement.classList.toggle(TEMA.CLASE_OSCURO, valor === TEMA.OSCURO)
}

export function alternarTema() {
  tema.actual = tema.actual === TEMA.OSCURO ? TEMA.CLARO : TEMA.OSCURO
  localStorage.setItem(TEMA.CLAVE_STORAGE, tema.actual)
  aplicar(tema.actual)
}

aplicar(tema.actual)
