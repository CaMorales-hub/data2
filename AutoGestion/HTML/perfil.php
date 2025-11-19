<?php
session_start();
require '../PHP/conexion.php';

if (!isset($_SESSION['cliente_id'])) {
    header("Location: login.html");
    exit;
}

$cliente_id = $_SESSION['cliente_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil del Cliente</title>
  <link rel="stylesheet" href="../CSS/perfil.css?=2">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
  <header>
    <div class="contenedor-header">
      <h1><i data-lucide="user"></i> Perfil del Cliente</h1>
      <nav>
        <a href="#" class="campana-noti" onclick="mostrarNotificaciones()">
          <i data-lucide="bell"></i>
          <span class="contador-noti" id="contadorNoti">0</span>
        </a>
        <a href="login.html"><i data-lucide="log-out"></i>Salir</a>
      </nav>
    </div>
  </header>

  <main class="contenedor-panel">
    <section class="panel-izquierdo">
      <h2><i data-lucide="info"></i> Información</h2>
      <p>Bienvenido al sistema. Aquí verás tus notificaciones y citas confirmadas.</p>
    </section>

    <section class="opciones-panel">
      <a href="cita.php" class="opcion">
        <i data-lucide="calendar-check"></i>
        <h3>Citas</h3>
        <p>Consulta o agenda tus citas fácilmente.</p>
      </a>

      <a href="cotizacion.php" class="opcion">
        <i data-lucide="file-text"></i>
        <h3>Cotizaciones</h3>
        <p>Solicita una cotización para tu vehículo.</p>
      </a>

<a href="#" class="opcion" onclick="abrirModalResena()">
        <i data-lucide="star"></i>
        <h3>Reseñas</h3>
        <p>Comparte tu experiencia con nuestros servicios.</p>
      </a>

      <a href="Carrito.php" class="opcion">
        <i data-lucide="shopping-cart"></i>
        <h3>Comprar</h3>
        <p>Explora y adquiere refacciones o servicios.</p>
      </a>

      <a href="historial.php" class="opcion">
        <i data-lucide="clock"></i>
        <h3>Historial</h3>
        <p>Consulta tus compras y citas pasadas.</p>
      </a>

    <a href="#" class="opcion" onclick="abrirModalSoporte()">
  <i data-lucide="help-circle"></i>
  <h3>Soporte</h3>
  <p>Contáctanos o revisa preguntas frecuentes.</p>
</a>

    </section>
  </main>

  <footer>
    &copy; 2025 AutoGestión MX | Todos los derechos reservados
  </footer>

  <!-- Modal Notificaciones -->
  <div id="modalNotificaciones" class="modal-notificacion" style="display:none;">
    <div class="contenido-modal">
      <h2><i data-lucide="bell"></i> Notificaciones</h2>
      <ul id="listaNotificaciones"></ul>
      <button class="btn-confirmar" onclick="marcarNotificacionesLeidas()">Enterado</button>
    </div>
  </div>

  <script src="../JS/mensajes.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    lucide.createIcons();

    setInterval(() => {
      fetch("../PHP/contar_notificaciones.php?receptor_tipo=cliente")
        .then(res => res.text())
        .then(data => {
          document.getElementById("contadorNoti").textContent = data;
        });
    }, 1000);

    // === Reseñas ===
    window.abrirModalResena = function () {
      document.getElementById("modalResena").style.display = "flex";
    };

    window.cerrarModalResena = function () {
      document.getElementById("modalResena").style.display = "none";
    };

    document.getElementById("formResena").addEventListener("submit", function (e) {
      e.preventDefault();
      const form = this;
      const datos = new FormData(form);

      fetch("../PHP/guardar_resena.php", {
        method: "POST",
        body: datos
      })
        .then(res => res.text())
        .then(response => {
          if (response.trim() === "ok") {
            cerrarModalResena();
            form.reset();
            mostrarModalConfirmacionResena();
          } else {
            alert("Error: " + response);
          }
        })
        .catch(() => {
          alert("Ocurrió un error al enviar la reseña.");
        });
    });

    window.mostrarModalConfirmacionResena = function () {
      const modal = document.getElementById("modalConfirmacionResena");
      modal.style.display = "flex";
      setTimeout(() => {
        modal.style.display = "none";
      }, 1500);
    };
  });

  // === SOPORTE ===
