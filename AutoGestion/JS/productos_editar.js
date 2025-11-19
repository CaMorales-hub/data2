document.getElementById("formEditarProducto").addEventListener("submit", function (e) {
  e.preventDefault();
  const form = e.target;
  const datos = new FormData(form);

  fetch("../PHP/editar_producto.php", {
    method: "POST",
    body: datos
  })
  .then(res => res.text())
  .then(respuesta => {
    if (respuesta.trim() === "ok") {
      mostrarConfirmacion("Producto actualizado correctamente");
      cerrarModalEditar();
      cargarProductos();
    } else {
      mostrarConfirmacion("Error al actualizar: " + respuesta, true);
    }
  })
  .catch(err => {
    mostrarConfirmacion("Error en la solicitud: " + err, true);
  });
});

function mostrarConfirmacion(mensaje, error = false) {
  const modal = document.getElementById("mensajeConfirmacion");
  const texto = document.getElementById("textoConfirmacion");
  texto.textContent = mensaje;
  texto.style.color = error ? "var(--rojo)" : "white";
  modal.style.display = "block";

  if (!error) {
    setTimeout(() => {
      modal.style.display = "none";
    }, 2000); // solo 2 segundos
  }
}
