<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['cliente_id'])) {
    http_response_code(401);
    echo "No autorizado";
    exit;
}

$cliente_id = $_SESSION['cliente_id'];

// Obtener la última cita confirmada del cliente
$query = $conn->prepare("SELECT archivo_pdf FROM citas WHERE usuario_id = ? AND estado = 'confirmada' ORDER BY creado_en DESC LIMIT 1");
$query->bind_param("i", $cliente_id);
$query->execute();
$resultado = $query->get_result();

if ($resultado && $resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $archivo = $row['archivo_pdf'];
    $ruta = "../archivos/" . $archivo;

    if (file_exists($ruta)) {
        header("Content-Disposition: attachment; filename=" . basename($archivo));
        header("Content-Type: application/pdf");
        readfile($ruta);
        exit;
    } else {
        http_response_code(404);
        echo "No se encontró el archivo PDF.";
    }
} else {
    http_response_code(404);
    echo "No se encontró una cita confirmada.";
}
