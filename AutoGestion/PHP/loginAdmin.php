<?php
session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $stmt = $conn->prepare("SELECT id, correo, contrasena FROM administradores WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $admin = $resultado->fetch_assoc();
        if ($contrasena === $admin['contrasena']) {
            $_SESSION['admin_id'] = $admin['id'];
            header("Location: ../HTML/login.html?login=exitoso&tipo=administrador");
            exit;
        }
    }

    header("Location: ../HTML/login.html?error=credenciales");
    exit;
} else {
    echo "Acceso denegado.";
}
