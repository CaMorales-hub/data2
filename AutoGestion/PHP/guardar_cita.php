<?php
session_start();
require 'conexion.php';
require_once 'funciones_pdf.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar sesión del cliente
    if (!isset($_SESSION['cliente_id'])) {
        http_response_code(401);
        echo "No hay sesión activa del cliente.";
        exit;
    }

    $cliente_id = $_SESSION['cliente_id'];

    // Validar existencia del cliente
    $check = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
    $check->bind_param("i", $cliente_id);
    $check->execute();
    $resultado = $check->get_result();

    if ($resultado->num_rows === 0) {
        http_response_code(400);
        echo "El usuario no existe en la tabla de clientes.";
        exit;
    }
    $check->close();

    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $estado = 'pendiente';

    // Insertar cita
    $stmt = $conn->prepare("INSERT INTO citas (usuario_id, fecha, hora, estado, creado_en) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isss", $cliente_id, $fecha, $hora, $estado);

    if ($stmt->execute()) {
        $cita_id = $conn->insert_id;

        // Generar y guardar PDF
        $archivo_pdf = generarReporteCita($nombre, $telefono, $fecha, $hora, $estado);
        $update = $conn->prepare("UPDATE citas SET archivo_pdf = ? WHERE id = ?");
        $update->bind_param("si", $archivo_pdf, $cita_id);
        $update->execute();
        $update->close();

        // Notificar a todos los usuarios del personal
        $mensaje = "Nueva cita registrada para el día $fecha a las $hora.";
        $result = $conn->query("SELECT id FROM personal");

        while ($row = $result->fetch_assoc()) {
            $personal_id = $row['id'];
            $noti = $conn->prepare("INSERT INTO notificaciones (usuario_id, receptor_tipo, receptor_id, mensaje, leido) VALUES (?, 'personal', ?, ?, 0)");
            $noti->bind_param("iis", $cliente_id, $personal_id, $mensaje);
            $noti->execute();
            $noti->close();
        }

        echo "Ok";
    } else {
        http_response_code(500);
        echo "Error al registrar la cita: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo "Método no permitido.";
}
