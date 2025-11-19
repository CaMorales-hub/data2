<?php
session_start();
require '../PHP/conexion.php';

if (!isset($_SESSION['personal_id'])) {
    header("Location: login.html");
    exit;
}

$personal_id = $_SESSION['personal_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil del Personal</title>
  <link rel="stylesheet" href="../CSS/personal_citas.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<header>
  <div class="contenedor-header">
    <h1><i data-lucide="user-check"></i> Perfil del Personal</h1>
    <nav>
<a href="cotizaciones_personal.php"><i data-lucide="file-text"></i>Cotizaciones</a>
<a href="personal_citas.php"><i data-lucide="calendar"></i>Citas</a>
<a href="login.html"><i data-lucide="log-out"></i>Salir</a>

    </nav>
    <a href="#" class="campana-noti" onclick="mostrarNotificaciones()">
      <i data-lucide="bell"></i>
      <span class="contador-noti" id="contadorNoti">0</span>
    </a>
  </div>
</header>


  <main class="dashboard">
    <section class="panel-izquierdo">
      <h2>Vista previa</h2>
      <div id="detalleCita">
      
      </div>
    </section>

    <section class="panel-derecho">
      <table class="tabla-estilizada">
        <thead>
          <tr>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $resultado = $conn->query("SELECT c.id, u.nombre AS usuario, c.fecha, c.hora, c.estado, c.archivo_pdf
                                     FROM citas c
                                     JOIN usuarios u ON c.usuario_id = u.id
                                     ORDER BY c.fecha DESC");

          while ($row = $resultado->fetch_assoc()):
          ?>
            <tr data-id="<?= $row['id'] ?>">
              <td><?= htmlspecialchars($row['usuario']) ?></td>
              <td><?= $row['fecha'] ?></td>
              <td><?= $row['hora'] ?></td>
              <td class="estado"><?= ucfirst($row['estado']) ?></td>
              <td>
                <button class="btn-editar" onclick="mostrarCita('<?= $row['archivo_pdf'] ?>')" title="Ver PDF">
                  <i data-lucide="eye"></i>
                </button>
                <button class="btn-confirmar" onclick="confirmarCita(<?= $row['id'] ?>)" title="Confirmar">
                  <i data-lucide="check-circle"></i>
                </button>
                <button class="btn-eliminar" onclick="abrirModalEliminar(<?= $row['id'] ?>)" title="Eliminar">
                  <i data-lucide="trash"></i>
                </button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </main>

  <footer></footer>

  <!-- Modal Notificaciones -->
  <div id="modalNotificaciones" class="modal-notificacion" style="display:none;">
    <div class="contenido-modal">
      <h2 style="text-align:center;"><i data-lucide="bell"></i> Notificaciones</h2>
      <ul id="listaNotificaciones"></ul>
      <button class="btn-confirmar" onclick="marcarNotificacionesLeidas()">Enterado</button>
    </div>
  </div>

  <!-- Modal Mensajes -->
  <div id="modalMensaje" class="modal-notificacion" style="display:none;">
    <div class="contenido-modal">
      <h2 id="mensajeTexto" style="text-align:center;"></h2>
    </div>
  </div>

  <!-- Modal Confirmar Eliminación -->
  <div id="modalEliminar" class="modal-notificacion" style="display:none;">
    <div class="contenido-modal">
      <h2 style="text-align:center;"><i data-lucide="alert-triangle"></i> Confirmar eliminación</h2>
      <p style="text-align:center;">¿Estás seguro de que deseas eliminar esta cita?</p>
      <div style="margin-top: 20px; display: flex; justify-content: center; gap: 10px;">
        <button class="btn-eliminar" onclick="confirmarEliminarCita()">Eliminar</button>
        <button class="btn-cancelar" onclick="cerrarModalEliminar()">Cancelar</button>
      </div>
    </div>
  </div>

  <script>
    lucide.createIcons();

    let citaIdEliminar = null;

    function mostrarCita(archivo) {
      const detalle = document.getElementById("detalleCita");
    detalle.innerHTML = `
  <iframe src="../archivos/${archivo}" width="100%" height="400px" frameborder="0"></iframe>
`;

    }

    function confirmarCita(id) {
      fetch(`../PHP/confirmar_cita.php?id=${id}`)
        .then(res => res.text())
        .then(() => {
          document.querySelector(`tr[data-id="${id}"] .estado`).textContent = "Confirmada";
          mostrarMensaje("Cita confirmada");
          actualizarNotificaciones();
        });
    }

    function abrirModalEliminar(id) {
      citaIdEliminar = id;
      document.getElementById("modalEliminar").style.display = "flex";
    }

    function cerrarModalEliminar() {
      document.getElementById("modalEliminar").style.display = "none";
      citaIdEliminar = null;
    }

    function confirmarEliminarCita() {
      if (citaIdEliminar) {
        fetch(`../PHP/eliminar_cita.php?id=${citaIdEliminar}`)
          .then(res => res.text())
          .then(() => {
            document.querySelector(`tr[data-id="${citaIdEliminar}"]`).remove();
            cerrarModalEliminar();
            mostrarMensaje("Cita eliminada");
          });
      }
    }

    function mostrarNotificaciones() {
      fetch("../PHP/obtener_notificaciones.php?receptor_tipo=personal")
        .then(response => response.json())
        .then(data => {
          const lista = document.getElementById("listaNotificaciones");
          lista.innerHTML = "";

          if (!Array.isArray(data) || data.length === 0) {
            lista.innerHTML = "<li>No hay notificaciones nuevas.</li>";
          } else {
            data.forEach(n => {
              const li = document.createElement("li");
              li.textContent = n.mensaje;
              lista.appendChild(li);
            });
          }

          document.getElementById("modalNotificaciones").style.display = "flex";
        });
    }

    function marcarNotificacionesLeidas() {
      fetch("../PHP/marcar_notificaciones_leidas.php")
        .then(response => response.text())
        .then(data => {
          if (data.trim() === "ok") {
            document.getElementById("modalNotificaciones").style.display = "none";
            document.getElementById("listaNotificaciones").innerHTML = "";
            document.getElementById("contadorNoti").textContent = "0";
          } else {
            mostrarMensaje("No se pudieron marcar como leídas.");
          }
        });
    }

 function mostrarMensaje(texto) {
  document.getElementById("mensajeTexto").textContent = texto;
  document.getElementById("modalMensaje").style.display = "flex";

  setTimeout(() => {
    document.getElementById("modalMensaje").style.display = "none";
  }, 1000); // Ocultar en 1 segundo
}

    function cerrarModalMensaje() {
      document.getElementById("modalMensaje").style.display = "none";
    }

  function actualizarNotificaciones() {
  fetch("../PHP/contar_notificaciones.php?receptor_tipo=personal")
    .then(res => res.text())
    .then(data => {
      const contador = document.getElementById("contadorNoti");
      const campana = document.querySelector('.campana-noti');

      if (parseInt(data) > 0) {
        contador.textContent = data;
        campana.classList.add("nueva");

        setTimeout(() => {
          campana.classList.remove("nueva");
        }, 800);
      } else {
        contador.textContent = "0";
      }
    });
}

    function actualizarTablaCitas() {
  fetch("../PHP/obtener_citas_personal.php")
    .then(res => res.text())
    .then(html => {
      document.querySelector("tbody").innerHTML = html;
      lucide.createIcons(); // recargar íconos
    });
}

setInterval(actualizarTablaCitas, 1000); // cada 5 segundos

    setInterval(actualizarNotificaciones, 1000);
  </script>
</body>
</html>
