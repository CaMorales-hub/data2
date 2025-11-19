<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel de Administración - Productos</title>
  <link rel="stylesheet" href="../CSS/admin_prod.css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
  <script>Chart.register(ChartDataLabels);</script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

</head>
<body>
  <header>
    <div class="contenedor-header">
      <h1><i data-lucide="box" class="icon"></i> Administración de Productos</h1>
      <nav>
        
          <a href="administrador.php"><i data-lucide="users"></i>Usuarios</a>
        <a href="productos.php"><i data-lucide="box"></i>Productos</a>
        <a href="cotizaciones.php"><i data-lucide="file-text"></i>Cotizaciones</a>
        <a href="admin_citas.php"><i data-lucide="users"></i>Citas</a>
        <a href="../HTML/login.html"><i data-lucide="log-out"></i>Salir</a>
      </nav>
    </div>
  </header>

  <main class="dashboard dashboard-expandible" id="contenedorPrincipal">
    <!-- Formulario -->
    <section class="blur-box izquierda" style="display: block;">
      <div class="acciones-superiores">
<button onclick="mostrarLista(); document.getElementById('btnBusqueda').classList.remove('disabled');" class="btn-icon" title="Ver Lista">
  <i data-lucide="list"></i>
</button>
        <button onclick="window.print()" class="btn-icon" title="Imprimir Reporte"><i data-lucide="download"></i></button>
        <button id="btnBusqueda" onclick="toggleBusqueda()" class="btn-icon" title="Buscar Producto"><i data-lucide="search"></i></button>
        <button onclick="mostrarEstadisticas()" class="btn-icon" title="Ver Estadísticas"><i data-lucide="bar-chart-2"></i></button>
        <button onclick="exportarExcel()" class="btn-icon" title="Exportar Excel"><i data-lucide="file-down"></i></button>
      </div>

      <div class="busqueda-producto" id="busquedaProducto">
        <input type="text" id="inputBusqueda" placeholder="Buscar por nombre..." oninput="filtrarProductos()" />
      </div>

      <h2>Agregar Producto</h2>
      <form id="formAgregarProducto" enctype="multipart/form-data">
        <input type="text" name="nombre" placeholder="Nombre del producto" required />
        <textarea name="descripcion" placeholder="Descripción" required></textarea>
        <input type="number" name="precio" placeholder="Precio" required min="0" step="0.01" />
        <input type="number" name="stock" placeholder="Cantidad en stock" required min="0" step="1" />
        <input type="file" name="imagen" accept="image/*" required />
        <button type="submit">Guardar</button>
      </form>
    </section>

    <!-- Tabla de productos -->
    <section class="blur-box derecha visible" id="panelProductos">
  <h2>Lista de Productos</h2> <!-- AQUI DEBE IR el contenedor scroll-tabla -->
  <div class="scroll-tabla">
    <table>
      <thead>
        <tr>
          <th>Imagen</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="tablaProductos">
        <!-- Productos insertados por JS -->
      </tbody>
    </table>
  </div>
</section>


    <!-- Gráficas -->
<section class="blur-box derecha" id="graficaEstadisticas" style="display: none;">
  <h2>Estadísticas de Productos</h2>
  <div class="carrusel-graficas">
    <button class="btn-carrusel" onclick="cambiarGrafica(-1)">&#60;</button>

    <div class="contenedor-graficas">
      <canvas id="graficaMasVendidos" class="grafica-slide visible"></canvas>
      <canvas id="graficaSinVentas" class="grafica-slide"></canvas>
      <canvas id="graficaTotalVentas" class="grafica-slide"></canvas>
      <canvas id="graficaComparativa" class="grafica-slide"></canvas>
    </div>

    <button class="btn-carrusel" onclick="cambiarGrafica(1)">&#62;</button>
  </div>
</section>

  </main>

  <!-- MODAL EDITAR PRODUCTO -->
  <div id="modalEditar" class="blur-box modal-editar" style="display: none;">
    <h2>Editar Producto</h2>
    <form id="formEditarProducto">
      <input type="hidden" name="id" id="edit-id" />
      <input type="text" name="nombre" id="edit-nombre" placeholder="Nombre" required />
      <textarea name="descripcion" id="edit-descripcion" placeholder="Descripción" required></textarea>
      <input type="number" name="precio" id="edit-precio" placeholder="Precio" required min="0" step="0.01" />
      <input type="number" name="stock" id="edit-stock" placeholder="Stock" required min="0" />
      <div style="text-align: center; margin-top: 10px;">
        <button type="submit">Actualizar</button>
        <button type="button" onclick="cerrarModalEditar()" style="margin-left: 10px;">Cancelar</button>
      </div>
    </form>
  </div>


  <!-- Modal personalizado para confirmar eliminación -->
<div id="modalConfirmarEliminar" class="modal-confirmacion" style="display: none;">
  <h2 id="textoConfirmar">¿Estás seguro de eliminar este producto?</h2>
  <div style="margin-top: 20px; display: flex; justify-content: center; gap: 20px;">
    <button onclick="confirmarEliminarProducto()" style="padding: 10px 20px; background-color: var(--rojo); color: white; border: none; border-radius: 10px; cursor: pointer;">Sí, eliminar</button>
    <button onclick="cerrarModalConfirmar()" style="padding: 10px 20px; background-color: var(--gris-claro); color: var(--fondo-oscuro); border: none; border-radius: 10px; cursor: pointer;">Cancelar</button>
  </div>
</div>


<!-- Modal visual de confirmación de éxito o error -->
<div id="mensajeConfirmacion" class="modal-confirmacion" style="display: none;">
  <h2 id="textoConfirmacion">Mensaje aquí</h2>
</div>


  <!-- Scripts separados cargados en orden -->
<!-- Scripts separados cargados en orden -->
<script src="../JS/productos_tabla.js"></script>
<script src="../JS/productos_graficas.js"></script>
<script src="../JS/productos_editar.js"></script>
<script src="../JS/productos_agregar.js"></script>
<script src="../JS/eliminar_producto.js"></script>

<script src="../JS/exportar_excel.js"></script>


<!-- Este script asegura que mostrarLista() se ejecute al final -->
<script>
  window.addEventListener("DOMContentLoaded", () => {
    if (typeof mostrarLista === "function") {
      setTimeout(() => {
        mostrarLista();
        // ✅ Opcional: solo si quieres asegurar carga inicial
        // cargarProductos();
      }, 100);
    } else {
      console.error("mostrarLista() no está disponible.");
    }
  });
</script>

  <footer>

  </footer>

</body>
</html>