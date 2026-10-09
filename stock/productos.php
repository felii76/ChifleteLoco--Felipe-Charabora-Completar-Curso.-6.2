
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

function claseMovimiento($tipo) {
    if ($tipo === "Ingreso") {
        return "tipo-ingreso";
    }

    if ($tipo === "Egreso") {
        return "tipo-egreso";
    }

    return "tipo-otro";
}

$error = "";

$producto = "";
$tipo = "Ingreso";
$cantidad = "";
$fecha = date("Y-m-d");

// Registrar movimiento
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $producto = $_POST["producto"] ?? "";
    $tipo = $_POST["tipo"] ?? "";
    $cantidad = $_POST["cantidad"] ?? "";
    $fecha = $_POST["fecha"] ?? "";

    $productoId = filter_var(
        $producto,
        FILTER_VALIDATE_INT
    );

    $cantidadNumero = filter_var(
        $cantidad,
        FILTER_VALIDATE_INT
    );

    $fechaObjeto = DateTime::createFromFormat(
        "!Y-m-d",
        $fecha
    );

    $fechaValida = (
        $fechaObjeto &&
        $fechaObjeto->format("Y-m-d") === $fecha
    );

    if ($productoId === false || $productoId < 1) {
        $error = "Seleccioná un producto válido.";

    } elseif (!in_array($tipo, ["Ingreso", "Egreso"], true)) {
        $error = "Seleccioná un tipo de movimiento válido.";

    } elseif (
        $cantidadNumero === false ||
        $cantidadNumero < 1
    ) {
        $error = "La cantidad debe ser un número entero mayor a cero.";

    } elseif (!$fechaValida) {
        $error = "Ingresá una fecha válida.";

    } else {

        try {
            $conexion->begin_transaction();

            // Buscar y bloquear el stock actual
            $stmt = $conexion->prepare(
                "SELECT stock
                 FROM productos
                 WHERE id_producto = ?
                 FOR UPDATE"
            );

            $stmt->bind_param("i", $productoId);
            $stmt->execute();

            $dato = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$dato) {
                throw new Exception("El producto no existe.");
            }

            $stockActual = (int)$dato["stock"];

            // Calcular el nuevo stock
            if ($tipo === "Ingreso") {
                $nuevoStock = $stockActual + $cantidadNumero;
            } else {
                $nuevoStock = $stockActual - $cantidadNumero;
            }

            // Evitar stock negativo
            if ($nuevoStock < 0) {
                throw new Exception(
                    "No hay suficiente stock para realizar este egreso."
                );
            }

            // Actualizar stock
            $stmt = $conexion->prepare(
                "UPDATE productos
                 SET stock = ?
                 WHERE id_producto = ?"
            );

            $stmt->bind_param(
                "ii",
                $nuevoStock,
                $productoId
            );

            if (!$stmt->execute()) {
                throw new Exception(
                    "No se pudo actualizar el stock."
                );
            }

            $stmt->close();

            // Registrar movimiento
            $stmt = $conexion->prepare(
                "INSERT INTO movimientos_stock
                 (id_producto, tipo, cantidad, fecha)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "isis",
                $productoId,
                $tipo,
                $cantidadNumero,
                $fecha
            );

            if (!$stmt->execute()) {
                throw new Exception(
                    "No se pudo registrar el movimiento."
                );
            }

            $stmt->close();

            // Confirmar ambas operaciones
            $conexion->commit();

            header("Location: productos.php");
            exit;

        } catch (Throwable $e) {

            $conexion->rollback();

            error_log($e->getMessage());

            if (
                $e->getMessage() ===
                "No hay suficiente stock para realizar este egreso."
            ) {
                $error = $e->getMessage();
            } else {
                $error = "No se pudo guardar el movimiento. " .
                         "Revisá los datos e intentá nuevamente.";
            }
        }
    }
}

// Obtener productos
$productos = $conexion->query(
    "SELECT id_producto, nombre, stock
     FROM productos
     ORDER BY nombre"
);

