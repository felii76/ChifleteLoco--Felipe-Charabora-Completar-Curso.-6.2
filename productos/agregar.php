
<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../conexion.php";

$error = "";

$nombre = "";
$descripcion = "";
$precio = "";
$stock = "";

function mostrar($valor) {
    return htmlspecialchars(
        (string)($valor ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = trim($_POST["precio"] ?? "");
    $stock = trim($_POST["stock"] ?? "");

    if ($nombre === "" || $precio === "" || $stock === "") {
        $error = "Completá los campos obligatorios.";
    } elseif (
        !is_numeric($precio) ||
        !is_finite((float)$precio) ||
        (float)$precio < 0
    ) {
        $error = "Ingresá un precio válido.";
    } elseif (
        filter_var($stock, FILTER_VALIDATE_INT) === false ||
        (int)$stock < 0
    ) {
        $error = "El stock debe ser un número entero mayor o igual a cero.";
    } else {

        $precioNumero = (float)$precio;
        $stockNumero = (int)$stock;

        $sql = "INSERT INTO productos
                (nombre, descripcion, precio, stock)
                VALUES (?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);

        if ($stmt) {

            $stmt->bind_param(
                "ssdi",
                $nombre,
                $descripcion,
                $precioNumero,
                $stockNumero
            );

            if ($stmt->execute()) {
                $stmt->close();

                header("Location: index.php");
                exit;
            } else {
                $error = "No se pudo guardar el producto.";
            }

            $stmt->close();

        } else {
            $error = "No se pudo preparar la consulta.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto - Chiflete Loco</title>

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

        .campo-completo {
            grid-column: 1 / -1;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            color: #334155;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input:focus, textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #dbeafe;
        }

        .obligatorio {
            color: #dc2626;
        }

        .ayuda {
            color: #64748b;
            font-size: 12px;
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

            .campo-completo {
                grid-column: auto;
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
    <a href="../index.php" class="marca">
        Chiflete Loco
    </a>

    <a href="index.php" class="volver">
        ← Volver a productos
    </a>
</header>

<main class="contenedor">

    <div class="titulo">
        <h1>Agregar producto</h1>
        <p class="subtitulo">
            Completá los datos para registrar un nuevo producto.
        </p>
    </div>

    <form method="POST" class="formulario">

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= mostrar($error) ?>
            </div>
        <?php endif; ?>

        <div class="campos">

            <div class="campo campo-completo">
                <label for="nombre">
                    Nombre del producto
                    <span class="obligatorio">*</span>
                </label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="<?= mostrar($nombre) ?>"
                    placeholder="Ej: Burlete de goma"
                    required
                >
            </div>

            <div class="campo campo-completo">
                <label for="descripcion">Descripción</label>

                <textarea
                    id="descripcion"
                    name="descripcion"
                    placeholder="Describí las características del producto"
                ><?= mostrar($descripcion) ?></textarea>
            </div>

            <div class="campo">
                <label for="precio">
                    Precio ($)
                    <span class="obligatorio">*</span>
                </label>

                <input
                    type="number"
                    id="precio"
                    name="precio"
                    step="0.01"
                    min="0"
                    value="<?= mostrar($precio) ?>"
                    placeholder="0.00"
                    required
                >

                <span class="ayuda">
                    Ingresá el precio en pesos.
                </span>
            </div>

            <div class="campo">
                <label for="stock">
                    Stock inicial
                    <span class="obligatorio">*</span>
                </label>

                <input
                    type="number"
                    id="stock"
                    name="stock"
                    step="1"
                    min="0"
                    value="<?= mostrar($stock) ?>"
                    placeholder="0"
                    required
                >

                <span class="ayuda">
                    Cantidad de unidades disponibles.
                </span>
            </div>

        </div>

        <div class="acciones">
            <a href="index.php" class="cancelar">
                Cancelar
            </a>

            <button type="submit" class="guardar">
                Guardar producto
            </button>
        </div>

    </form>

</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>