<?php
session_start();
require 'conexion.php';

// Asegúrate de que un personal esté logueado
if (!isset($_SESSION['personal_id'])) {
    echo "No autorizado";
    exit;
}

if (isset($_GET['id'])) {
    $cita_id = intval($_GET['id']);

    // 1. Actualizar el estado de la cita a 'confirmada'
    $stmt = $conn->prepare("UPDATE citas SET estado = 'confirmada' WHERE id = ?");
    $stmt->bind_param("i", $cita_id);
    $stmt->execute();

    // 2. Obtener el usuario_id (cliente que la creó)
    $stmt2 = $conn->prepare("SELECT usuario_id FROM citas WHERE id = ?");
    $stmt2->bind_param("i", $cita_id);
    $stmt2->execute();
    $res = $stmt2->get_result();
    $usuario = $res->fetch_assoc();

    if ($usuario) {
        $cliente_id = $usuario['usuario_id'];

        // 3. Insertar la notificación al cliente
        $mensaje = "Tu cita ha sido confirmada.";
        $stmt3 = $conn->prepare("INSERT INTO notificaciones (receptor_id, receptor_tipo, mensaje, leido, usuario_id, creado_en) VALUES (?, 'cliente', ?, 0, ?, NOW())");
        $stmt3->bind_param("isi", $cliente_id, $mensaje, $_SESSION['personal_id']);
        $stmt3->execute();
    }

    echo "ok";
} else {
    echo "ID de cita no recibido";
}
?>