// Obtener historial
$movimientos = $conexion->query(
    "SELECT movimientos_stock.id_movimiento,
            productos.nombre,
            movimientos_stock.tipo,
            movimientos_stock.cantidad,
            movimientos_stock.fecha
     FROM movimientos_stock
     INNER JOIN productos
     ON movimientos_stock.id_producto = productos.id_producto
     ORDER BY movimientos_stock.fecha DESC,
              movimientos_stock.id_movimiento DESC"
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Stock de Productos - Chiflete Loco</title>

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
            max-width: 1150px;
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

        .tarjeta {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 28px;
            margin-bottom: 28px;
        }

        .titulo-tarjeta {
            font-size: 18px;
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

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
            font-size: 14px;
            font-family: inherit;
            outline: none;
        }

        input:focus, select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px #dbeafe;
        }

        .obligatorio {
            color: #dc2626;
        }

        .acciones {
            display: flex;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .guardar {
            background: #2563eb;
            color: white;
            padding: 13px 20px;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        .guardar:hover {
            background: #1d4ed8;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            padding: 17px;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 17px;
            border-bottom: 1px solid #f1f5f9;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .numero {
            color: #2563eb;
            font-weight: bold;
        }

        .producto {
            font-weight: bold;
        }

        .tipo {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .tipo-ingreso {
            background: #dcfce7;
            color: #166534;
        }

        .tipo-egreso {
            background: #fee2e2;
            color: #b91c1c;
        }

        .tipo-otro {
            background: #e2e8f0;
            color: #475569;
        }

        .cantidad {
            font-weight: bold;
        }

        .cantidad-ingreso {
            color: #16a34a;
        }

        .cantidad-egreso {
            color: #dc2626;
        }

        .fecha {
            color: #64748b;
            white-space: nowrap;
        }

        .vacio {
            text-align: center;
            color: #64748b;
            padding: 30px;
        }

        .enlace-volver {
            display: inline-block;
            margin-top: 5px;
            padding: 12px 18px;
            background: #f1f5f9;
            color: #334155;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .enlace-volver:hover {
            background: #e2e8f0;
        }

        .pie {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            padding: 35px 15px;
        }

        @media (max-width: 650px) {
            .encabezado {
                padding: 18px;
            }

            .contenedor {
                margin-top: 25px;
            }

            .tarjeta {
                padding: 20px;
            }

            .campos {
                grid-template-columns: 1fr;
            }

            .campo-completo {
                grid-column: auto;
            }

            .acciones {
                display: block;
            }

            .guardar {
                width: 100%;
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

    <a href="../index.php" class="volver">
        ← Volver al panel
    </a>
</header>

<main class="contenedor">

    <div class="titulo">
        <h1>Stock de productos</h1>
        <p class="subtitulo">
            Registrá ingresos y egresos y consultá
            el historial de movimientos.
        </p>
    </div>

    <!-- Formulario -->
    <section class="tarjeta">

        <h2 class="titulo-tarjeta">
            Registrar movimiento
        </h2>

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= mostrar($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="campos">

                <div class="campo campo-completo">
                    <label for="producto">
                        Producto
                        <span class="obligatorio">*</span>
                    </label>

                    <select
                        id="producto"
                        name="producto"
                        required
                    >
                        <option value="">
                            Seleccionar producto
                        </option>

                        <?php if ($productos): ?>
                            <?php while ($p = $productos->fetch_assoc()): ?>

                                <option
                                    value="<?= (int)$p["id_producto"] ?>"
                                    <?= (string)$producto === (string)$p["id_producto"] ? "selected" : "" ?>
                                >
                                    <?= mostrar($p["nombre"]) ?>
                                    (Stock: <?= (int)$p["stock"] ?>)
                                </option>

                            <?php endwhile; ?>
                        <?php endif; ?>

                    </select>
                </div>

                <div class="campo">
                    <label for="tipo">
                        Tipo de movimiento
                        <span class="obligatorio">*</span>
                    </label>

                    <select id="tipo" name="tipo" required>

                        <option
                            value="Ingreso"
                            <?= $tipo === "Ingreso" ? "selected" : "" ?>
                        >
                            Ingreso
                        </option>

                        <option
                            value="Egreso"
                            <?= $tipo === "Egreso" ? "selected" : "" ?>
                        >
                            Egreso
                        </option>

                    </select>
                </div>

                <div class="campo">
                    <label for="cantidad">
                        Cantidad
                        <span class="obligatorio">*</span>
                    </label>

                    <input
                        type="number"
                        id="cantidad"
                        name="cantidad"
                        min="1"
                        step="1"
                        value="<?= mostrar($cantidad) ?>"
                        placeholder="Ej: 10"
                        required
                    >
                </div>

                <div class="campo">
                    <label for="fecha">
                        Fecha
                        <span class="obligatorio">*</span>
                    </label>

                    <input
                        type="date"
                        id="fecha"
                        name="fecha"
                        value="<?= mostrar($fecha) ?>"
                        required
                    >
                </div>

            </div>

            <div class="acciones">
                <button type="submit" class="guardar">
                    Guardar movimiento
                </button>
            </div>

        </form>

    </section>

    <!-- Historial -->
    <section class="tarjeta">

        <h2 class="titulo-tarjeta">
            Historial de movimientos
        </h2>

        <div class="tabla-contenedor">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($movimientos && $movimientos->num_rows > 0): ?>

                        <?php while ($mov = $movimientos->fetch_assoc()): ?>

                            <?php
                                $tipoMovimiento = $mov["tipo"];
                                $cantidadMovimiento = (int)$mov["cantidad"];

                                $fechaMovimiento = $mov["fecha"];

                                if (!empty($fechaMovimiento)) {
                                    $timestamp = strtotime($fechaMovimiento);

                                    if ($timestamp !== false) {
                                        $fechaMovimiento = date(
                                            "d/m/Y",
                                            $timestamp
                                        );
                                    }
                                }
                            ?>

                            <tr>
                                <td class="numero">
                                    #<?= (int)$mov["id_movimiento"] ?>
                                </td>

                                <td class="producto">
                                    <?= mostrar($mov["nombre"]) ?>
                                </td>

                                <td>
                                    <span class="tipo <?= claseMovimiento($tipoMovimiento) ?>">
                                        <?= mostrar($tipoMovimiento) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($tipoMovimiento === "Ingreso"): ?>

                                        <span class="cantidad cantidad-ingreso">
                                            +<?= $cantidadMovimiento ?>
                                        </span>

                                    <?php elseif ($tipoMovimiento === "Egreso"): ?>

                                        <span class="cantidad cantidad-egreso">
                                            -<?= $cantidadMovimiento ?>
                                        </span>

                                    <?php else: ?>

                                        <span class="cantidad">
                                            <?= $cantidadMovimiento ?>
                                        </span>

                                    <?php endif; ?>
                                </td>

                                <td class="fecha">
                                    <?= mostrar($fechaMovimiento) ?>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="5" class="vacio">
                                Todavía no hay movimientos registrados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>

        </div>

    </section>

    <a href="../productos/" class="enlace-volver">
        ← Volver a Productos
    </a>

</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>