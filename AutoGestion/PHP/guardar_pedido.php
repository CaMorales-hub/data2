<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'conexion.php';
require 'fpdf/fpdf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['cliente_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit;
}

$usuario_id = $_SESSION['cliente_id'];
$total = $data['total'];
$productos = $data['productos'];
$pago = $data['pago'];
$fecha = date("Y-m-d H:i:s");

$nombre = $pago['nombre'];
$correo = ""; // Puedes obtenerlo de la BD si lo deseas

// Insertar pedido
$stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, nombre_cliente, correo_cliente, total, fecha) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issds", $usuario_id, $nombre, $correo, $total, $fecha);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar pedido']);
    exit;
}
$pedido_id = $stmt->insert_id;

// Detalle pedido
foreach ($productos as $producto) {
    $producto_id = $producto['id'];
    $cantidad = $producto['cantidad'];
    $precio_unitario = $producto['precio'];

    $stmt = $conn->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $pedido_id, $producto_id, $cantidad, $precio_unitario);
    $stmt->execute();

    $conn->query("UPDATE productos SET stock = stock - $cantidad WHERE id = $producto_id");
}

// PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Resumen de Pedido',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Ln(5);
$pdf->Cell(0,10,"Cliente: $nombre",0,1);
$pdf->Cell(0,10,"Fecha: $fecha",0,1);
$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(90,10,'Producto',1);
$pdf->Cell(30,10,'Cantidad',1);
$pdf->Cell(30,10,'Precio',1);
$pdf->Cell(40,10,'Subtotal',1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
foreach ($productos as $item) {
    $nombreProd = $item['nombre'];
    $cantidad = $item['cantidad'];
    $precio = $item['precio'];
    $subtotal = $cantidad * $precio;
    $pdf->Cell(90,10,$nombreProd,1);
    $pdf->Cell(30,10,$cantidad,1);
    $pdf->Cell(30,10,"$".$precio,1);
    $pdf->Cell(40,10,"$".number_format($subtotal,2),1);
    $pdf->Ln();
}

$pdf->Ln(5);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Total: $'.number_format($total,2),0,1,'R');

$archivo_nombre = "pedido_$pedido_id.pdf";
$ruta = "../archivos/$archivo_nombre";
$pdf->Output("F", $ruta);

// Guardar nombre del PDF
$conn->query("UPDATE pedidos SET archivo_pdf = '$archivo_nombre' WHERE id = $pedido_id");

echo json_encode(['success' => true]);
