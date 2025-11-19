<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf;

function generarReporteCita($nombre, $telefono, $fecha, $hora, $estado = "pendiente") {
    $html = "
        <h1>Comprobante de Cita Mecánica</h1>
        <p><strong>Nombre del cliente:</strong> $nombre</p>
        <p><strong>Teléfono:</strong> $telefono</p>
        <p><strong>Fecha de la cita:</strong> $fecha</p>
        <p><strong>Hora de la cita:</strong> $hora</p>
        <p><strong>Estado:</strong> " . ucfirst($estado) . "</p>
        <hr>
        <p>Gracias por usar AutoGestión MX.</p>
    ";

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $nombreArchivo = uniqid('cita_', true) . '.pdf';
    $rutaArchivo = __DIR__ . '/../archivos/' . $nombreArchivo;
    file_put_contents($rutaArchivo, $dompdf->output());

    return $nombreArchivo;
}
?>
