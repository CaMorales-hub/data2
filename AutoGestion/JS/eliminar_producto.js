let productoAEliminarId = null;

function eliminarProducto(id) {
  productoAEliminarId = id;
  document.getElementById("modalConfirmarEliminar").style.display = "block";
}

function confirmarEliminarProducto() {
  if (!productoAEliminarId) return;

  fetch("../PHP/eliminar_producto.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `id=${productoAEliminarId}`
  })
  .then(res => res.json())
  .then(data => {
    cerrarModalConfirmar();

    if (data.success) {
      mostrarConfirmacion("Producto eliminado correctamente.");

      const fila = document.querySelector(`tr[data-id='${productoAEliminarId}']`);
      if (fila) {
        fila.classList.add("fade-out");
        setTimeout(() => fila.remove(), 500);
      }
    } else {
      mostrarConfirmacion("Error: " + (data.error || "No se pudo eliminar."), true);
    }

    productoAEliminarId = null;
  })
  .catch(error => {
    cerrarModalConfirmar();
    mostrarConfirmacion("Error del servidor o conexión.", true);
    console.error(error);
    productoAEliminarId = null;
  });
}

function cerrarModalConfirmar() {
  document.getElementById("modalConfirmarEliminar").style.display = "none";
}

function mostrarConfirmacion(mensaje, error = false) {
  const modal = document.getElementById("mensajeConfirmacion"); // ✅ CORREGIDO
  const texto = document.getElementById("textoConfirmacion");
  texto.textContent = mensaje;
  texto.style.color = error ? "var(--rojo)" : "white";
  modal.style.display = "block";

  if (!error) {
    setTimeout(() => {
      modal.style.display = "none";
    }, 800);
  }
}

function cerrarConfirmacion() {
  document.getElementById("mensajeConfirmacion").style.display = "none"; // ✅ CORREGIDO
}
