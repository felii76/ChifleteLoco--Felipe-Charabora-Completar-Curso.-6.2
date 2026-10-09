
<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../conexion.php";

$error = "";

$nombre = "";
$apellido = "";
$dni = "";
$direccion = "";
$telefono = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $dni = trim($_POST["dni"] ?? "");
    $direccion = trim($_POST["direccion"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($nombre === "" || $apellido === "" || $dni === "") {
        $error = "Completá los campos obligatorios.";
    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingresá un correo electrónico válido.";
    } else {

        $sql = "INSERT INTO clientes
                (nombre, apellido, dni, direccion, telefono, email)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);

        if ($stmt) {
            $stmt->bind_param(
                "ssssss",
                $nombre,
                $apellido,
                $dni,
                $direccion,
                $telefono,
                $email
            );

            if ($stmt->execute()) {
                header("Location: index.php");
                exit;
            } else {
                $error = "No se pudo guardar el cliente.";
            }

            $stmt->close();
        } else {
            $error = "No se pudo preparar la consulta.";
        }
    }
}

function mostrar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cliente - Chiflete Loco</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #1e293b;
        }

        .encabezado {
            background: white;
            padding: 20px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .marca {
            font-size: 22px;
            font-weight: bold;
            color: #1d4ed8;
            text-decoration: none;
        }

        .volver {
            background: #eff6ff;
            color: #1d4ed8;
            text-decoration: none;
            padding: 11px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .contenedor {
            max-width: 750px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .titulo {
            margin-bottom: 25px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 27px;
        }

        .subtitulo {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }

        .formulario {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 30px;
        }

        .campos {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .campo {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            color: #334155;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
        }

        input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #dbeafe;
        }

        .obligatorio {
            color: #dc2626;
        }

        .acciones {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
        }

        .guardar, .cancelar {
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .guardar {
            background: #2563eb;
            color: white;
            border: none;
        }

        .guardar:hover {
            background: #1d4ed8;
        }

        .cancelar {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        .cancelar:hover {
            background: #e2e8f0;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .pie {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            padding: 35px 15px;
        }

        @media (max-width: 600px) {
            .campos {
                grid-template-columns: 1fr;
            }

            .formulario {
                padding: 20px;
            }

            .contenedor {
                margin-top: 25px;
            }

            .acciones {
                flex-direction: column-reverse;
            }

            .guardar, .cancelar {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<header class="encabezado">
    <a href="../index.php" class="marca">Chiflete Loco</a>
    <a href="index.php" class="volver">← Volver a clientes</a>
</header>

<main class="contenedor">

    <div class="titulo">
        <h1>Agregar cliente</h1>
        <p class="subtitulo">
            Completá los datos para registrar un nuevo cliente.
        </p>
    </div>

    <form method="POST" class="formulario">

        <?php if ($error !== ""): ?>
            <div class="error"><?= mostrar($error) ?></div>
        <?php endif; ?>

        <div class="campos">

            <div class="campo">
                <label for="nombre">
                    Nombre <span class="obligatorio">*</span>
                </label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?= mostrar($nombre) ?>"
                    required
                >
            </div>

            <div class="campo">
                <label for="apellido">
                    Apellido <span class="obligatorio">*</span>
                </label>
                <input
                    type="text"
                    id="apellido"
                    name="apellido"
                    value="<?= mostrar($apellido) ?>"
                    required
                >
            </div>

            <div class="campo">
                <label for="dni">
                    DNI <span class="obligatorio">*</span>
                </label>
                <input
                    type="text"
                    id="dni"
                    name="dni"
                    value="<?= mostrar($dni) ?>"
                    required
                >
            </div>

            <div class="campo">
                <label for="telefono">Teléfono</label>
                <input
                    type="tel"
                    id="telefono"
                    name="telefono"
                    value="<?= mostrar($telefono) ?>"
                >
            </div>

            <div class="campo">
                <label for="direccion">Dirección</label>
                <input
                    type="text"
                    id="direccion"
                    name="direccion"
                    value="<?= mostrar($direccion) ?>"
                >
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= mostrar($email) ?>"
                >
            </div>

        </div>

        <div class="acciones">
            <a href="index.php" class="cancelar">Cancelar</a>
            <button type="submit" class="guardar">
                Guardar cliente
            </button>
        </div>

    </form>

</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>