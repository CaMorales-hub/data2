function cargarProductos() {
  fetch('../PHP/obtener_productos.php')
    .then(res => res.text())
    .then(html => {
      document.getElementById('tablaProductos').innerHTML = html;
      if (typeof lucide !== "undefined") lucide.createIcons();
    })
    .catch(err => console.error('Error al cargar productos:', err));
}

function mostrarLista() {
  const panelProductos = document.getElementById("panelProductos");
  const panelGraficas = document.getElementById("graficaEstadisticas");

  // Mostrar tabla
  panelProductos.style.display = "block";
  panelProductos.classList.add("visible");

  // Ocultar gráficas
  panelGraficas.style.display = "none";
  panelGraficas.classList.remove("visible");

  // Reactivar búsqueda
  const btnBusqueda = document.getElementById("btnBusqueda");
  btnBusqueda.classList.remove("disabled");

  // Ocultar input de búsqueda
  const busqueda = document.getElementById("busquedaProducto");
  busqueda.style.display = "none";

  // ✅ Cargar productos aquí
  cargarProductos();
}


function toggleBusqueda() {
  const busqueda = document.getElementById("busquedaProducto");
  const panelProductos = document.getElementById("panelProductos");

  // Solo mostrar si el panel de productos está activo
  if (panelProductos.classList.contains("visible")) {
    busqueda.style.display = (busqueda.style.display === "block") ? "none" : "block";
  } else {
    // Si no está visible el panel de productos, ocultar el input si estuviera abierto
    busqueda.style.display = "none";
  }
}


function filtrarProductos() {
  const filtro = document.getElementById("inputBusqueda").value.toLowerCase();
  document.querySelectorAll("#tablaProductos tr").forEach(fila => {
    const nombre = fila.children[1].textContent.toLowerCase();
    fila.style.display = nombre.includes(filtro) ? "" : "none";
  });
}

function eliminarProducto(id) {
  if (!confirm("¿Estás seguro de eliminar este producto?")) return;

  fetch('../PHP/eliminar_producto.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id=' + encodeURIComponent(id)
  })
  .then(res => res.json())
  .then(respuesta => {
    if (respuesta.success) {
      const fila = document.querySelector(`tr[data-id="${id}"]`);
      if (fila) {
        fila.classList.add('fade-out');
        setTimeout(() => fila.remove(), 400);
      } else {
        cargarProductos();
      }
    } else {
      alert("Error al eliminar: " + (respuesta.error || ''));
    }
  })
  .catch(err => alert("Error en la solicitud: " + err));
}
function abrirModalEditar(id, nombre, descripcion, precio, stock) {
  document.getElementById("edit-id").value = id;
  document.getElementById("edit-nombre").value = nombre;
  document.getElementById("edit-descripcion").value = descripcion;
  document.getElementById("edit-precio").value = precio;
  document.getElementById("edit-stock").value = stock;
  document.getElementById("modalEditar").style.display = "block";
}

function cerrarModalEditar() {
  document.getElementById("modalEditar").style.display = "none";
}
