

<?php
session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $stmt = $conn->prepare("SELECT id, correo, contrasena FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if ($contrasena === $usuario['contrasena']) {
            $_SESSION['cliente_id'] = $usuario['id'];
            header("Location: ../HTML/login.html?login=exitoso&tipo=cliente");
            exit;
        }
    }

    header("Location: ../HTML/login.html?error=credenciales");
    exit;
} else {
    echo "Acceso denegado.";
}