window.abrirModalSoporte = function () {
  document.getElementById("modalSoporte").style.display = "flex";
};

window.cerrarModalSoporte = function () {
  document.getElementById("modalSoporte").style.display = "none";
};

function mostrarModalConfirmacionSoporte() {
  const modal = document.getElementById("modalConfirmacionSoporte");
  modal.style.display = "flex";
  setTimeout(() => {
    modal.style.display = "none";
  }, 2000);
}




    function marcarNotificacionesLeidas() {
      fetch("../PHP/marcar_notificaciones_leidas_cliente.php")
        .then(() => {
          document.getElementById("modalNotificaciones").style.display = "none";
          document.getElementById("listaNotificaciones").innerHTML = "";
          document.getElementById("contadorNoti").textContent = "0";
        });
    }


    function mostrarNotificaciones() {
      fetch("../PHP/obtener_notificaciones.php?receptor_tipo=cliente")
        .then(response => response.json())
        .then(data => {
          const lista = document.getElementById("listaNotificaciones");
          lista.innerHTML = "";

          if (!Array.isArray(data) || data.length === 0) {
            lista.innerHTML = "<li>No hay notificaciones nuevas.</li>";
          } else {
            data.forEach(n => {
              const li = document.createElement("li");
              li.innerHTML = n.mensaje;
              lista.appendChild(li);
            });
          }

          document.getElementById("modalNotificaciones").style.display = "flex";
        });
    }

</script>

  <!-- Modal Reseña -->
<div id="modalResena" class="modal-notificacion" style="display: none;">
  <div class="contenido-modal">
    <h2><i data-lucide="star"></i> Deja tu reseña</h2>
    <form id="formResena">
      <textarea name="texto" rows="5" placeholder="Escribe tu experiencia..." required style="width: 100%; border-radius: 8px; padding: 10px;"></textarea>
      <button type="submit" class="btn-confirmar" style="margin-top: 10px;">Enviar reseña</button>
      <button type="button" class="btn-cancelar" onclick="cerrarModalResena()" style="margin-top: 10px;">Cancelar</button>
    </form>
  </div>
</div>
<!-- Modal confirmación de reseña -->
 <div id="modalConfirmacionResena" class="modal-notificacion" style="display: none;">
  <div class="contenido-modal">
    <h2><i data-lucide="check-circle"></i> ¡Gracias por tu reseña!</h2>
  </div>
</div>
<!-- Modal Soporte -->
<div id="modalSoporte" class="modal-notificacion" style="display: none;">
  <div class="contenido-modal">
    <h2><i data-lucide="mail"></i> Contacta con Soporte</h2>
    <form id="contact-form">
      <input type="text" name="nombre" placeholder="Tu nombre" required style="width: 100%; margin-bottom: 10px; padding: 10px; border-radius: 8px;">
      <input type="email" name="email" placeholder="Tu correo" required style="width: 100%; margin-bottom: 10px; padding: 10px; border-radius: 8px;">
      <textarea name="mensaje" rows="5" placeholder="Escribe tu mensaje..." required style="width: 100%; padding: 10px; border-radius: 8px;"></textarea>
      <div style="margin-top: 15px; display: flex; justify-content: center; gap: 10px;">
        <button type="submit" class="btn-confirmar">Enviar</button>
        <button type="button" class="btn-cancelar" onclick="cerrarModalSoporte()">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- EmailJS -->
<script src="https://cdn.emailjs.com/dist/email.min.js"></script>
<script>
  (function() {
    emailjs.init("yU2eQr-HErtcbnQIX"); // Tu Public Key
  })();

  document.getElementById("contact-form").addEventListener("submit", function(e) {
    e.preventDefault();

    emailjs.sendForm("service_7yl3osh", "template_re0o1vf", this)
      .then(function() {
        mostrarModalConfirmacionSoporte();

        document.getElementById("contact-form").reset();
        cerrarModalSoporte();
      }, function(error) {
        alert("Hubo un error al enviar el mensaje. Intenta más tarde.");
        console.error(error);
      });
  });
</script>

<!-- Modal confirmación de soporte -->
<div id="modalConfirmacionSoporte" class="modal-notificacion" style="display: none;">
  <div class="contenido-modal">
    <h2><i data-lucide="check-circle"></i> Mensaje enviado</h2>
    <p>En seguida te contactaremos.</p>
  </div>
</div>


  
</body>
</html>
