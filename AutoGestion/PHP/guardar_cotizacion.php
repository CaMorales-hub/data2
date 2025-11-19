<?php
require 'conexion.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;

session_start();

if (!isset($_SESSION['cliente_id'])) {
    die("No has iniciado sesión.");
}

$usuario_id = $_SESSION['cliente_id'];
$nombre     = $_POST['nombre'];
$correo     = $_POST['correo'];
$telefono   = $_POST['telefono'];
$marca      = $_POST['marca'];
$modelo     = $_POST['modelo'];
$servicio   = $_POST['servicio'];
$comentarios= $_POST['comentarios'];
$fecha      = date('Y-m-d H:i:s');

// Crear contenido del PDF con formato profesional
$html = "
  <style>
    body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
    h1 { text-align: center; color: #1e293b; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ccc; }
    th { background-color: #f1f5f9; }
  </style>
  <h1>Solicitud de Cotización</h1>
  <table>
    <tr><th>Nombre</th><td>{$nombre}</td></tr>
    <tr><th>Correo</th><td>{$correo}</td></tr>
    <tr><th>Teléfono</th><td>{$telefono}</td></tr>
    <tr><th>Marca</th><td>{$marca}</td></tr>
    <tr><th>Modelo</th><td>{$modelo}</td></tr>
    <tr><th>Servicio solicitado</th><td>{$servicio}</td></tr>
    <tr><th>Comentarios</th><td>{$comentarios}</td></tr>
    <tr><th>Fecha de solicitud</th><td>{$fecha}</td></tr>
  </table>
";

// Generar el PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Guardar el archivo PDF
$nombreArchivo = 'cotizacion_' . time() . '.pdf';
$rutaArchivo = '../archivos/' . $nombreArchivo;
file_put_contents($rutaArchivo, $dompdf->output());

// Guardar en la base de datos
$stmt = $conn->prepare("INSERT INTO cotizaciones (usuario_id, estado, creado_en, archivo_pdf) VALUES (?, 'pendiente', ?, ?)");
$stmt->bind_param("iss", $usuario_id, $fecha, $nombreArchivo);
$stmt->execute();

// después de guardar correctamente:
header("Location: ../HTML/cotizacion.php?msg=Solicitud enviada correctamente");
exit;

?>
