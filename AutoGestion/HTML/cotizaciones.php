<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración - Cotizaciones</title>
  <link rel="stylesheet" href="../CSS/cotizaciones.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
  <header>
    <div class="contenedor-header">
      <h1><i data-lucide="file-text"></i> Administración de Cotizaciones</h1>
      <nav>
               <a href="administrador.php"><i data-lucide="users"></i>Usuarios</a>
        <a href="productos.php"><i data-lucide="box"></i>Productos</a>
        <a href="cotizaciones.php"><i data-lucide="file-text"></i>Cotizaciones</a>
        <a href="admin_citas.php"><i data-lucide="users"></i>Citas</a>
        <a href="../HTML/login.html"><i data-lucide="log-out"></i>Salir</a>
      </nav>
    </div>
  </header>

  <main class="dashboard">
    <!-- Panel izquierdo -->
    <section class="izquierda">
      <div class="blur-box">
        <div class="acciones-superiores">
          <button class="btn-icon" onclick="exportarReporte('todas')" title="Exportar todas">
            <i data-lucide="file-down"></i>
          </button>
          <button class="btn-icon" onclick="exportarReporte('pendientes')" title="Exportar pendientes">
            <i data-lucide="calendar-clock"></i>
          </button>
          <button class="btn-icon" onclick="exportarReporte('respondidas')" title="Exportar respondidas">
            <i data-lucide="calendar-check"></i>
          </button>
        </div>
        <h2>Vista general</h2>
        <p>Panel de revisión de cotizaciones recibidas. Los archivos enviados por los clientes están disponibles para descarga directa.</p>
      </div>
    </section>

    <!-- Panel derecho -->
    <section class="derecha visible">
      <div class="blur-box">
        <h2>Lista de Cotizaciones</h2>
        <div class="scroll-tabla">
          <table>
            <thead>
              <tr>
                <th>Usuario</th>
                <th>Archivo</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php
              require '../PHP/conexion.php';
              $sql = "SELECT c.*, u.nombre FROM cotizaciones c
                      JOIN usuarios u ON c.usuario_id = u.id
                      ORDER BY c.creado_en DESC";
              $result = $conn->query($sql);
              while ($row = $result->fetch_assoc()) {
                $archivo = htmlspecialchars($row['archivo_pdf']);
                echo "<tr class='fade-in'>
                        <td>" . htmlspecialchars($row['nombre']) . "</td>
                        <td><a href='../archivos/$archivo' download>Descargar PDF</a></td>
                        <td>" . htmlspecialchars($row['estado']) . "</td>
                        <td>" . htmlspecialchars($row['creado_en']) . "</td>
                        <td>
                          <a href='#' class='btn-eliminar' onclick='confirmarEliminarCotizacion({$row["id"]})' title='Eliminar'>
                            <i data-lucide=\"trash\"></i>
                          </a>
                        </td>
                      </tr>";
              }
              $conn->close();
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <!-- Modal de confirmación -->
  <div id="modal-confirmar" class="modal-confirmacion" style="display: none;">
    <h2>¿Confirmar eliminación de esta cotización?</h2>
    <div class="acciones-superiores">
      <button class="btn-confirmar" onclick="eliminarCotizacionConfirmada()">Confirmar</button>
      <button class="btn-cancelar" onclick="cerrarModalConfirmar()">Cancelar</button>
    </div>
  </div>

  <!-- Mensaje tipo toast -->
  <div id="mensajeConfirmacion" class="mensaje-confirmacion" style="display: none;">
    <div class="contenido-confirmacion">
      <p id="textoConfirmacion"></p>
      <button onclick="cerrarConfirmacion()">&times;</button>
    </div>
  </div>

  <script src="../JS/mensajes.js"></script>
  <script>
    let idEliminar = null;

    function confirmarEliminarCotizacion(id) {
      idEliminar = id;
      document.getElementById("modal-confirmar").style.display = "block";
    }

    function eliminarCotizacionConfirmada() {
      if (idEliminar !== null) {
        window.location.href = "../PHP/eliminar_cotizacion.php?id=" + idEliminar;
      }
    }

    function cerrarModalConfirmar() {
      document.getElementById("modal-confirmar").style.display = "none";
      idEliminar = null;
    }

    function exportarReporte(tipo) {
      window.location.href = `../PHP/generar_reporte_cotizacion.php?tipo=${tipo}`;
    }

    lucide.createIcons();
  </script>

  <?php if (isset($_GET['msg'])): ?>
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        mostrarMensaje("<?= htmlspecialchars($_GET['msg']) ?>");
      });
    </script>
  <?php endif; ?>

    <footer>

  </footer>

</body>
</html>
