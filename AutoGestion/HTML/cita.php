<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Agendar Cita</title>
  <link rel="stylesheet" href="../CSS/cotizacion.css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>

  </style>
</head>
<body>
  <header>
    <div class="contenedor-header">
      <h1><i data-lucide="calendar-check"></i> Agenda tu Cita</h1>
      <nav>
        <a href="perfil.php"><i data-lucide="user"></i>Perfil</a>
        <a href="login.html"><i data-lucide="log-out"></i>Cerrar sesión</a>
      </nav>
    </div>
  </header>

  <main class="dashboard">
    <div class="contenedor-azul">
      <section class="cotizacion-formulario">
        <div class="blur-box">
          <h2>Formulario de Cita</h2>
          <form id="formCita">
            <input type="text" name="nombre" placeholder="Tu nombre" required />
            <input type="tel" name="telefono" placeholder="Teléfono de contacto" required />
            <input type="date" name="fecha" required />
            <input type="time" name="hora" required />
            <button type="submit">Agendar Cita</button>
          </form>
        </div>
      </section>
    </div>
  </main>

  <footer>
    &copy; 2025 AutoGestión MX | Todos los derechos reservados
  </footer>

  <!-- Modal oculto -->
  <div id="modalConfirmacion" class="modal-confirmacion" style="display: none;">
    Cita agendada correctamente
  </div>

  <script>
    lucide.createIcons();

    document.getElementById("formCita").addEventListener("submit", function(e) {
      e.preventDefault(); // Evita recargar
      const form = e.target;
      const datos = new FormData(form);

      fetch("../PHP/guardar_cita.php", {
        method: "POST",
        body: datos
      })
      .then(res => res.text())
      .then(respuesta => {
        // Mostrar modal
        const modal = document.getElementById("modalConfirmacion");
        modal.style.display = "block";

        // Limpiar formulario
        form.reset();

        // Ocultar modal después de 1 segundo
        setTimeout(() => {
          modal.style.display = "none";
        }, 1000);
      })
      .catch(err => {
        console.error("Error al enviar cita:", err);
      });
    });
  </script>
</body>
</html>
