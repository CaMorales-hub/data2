function mostrarMensaje(mensaje, tipo = 'info') {
  const modal = document.getElementById("mensajeConfirmacion");
  const texto = document.getElementById("textoConfirmacion");

  let icono = '';
  let clase = '';

  switch (tipo) {
    case 'success':
      icono = '✅';
      clase = 'toast-success';
      break;
    case 'error':
      icono = '❌';
      clase = 'toast-error';
      break;
    case 'info':
    default:
      icono = 'ℹ️';
      clase = 'toast-info';
      break;
  }

  texto.innerHTML = `<span class="toast-icon ${clase}">${icono}</span> ${mensaje}`;
  modal.style.display = "block";

  // Ocultar automáticamente después de 1 segundo
  setTimeout(() => {
    modal.style.display = "none";
  }, 1000);
}

function cerrarConfirmacion() {
  document.getElementById("mensajeConfirmacion").style.display = "none";
}

// Confirmación visual y redirección con mensaje
function eliminarUsuarioConfirmado() {
  if (idEliminar !== null) {
    window.location.href = `../PHP/eliminar_usuario.php?id=${idEliminar}&msg=Usuario%20eliminado&type=success`;
  }
}
