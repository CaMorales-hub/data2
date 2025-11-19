<?php
require 'conexion.php';
require 'fpdf/fpdf.php'; // Asegúrate de que esta ruta sea correcta

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id_cotizacion'];
    $costo = $_POST['costo'];
    $direccion = $_POST['direccion'];
    $tecnico = $_POST['tecnico'];
    $observaciones = $_POST['observaciones'];

    // Obtener datos del cliente
    $stmt = $conn->prepare("SELECT u.nombre, u.correo, c.creado_en FROM cotizaciones c 
                            JOIN usuarios u ON c.usuario_id = u.id WHERE c.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();

    if (!$datos) {
        echo "Cotización no encontrada";
        exit;
    }

    $nombreCliente = $datos['nombre'];
    $correoCliente = $datos['correo'];
    $fecha = $datos['creado_en'];

    // Crear PDF con FPDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Encabezado
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Respuesta a la Cotización', 0, 1, 'C');

    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(5);
    $pdf->Cell(0, 10, "Cliente: $nombreCliente", 0, 1);
    $pdf->Cell(0, 10, "Correo: $correoCliente", 0, 1);
    $pdf->Cell(0, 10, "Fecha de solicitud: $fecha", 0, 1);
    $pdf->Ln(5);

    // Respuesta
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Respuesta del taller:', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, "Costo estimado: $costo");
    $pdf->MultiCell(0, 10, "Dirección del taller: $direccion");
    $pdf->MultiCell(0, 10, "Técnico asignado: $tecnico");
    $pdf->MultiCell(0, 10, "Observaciones: $observaciones");

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Contacto en caso de dudas:', 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Teléfono: 744 123 4567', 0, 1);
    $pdf->Cell(0, 10, 'Teléfono alternativo: 744 765 4321', 0, 1);
    $pdf->Cell(0, 10, 'Correo: soporte@autogestionmx.com', 0, 1);

    // Guardar PDF
    $nombreArchivo = "respuesta_" . time() . ".pdf";
    $rutaArchivo = "../archivos/" . $nombreArchivo;
    $pdf->Output('F', $rutaArchivo);

    // Actualizar BD
    $stmt = $conn->prepare("UPDATE cotizaciones SET estado = 'respondida', archivo_respuesta_pdf = ? WHERE id = ?");
    $stmt->bind_param("si", $nombreArchivo, $id);

    if ($stmt->execute()) {
        echo "Respuesta registrada";
        // Obtener el usuario_id (cliente) para notificación
$consulta = $conn->prepare("SELECT usuario_id FROM cotizaciones WHERE id = ?");
$consulta->bind_param("i", $id);
$consulta->execute();
$resultado = $consulta->get_result();
$cliente = $resultado->fetch_assoc();
$cliente_id = $cliente['usuario_id'];

// Crear mensaje con botón de descarga
$mensaje = "Tu cotización ha sido respondida. <a href='/AutoGestion/archivos/$nombreArchivo' download style='color:#fff;text-decoration:underline;'>Descargar acuse</a>";

$insertar = $conn->prepare("INSERT INTO notificaciones (receptor_id, receptor_tipo, mensaje, leido, usuario_id) VALUES (?, 'cliente', ?, 0, ?)");
$insertar->bind_param("isi", $cliente_id, $mensaje, $cliente_id);
$insertar->execute();

    } else {
        echo "Error al guardar respuesta";
    }
}
?>
