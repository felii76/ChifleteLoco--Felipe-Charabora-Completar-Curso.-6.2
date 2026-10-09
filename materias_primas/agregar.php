
<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../conexion.php";

function mostrar($valor) {
    return htmlspecialchars(
        (string)($valor ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$error = "";

$nombre = "";
$unidad = "";
$stock = "0";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $unidad = trim($_POST["unidad"] ?? "");
    $stock = trim($_POST["stock"] ?? "");

    $stockNumero = filter_var(
        $stock,
        FILTER_VALIDATE_FLOAT
    );

    if ($nombre === "") {
        $error = "Ingresá el nombre de la materia prima.";

    } elseif ($unidad === "") {
        $error = "Ingresá la unidad de medida.";

    } elseif (
        $stockNumero === false ||
        !is_finite($stockNumero) ||
        $stockNumero < 0 ||
        round($stockNumero, 2) != $stockNumero
    ) {
        $error = "El stock debe ser un número válido, mayor o igual a cero y con hasta dos decimales.";

    } else {

        // Guardar materia prima
        $stmt = $conexion->prepare(
            "INSERT INTO materias_primas
             (nombre, unidad, stock)
             VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "ssd",
            $nombre,
            $unidad,
            $stockNumero
        );

        if ($stmt->execute()) {

            $stmt->close();

            header("Location: index.php");
            exit;

        } else {

            $error = "No se pudo guardar la materia prima.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agregar Materia Prima - Chiflete Loco</title>

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

        .volver:hover {
            background: #dbeafe;
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

        .seccion-titulo {
            font-size: 17px;
            font-weight: bold;
            color: #334155;
            margin: 0 0 22px;
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

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
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

        .ayuda {
            font-size: 12px;
            color: #64748b;
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
            margin-bottom: 22px;
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

            h1 {
                font-size: 23px;
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
        ← Volver a materias primas
    </a>
</header>

<main class="contenedor">

    <div class="titulo">
        <h1>Agregar materia prima</h1>

        <p class="subtitulo">
            Completá los datos para registrar
            una nueva materia prima.
        </p>
    </div>

    <form method="POST" class="formulario">

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= mostrar($error) ?>
            </div>
        <?php endif; ?>

        <h2 class="seccion-titulo">
            Información de la materia prima
        </h2>

        <div class="campos">

            <div class="campo campo-completo">
                <label for="nombre">
                    Nombre
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

            <div class="campo">
                <label for="unidad">
                    Unidad de medida
                    <span class="obligatorio">*</span>
                </label>

                <input
                    type="text"
                    id="unidad"
                    name="unidad"
                    value="<?= mostrar($unidad) ?>"
                    placeholder="Ej: kg, metros, unidades"
                    required
                >

                <span class="ayuda">
                    Indicá cómo se mide el material.
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
                    min="0"
                    step="0.01"
                    value="<?= mostrar($stock) ?>"
                    required
                >

                <span class="ayuda">
                    Podés comenzar con 0 o indicar
                    una cantidad disponible.
                </span>
            </div>

        </div>

        <div class="acciones">

            <a href="index.php" class="cancelar">
                Cancelar
            </a>

            <button type="submit" class="guardar">
                Guardar materia prima
            </button>

        </div>

    </form>

</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>