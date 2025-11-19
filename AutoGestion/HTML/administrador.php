<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de Administración - Usuarios</title>
  <link rel="stylesheet" href="../CSS/admin.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
  <header>
    <div class="contenedor-header">
      <h1><i data-lucide="users"></i> Administración de Usuarios</h1>
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

    <!-- Botones de reportes en la parte superior -->
    <div class="acciones-superiores">
      <button class="btn-icon" title="Usuarios registrados" onclick="descargarPDF('todos')">
        <i data-lucide="file-text"></i>
      </button>
      <button class="btn-icon" title="Citas programadas" onclick="descargarPDF('programadas')">
        <i data-lucide="calendar-check"></i>
      </button>
      <button class="btn-icon" title="Citas pendientes" onclick="descargarPDF('pendientes')">
        <i data-lucide="calendar-clock"></i>
      </button>
      <button class="btn-icon" title="Cotizaciones sin confirmar" onclick="descargarPDF('cotizaciones')">
        <i data-lucide="file-search"></i>
      </button>
    </div>

    <h2>Agregar Usuario</h2>
    <form method="POST" id="formAgregarUsuario" action="../PHP/guardar_usuario.php">
      <input type="text" name="nombre" placeholder="Nombre completo" required>
      <input type="email" name="correo" placeholder="Correo electrónico" required>
      <input type="password" name="contrasena" placeholder="Contraseña" required>
      <button type="submit">Guardar</button>
    </form>

  </div>
</section>


    <!-- Panel derecho -->
    <section class="derecha visible" id="panelUsuarios">
      <div class="blur-box">
        <h2>Lista de Usuarios</h2>
        <div class="scroll-tabla">
          <table>
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Contraseña</th>
                <th>Creado en</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php
              require '../PHP/conexion.php';
              $sql = "SELECT * FROM usuarios ORDER BY creado_en DESC";
              $result = $conn->query($sql);
              while ($row = $result->fetch_assoc()) {
                  echo "<tr class='fade-in' data-id='{$row['id']}'>
                          <td>" . htmlspecialchars($row['nombre']) . "</td>
                          <td>" . htmlspecialchars($row['correo']) . "</td>
                          <td>" . htmlspecialchars($row['contrasena']) . "</td>
                          <td>" . htmlspecialchars($row['creado_en']) . "</td>
                          <td>
                            <a href='#' class='btn-editar' onclick='abrirModalEditar(
                              {$row["id"]},
                              " . json_encode($row["nombre"]) . ",
                              " . json_encode($row["correo"]) . ",
                              " . json_encode($row["contrasena"]) . "
                            )' title='Editar'><i data-lucide=\"edit\"></i></a>
                            <a href='#' class='btn-eliminar' onclick='confirmarEliminarUsuario({$row["id"]})' title='Eliminar'><i data-lucide=\"trash\"></i></a>
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

  <!-- Modal de edición -->
  <div id="modal-editar" class="modal-editar" style="display: none;">
    <h2>Editar Usuario</h2>
    <form id="formEditar">
      <input type="hidden" name="id" id="edit-id">
      <input type="text" name="nombre" id="edit-nombre" placeholder="Nombre completo" required>
      <input type="email" name="correo" id="edit-correo" placeholder="Correo electrónico" required>
      <input type="text" name="contrasena" id="edit-contrasena" placeholder="Contraseña" required>
      <div class="botones">
        <button type="submit" class="btn-guardar">Guardar Cambios</button>
        <button type="button" class="btn-cancelar" onclick="cerrarModalEditar()">Cancelar</button>
      </div>
    </form>
  </div>

<!-- Modal de confirmación -->
<div id="modal-confirmar" class="modal-confirmacion" style="display: none;">
  <h2>¿Estás seguro de eliminar este usuario?</h2>
  <div style="margin-top: 20px; display: flex; justify-content: center; gap: 20px;">
    <button class="btn-confirmar" onclick="eliminarUsuarioConfirmado()">Confirmar</button>
    <button class="btn-cancelar" onclick="cerrarModalConfirmar()">Cancelar</button>
  </div>
</div>


  <!-- Modal de mensaje -->
  <div id="mensajeConfirmacion" class="modal-confirmacion" style="display: none;">
    <h2 id="textoConfirmacion">Mensaje</h2>
  </div>

  <footer></footer>

  <script>
    let idEliminar = null;

    function abrirModalEditar(id, nombre, correo, contrasena) {
      document.getElementById("edit-id").value = id;
      document.getElementById("edit-nombre").value = nombre;
      document.getElementById("edit-correo").value = correo;
      document.getElementById("edit-contrasena").value = contrasena;
      document.getElementById("modal-editar").style.display = "block";
    }

    function cerrarModalEditar() {
      document.getElementById("modal-editar").style.display = "none";
    }

    function confirmarEliminarUsuario(id) {
      idEliminar = id;
      document.getElementById("modal-confirmar").style.display = "block";
    }

    function cerrarModalConfirmar() {
      document.getElementById("modal-confirmar").style.display = "none";
      idEliminar = null;
    }

    function eliminarUsuarioConfirmado() {
      if (idEliminar !== null) {
        fetch(`../PHP/eliminar_usuario.php?id=${idEliminar}`)
          .then(res => res.text())
          .then(msg => {
            mostrarMensajeConfirmacion(msg);
            cerrarModalConfirmar();
            setTimeout(() => location.reload(), 1000);
          });
      }
    }

    function descargarPDF(tipo) {
      window.location.href = `../PHP/generar_reporte.php?tipo=${tipo}`;
    }

    // Mensaje modal flotante
    function mostrarMensajeConfirmacion(texto) {
      const modal = document.getElementById("mensajeConfirmacion");
      const contenedor = document.getElementById("textoConfirmacion");
      contenedor.textContent = texto;
      modal.style.display = "block";
      setTimeout(() => {
        modal.style.display = "none";
      }, 1000);
    }

    // Guardar nuevo usuario
    document.getElementById("formAgregarUsuario").addEventListener("submit", function(e) {
      e.preventDefault();
      const datos = new FormData(this);

      fetch('../PHP/guardar_usuario.php', {
        method: 'POST',
        body: datos
      })
      .then(res => res.text())
      .then(msg => {
        mostrarMensajeConfirmacion(msg);
        this.reset();
        setTimeout(() => location.reload(), 1000);
      });
    });

    // Guardar edición
    document.getElementById("formEditar").addEventListener("submit", function(e) {
      e.preventDefault();
      const datos = new FormData(this);

      fetch('../PHP/editar_usuario.php', {
        method: 'POST',
        body: datos
      })
      .then(res => res.text())
      .then(msg => {
        mostrarMensajeConfirmacion(msg);
        cerrarModalEditar();
        setTimeout(() => location.reload(), 1000);
      });
    });

    lucide.createIcons();
  </script>
</body>
</html>
