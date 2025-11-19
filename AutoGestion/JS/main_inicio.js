document.addEventListener("DOMContentLoaded", () => {
  if (typeof mostrarLista === "function") {
    mostrarLista();
  } else {
    console.error("mostrarLista() no está definida.");
  }

  if (typeof lucide !== "undefined") {
    lucide.createIcons();
  }
});