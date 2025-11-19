<?php
include '../PHP/conexion.php';
$result = $conn->query("SELECT c.id, u.nombre AS usuario, c.fecha, c.hora, c.estado, c.archivo_pdf 
                        FROM citas c 
                        JOIN usuarios u ON c.usuario_id = u.id 
                        ORDER BY c.fecha DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel de Citas - Administrador</title>
  <link rel="stylesheet" href="../CSS/admin_citas.css" />
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
  <header>
    <div class="contenedor-header">
      <h1><i data-lucide="calendar"></i> Administración de Citas</h1>
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
    <section class="blur-box panel-izquierdo">
      <div class="acciones-superiores" style="justify-content: center;">
        <button class="btn-icon" onclick="exportarPDF('todas')" title="Exportar todas">
          <i data-lucide="file-text"></i>
        </button>
        <button class="btn-icon" onclick="exportarPDF('confirmadas')" title="Citas confirmadas">
          <i data-lucide="check-circle"></i>
        </button>
        <button class="btn-icon" onclick="exportarPDF('pendientes')" title="Citas pendientes">
          <i data-lucide="clock"></i>
        </button>
      </div>
      <h2>Vista general</h2>
      <p>En este panel puedes visualizar todas las citas registradas. Solo el administrador puede eliminar citas o exportarlas a PDF.</p>
    </section>

    <!-- Panel derecho -->
    <section class="blur-box panel-derecho scroll-tabla">
      <table class="tabla-estilizada">
        <thead>
          <tr>
            <th>Usuario</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
            <th>PDF</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="fade-in" data-id="<?= $row['id'] ?>">
              <td><?= htmlspecialchars($row['usuario']) ?></td>
              <td><?= $row['fecha'] ?></td>
              <td><?= $row['hora'] ?></td>
              <td><?= ucfirst($row['estado']) ?></td>
              <td>
  <a href="../archivos/<?= $row['archivo_pdf'] ?>" download class="btn-descargar-pdf">
    Descargar PDF
  </a>
</td>

       
              <td>
                <a href="#" class="btn-eliminar" onclick="abrirModalEliminar(<?= $row['id'] ?>)" title="Eliminar">
                  <i data-lucide="trash"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </main>

  <!-- Modal de confirmación -->
  <div id="modalEliminar" class="modal-confirmacion" style="display:none;">
    <h2>¿Estás seguro de eliminar esta cita?</h2>
    <button class="btn-eliminar" onclick="confirmarEliminar()">Confirmar</button>
    <button class="btn-cancelar" onclick="cerrarModalEliminar()">Cancelar</button>
  </div>

  <footer></footer>

  <script src="../JS/mensajes.js"></script>
  <script>
    lucide.createIcons();
    let idCitaAEliminar = null;

    function abrirModalEliminar(id) {
      idCitaAEliminar = id;
      document.getElementById("modalEliminar").style.display = "block";
    }

    function cerrarModalEliminar() {
      document.getElementById("modalEliminar").style.display = "none";
      idCitaAEliminar = null;
    }

    function confirmarEliminar() {
      if (!idCitaAEliminar) return;
      fetch(`../PHP/eliminar_cita.php?id=${idCitaAEliminar}`)
        .then(res => res.text())
        .then(() => {
          document.querySelector(`tr[data-id="${idCitaAEliminar}"]`).remove();
          cerrarModalEliminar();
          mostrarMensaje("Cita eliminada");
        });
    }

    function exportarPDF(tipo) {
      window.location.href = `../PHP/exportar_citas.php?filtro=${tipo}`;
    }
  </script>
</body>
</html>
