<?php
require '../vendor/autoload.php';
require 'conexion.php';
use Dompdf\Dompdf;

function generarReporteCita($cita_id, $usuario_id) {
    global $conn;

    // Obtener los datos de la cita y el cliente
    $stmt = $conn->prepare("SELECT c.fecha, c.hora, c.estado, u.nombre, u.telefono 
                            FROM citas c 
                            JOIN usuarios u ON c.usuario_id = u.id 
                            WHERE c.id = ? AND u.id = ?");
    $stmt->bind_param("ii", $cita_id, $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();
    $stmt->close();

    if (!$datos) return null;

    $nombre = $datos['nombre'];
    $telefono = $datos['telefono'];
    $fecha = $datos['fecha'];
    $hora = $datos['hora'];
    $estado = ucfirst($datos['estado']);

    // HTML del PDF
    $html = "
    <h1>Comprobante de Cita Mecánica</h1>
    <p><strong>Nombre del cliente:</strong> $nombre</p>
    <p><strong>Teléfono:</strong> $telefono</p>
    <p><strong>Fecha de la cita:</strong> $fecha</p>
    <p><strong>Hora de la cita:</strong> $hora</p>
    <p><strong>Estado:</strong> $estado</p>
    <hr>
    <p>Gracias por usar AutoGestión MX.</p>
    ";

    // Crear el PDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Guardar archivo en el servidor
    $pdfOutput = $dompdf->output();
    $nombreArchivo = uniqid('cita_', true) . '.pdf';
    $rutaArchivo = "../archivos/$nombreArchivo";
    file_put_contents($rutaArchivo, $pdfOutput);

    return $nombreArchivo;
}
?>
