<?php
require '../PHP/conexion.php';
$result = $conn->query("SELECT c.*, u.nombre FROM cotizaciones c 
                        JOIN usuarios u ON c.usuario_id = u.id 
                        ORDER BY c.creado_en DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel de Cotizaciones - Personal</title>
  <link rel="stylesheet" href="../CSS/cotizaciones.css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
.icono-accion {
  padding: 6px;
  border-radius: 8px;
  margin: 0 4px;
  display: inline-flex;
  transition: background 0.3s;
}

.ver-pdf {
  background: rgba(255, 248, 45, 0.69); /* Azul cielo claro */
}
.responder {
  background: rgba(79, 217, 5, 0.66); /* Verde menta */
}
.ver-respuesta {
  background: rgba(0, 98, 255, 0.49); /* Amarillo suave */
}

.icono-accion:hover {
  filter: brightness(1.1);
  cursor: pointer;
}


    #modalConfirmacion {
      position: fixed;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(30, 64, 175, 0.95);
      color: white;
      padding: 20px 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.5);
      z-index: 9999;
      display: none;
    }

    #modalConfirmacion h2 {
      margin-bottom: 10px;
      text-align: center;
    }

    #modalConfirmacion button {
      background: white;
      color: #1e3a8a;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      display: block;
      margin: 10px auto 0;
    }
  </style>
</head>
<body>
  <header>
    <div class="contenedor-header">
      <h1><i data-lucide="file-text"></i> Cotizaciones - Personal</h1>
      <nav>
 <a href="cotizaciones_personal.php"><i data-lucide="file-text"></i>Cotizaciones</a>
<a href="personal_citas.php"><i data-lucide="calendar"></i>Citas</a>
<a href="login.html"><i data-lucide="log-out"></i>Salir</a>

      </nav>
    </div>
  </header>

  <main class="dashboard">
    <!-- Panel izquierdo -->
    <section class="izquierda">
      <div class="blur-box" id="contenedorVista">
        <h2>Vista previa</h2>
        <p>Selecciona una cotización o respuesta para visualizar el PDF.</p>
        <iframe id="visorPDF" style="width:100%; height:400px; display:none;" frameborder="0"></iframe>
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
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="fade-in">
                  <td><?= htmlspecialchars($row['nombre']) ?></td>
                  <td><?= htmlspecialchars($row['estado']) ?></td>
                  <td><?= htmlspecialchars($row['creado_en']) ?></td>
                  <td>
                    <a href="#" class="icono-accion ver-pdf" onclick="verPDF('../archivos/<?= $row['archivo_pdf'] ?>')" title="Ver cotización">
                      <i data-lucide="eye"></i>
                    </a>

                    <?php if ($row['estado'] === 'pendiente'): ?>
                      <a href="#" class="icono-accion responder" onclick="abrirModalResponder(<?= $row['id'] ?>)" title="Responder">
                        <i data-lucide="send"></i>
                      </a>
                    <?php elseif ($row['estado'] === 'respondida' && $row['archivo_respuesta_pdf']): ?>
                      <a href="#" class="icono-accion ver-respuesta" onclick="verPDF('../archivos/<?= $row['archivo_respuesta_pdf'] ?>')" title="Ver respuesta">
                        <i data-lucide="file-text"></i>
                      </a>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <!-- Modal para responder cotización -->
  <div id="modalResponder" class="modal-confirmacion" style="display: none;">
    <h2>Responder Cotización</h2>
    <form id="formRespuesta">
      <input type="hidden" name="id_cotizacion" id="idCotizacion">
      <input type="text" name="costo" placeholder="Costo estimado" required>
      <input type="text" name="direccion" placeholder="Dirección del taller" required>
      <input type="text" name="tecnico" placeholder="Técnico asignado" required>
      <textarea name="observaciones" placeholder="Observaciones adicionales" required></textarea>
      <div class="acciones-superiores">
        <button type="submit" class="btn-confirmar">Enviar</button>
        <button type="button" class="btn-cancelar" onclick="cerrarModalResponder()">Cancelar</button>
      </div>
    </form>
  </div>

  <!-- Modal centrado de confirmación -->
  <div id="modalConfirmacion">
    <h2><i data-lucide="check-circle"></i> Cotización respondida</h2>
    <button onclick="cerrarConfirmacion()">Aceptar</button>
  </div>

  <script src="../JS/mensajes.js"></script>
  <script>
    lucide.createIcons();

    function verPDF(ruta) {
      const visor = document.getElementById("visorPDF");
      visor.src = ruta;
      visor.style.display = "block";
    }

    function abrirModalResponder(id) {
      document.getElementById("modalResponder").style.display = "block";
      document.getElementById("idCotizacion").value = id;
    }

    function cerrarModalResponder() {
      document.getElementById("modalResponder").style.display = "none";
      document.getElementById("formRespuesta").reset();
    }

    function cerrarConfirmacion() {
      document.getElementById("modalConfirmacion").style.display = "none";
    }

    document.getElementById("formRespuesta").addEventListener("submit", function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('../PHP/responder_cotizacion.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(res => {
        cerrarModalResponder();
        document.getElementById("modalConfirmacion").style.display = "block";
        lucide.createIcons();
        setTimeout(() => location.reload(), 1200);
      });
    });
  </script>

  <footer></footer>
</body>
</html>
