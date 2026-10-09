
<?php
session_start();
require_once "conexion.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conexion->prepare(
        "SELECT id, usuario, password FROM usuarios WHERE usuario = ?"
    );
    $stmt->bind_param("s", $usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $datos = $resultado->fetch_assoc();

    if ($datos && password_verify($password, $datos["password"])) {
        session_regenerate_id(true);

        $_SESSION["usuario_id"] = $datos["id"];
        $_SESSION["usuario"] = $datos["usuario"];

        header("Location: index.php");
        exit;
    }

    $error = "Usuario o contraseña incorrectos.";
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Chiflete Loco</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login {
            background: white;
            padding: 35px;
            border-radius: 10px;
            width: 320px;
            box-shadow: 0 4px 15px #0002;
            text-align: center;
        }

        input {
            box-sizing: border-box;
            width: 100%;
            padding: 12px;
            margin: 10px 0;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .error { color: #c62828; }
    </style>
</head>
<body>

    <div class="login">
        <h2>Chiflete Loco</h2>
        <p>Iniciar sesión</p>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="usuario"
                   placeholder="Usuario" required>

            <input type="password" name="password"
                   placeholder="Contraseña" required>

            <button type="submit">Ingresar</button>
        </form>
    </div>

</body>
</html>