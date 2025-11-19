

<?php
$mensaje = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Solicitud de Cotización</title>
  <link rel="stylesheet" href="../CSS/cotizacion.css?v=2">

  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
  <header>
    <div class="contenedor-header">
      <h1><i data-lucide="file-text"></i> Solicitud de Cotización</h1>
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
          <h2>Formulario de Cotización Mecánica</h2>
          <form action="../PHP/guardar_cotizacion.php" method="POST" id="formCotizacion">
            <input type="text" name="nombre" placeholder="Tu nombre" required />
            <input type="email" name="correo" placeholder="Correo electrónico" required />
            <input type="tel" name="telefono" placeholder="Teléfono" required />
            <input type="text" name="marca" placeholder="Marca del vehículo" required />
            <input type="text" name="modelo" placeholder="Modelo del vehículo" required />
            <input type="text" name="servicio" placeholder="Servicio requerido" required />
            <textarea name="comentarios" placeholder="Comentarios adicionales" rows="4"></textarea>
            <button type="submit">Enviar Cotización</button>
          </form>
        </div>
      </section>
    </div>
  </main>

  <footer>
    &copy; 2025 AutoGestión MX | Todos los derechos reservados
  </footer>

  <?php if ($mensaje): ?>
    <div id="mensajeConfirmacion" class="modal-confirmacion fade-in">
      <h2><?= $mensaje ?></h2>
    </div>
    <script>
      // Redirige automáticamente quitando el parámetro msg después de mostrar el mensaje
      setTimeout(() => {
        window.history.replaceState({}, document.title, window.location.pathname);
        const modal = document.getElementById("mensajeConfirmacion");
        if (modal) modal.remove();
      }, 1000);
    </script>
  <?php endif; ?>

  <script src="../JS/mensajes.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>