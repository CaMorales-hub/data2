<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $sql = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $correo, $contrasena);

    if ($stmt->execute()) {
        // Mostrar animación y luego redirigir
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Registro Exitoso</title>
            
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    background-color: #f0f8ff;
                    font-family: Arial, sans-serif;
                }
                .mensaje {
                    text-align: center;
                    padding: 40px;
                    background: #d4edda;
                    color: #155724;
                    border: 2px solid #c3e6cb;
                    border-radius: 15px;
                    animation: zoomIn 1s ease;
                    box-shadow: 0 0 15px rgba(0,0,0,0.2);
                }
                @keyframes zoomIn {
                    from {
                        transform: scale(0.3);
                        opacity: 0;
                    }
                    to {
                        transform: scale(1);
                        opacity: 1;
                    }
                }
            </style>
        </head>
        <body>
            <div class="mensaje">
                <h2>✅ ¡Registro Exitoso!</h2>
                <p>Serás redirigido en unos segundos...</p>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "../HTML/login.html";
                }, 3000); // Redirige después de 3 segundos
            </script>
        </body>
        </html>';
        exit();
    } else {
        echo "Error al registrar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
