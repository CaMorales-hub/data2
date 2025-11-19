document.getElementById("formAgregarProducto").addEventListener("submit", function (e) {
  e.preventDefault();
  const form = e.target;
  const datos = new FormData(form);

  fetch("../PHP/agregar_producto.php", {
    method: "POST",
    body: datos
  })
  .then(res => res.text())
  .then(respuesta => {
    if (respuesta.trim() === "ok") {
      mostrarConfirmacion("Producto guardado correctamente");
      form.reset();
      cargarProductos();
    } else {
      mostrarConfirmacion("Error al guardar: " + respuesta, true);
    }
  })
  .catch(err => {
    mostrarConfirmacion("Error en la solicitud: " + err, true);
  });
});

