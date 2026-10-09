
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

// Validar el ID de la materia prima
$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

// Buscar la materia prima
$stmt = $conexion->prepare(
    "SELECT id_materia_prima, nombre, unidad, stock
     FROM materias_primas
     WHERE id_materia_prima = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$materia = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$materia) {
    http_response_code(404);
    exit("La materia prima no existe.");
}

$error = "";

$nombre = $materia["nombre"];
$unidad = $materia["unidad"];

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $unidad = trim($_POST["unidad"] ?? "");

    if ($nombre === "") {
        $error = "Ingresá el nombre de la materia prima.";

    } elseif ($unidad === "") {
        $error = "Ingresá la unidad de medida.";

    } else {

        try {
            $stmt = $conexion->prepare(
                "UPDATE materias_primas
                 SET nombre = ?,
                     unidad = ?
                 WHERE id_materia_prima = ?"
            );

            $stmt->bind_param(
                "ssi",
                $nombre,
                $unidad,
                $id
            );

            if (!$stmt->execute()) {
                throw new Exception(
                    "No se pudo actualizar la materia prima."
                );
            }

            $stmt->close();

            header("Location: index.php");
            exit;

        } catch (Throwable $e) {

            error_log($e->getMessage());

            $error = "No se pudieron guardar los cambios. " .
                     "Intentá nuevamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Materia Prima - Chiflete Loco</title>

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

        input:disabled {
            background: #f1f5f9;
            color: #64748b;
            cursor: not-allowed;
        }

        .obligatorio {
            color: #dc2626;
        }

        .ayuda {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }

        .separador {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 28px 0;
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
        <h1>Editar materia prima</h1>

        <p class="subtitulo">
            Modificá la información de la materia prima
            #<?= $id ?>.
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

            <div class="campo campo-completo">
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
                    Indicá cómo se mide esta materia prima.
                </span>
            </div>

        </div>

        <hr class="separador">

        <h2 class="seccion-titulo">
            Información de stock
        </h2>

        <div class="campo">
            <label for="stock">
                Stock actual
            </label>

            <input
                type="text"
                id="stock"
                value="<?= number_format(
                    (float)$materia["stock"],
                    2,
                    ",",
                    "."
                ) ?> <?= mostrar($materia["unidad"]) ?>"
                disabled
            >

            <span class="ayuda">
                El stock no se modifica desde esta pantalla.
                Para registrar ingresos o egresos, utilizá
                el módulo Stock.
            </span>
        </div>

        <div class="acciones">

            <a href="index.php" class="cancelar">
                Cancelar
            </a>

            <button type="submit" class="guardar">
                Guardar cambios
            </button>

        </div>

    </form>

</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>