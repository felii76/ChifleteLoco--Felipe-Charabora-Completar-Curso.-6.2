
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

$cliente = "";
$producto = "";
$cantidad = "1";
$fecha = date("Y-m-d");
$estado = "Pendiente";

$estadosPermitidos = [
    "Pendiente",
    "Facturado",
    "Entregado",
    "Anulado"
];

// Obtener clientes y productos
$clientes = $conexion->query(
    "SELECT id_cliente, nombre, apellido
     FROM clientes
     ORDER BY nombre, apellido"
);

$productos = $conexion->query(
    "SELECT id_producto, nombre, precio
     FROM productos
     ORDER BY nombre"
);

// Guardar pedido
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $cliente = $_POST["cliente"] ?? "";
    $producto = $_POST["producto"] ?? "";
    $cantidad = $_POST["cantidad"] ?? "";
    $fecha = $_POST["fecha"] ?? "";
    $estado = $_POST["estado"] ?? "";

    $clienteId = filter_var(
        $cliente,
        FILTER_VALIDATE_INT
    );

    $productoId = filter_var(
        $producto,
        FILTER_VALIDATE_INT
    );

    $cantidadNumero = filter_var(
        $cantidad,
        FILTER_VALIDATE_INT
    );

    $fechaValida = false;

    $fechaObjeto = DateTime::createFromFormat(
        "!Y-m-d",
        $fecha
    );

    if (
        $fechaObjeto &&
        $fechaObjeto->format("Y-m-d") === $fecha
    ) {
        $fechaValida = true;
    }

    if (
        $clienteId === false ||
        $clienteId < 1 ||
        $productoId === false ||
        $productoId < 1
    ) {
        $error = "Seleccioná un cliente y un producto válidos.";

    } elseif (
        $cantidadNumero === false ||
        $cantidadNumero < 1
    ) {
        $error = "La cantidad debe ser mayor a cero.";

    } elseif (!$fechaValida) {
        $error = "Ingresá una fecha válida.";

    } elseif (!in_array($estado, $estadosPermitidos, true)) {
        $error = "Seleccioná un estado válido.";

    } else {

        try {
            // Todas las operaciones deben completarse
            $conexion->begin_transaction();

            // Verificar cliente
            $stmt = $conexion->prepare(
                "SELECT id_cliente
                 FROM clientes
                 WHERE id_cliente = ?"
            );

            $stmt->bind_param("i", $clienteId);
            $stmt->execute();

            $clienteExiste = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$clienteExiste) {
                throw new Exception("El cliente seleccionado no existe.");
            }

            // Obtener precio del producto
            $stmt = $conexion->prepare(
                "SELECT precio
                 FROM productos
                 WHERE id_producto = ?"
            );

            $stmt->bind_param("i", $productoId);
            $stmt->execute();

            $productoEncontrado = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$productoEncontrado) {
                throw new Exception("El producto seleccionado no existe.");
            }

            $precio = (float)$productoEncontrado["precio"];

            $subtotal = round(
                $precio * $cantidadNumero,
                2
            );

            // Crear pedido
            $stmt = $conexion->prepare(
                "INSERT INTO pedidos
                 (id_cliente, fecha, estado)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "iss",
                $clienteId,
                $fecha,
                $estado
            );

            if (!$stmt->execute()) {
                throw new Exception("No se pudo crear el pedido.");
            }

            $idPedido = $conexion->insert_id;
            $stmt->close();

            // Guardar detalle del pedido
            $stmt = $conexion->prepare(
                "INSERT INTO detalle_pedido
                 (id_pedido, id_producto, cantidad,
                  precio_unitario, subtotal)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "iiidd",
                $idPedido,
                $productoId,
                $cantidadNumero,
                $precio,
                $subtotal
            );

            if (!$stmt->execute()) {
                throw new Exception("No se pudo guardar el detalle.");
            }

            $stmt->close();

            // Actualizar total
            $stmt = $conexion->prepare(
                "UPDATE pedidos
                 SET total = ?
                 WHERE id_pedido = ?"
            );

            $stmt->bind_param(
                "di",
                $subtotal,
                $idPedido
            );

            if (!$stmt->execute()) {
                throw new Exception("No se pudo actualizar el total.");
            }

            $stmt->close();

            // Confirmar operaciones
            $conexion->commit();

            header("Location: index.php");
            exit;

        } catch (Throwable $e) {

            $conexion->rollback();

            error_log($e->getMessage());

            $error = "No se pudo guardar el pedido. Revisá los datos e intentá nuevamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nuevo Pedido - Chiflete Loco</title>

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

        .seccion-titulo {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 20px;
            color: #334155;
        }

        .separador {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 30px 0;
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
        ← Volver a pedidos
    </a>
</header>

<main class="contenedor">

    <div class="titulo">
        <h1>Nuevo pedido</h1>
        <p class="subtitulo">
            Completá los datos para registrar un pedido.
        </p>
    </div>

    <form method="POST" class="formulario">

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= mostrar($error) ?>
            </div>
        <?php endif; ?>

        <h2 class="seccion-titulo">
            Información del pedido
        </h2>

        <div class="campos">

            <div class="campo campo-completo">
                <label for="cliente">
                    Cliente
                    <span class="obligatorio">*</span>
                </label>

                <select
                    id="cliente"
                    name="cliente"
                    required
                >
                    <option value="">
                        Seleccionar cliente
                    </option>

                    <?php if ($clientes): ?>
                        <?php while ($c = $clientes->fetch_assoc()): ?>

                            <option
                                value="<?= (int)$c["id_cliente"] ?>"
                                <?= (string)$cliente === (string)$c["id_cliente"] ? "selected" : "" ?>
                            >
                                <?= mostrar($c["nombre"]) ?>
                                <?= mostrar($c["apellido"]) ?>
                            </option>

                        <?php endwhile; ?>
                    <?php endif; ?>

                </select>
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

            <div class="campo">
                <label for="estado">
                    Estado
                    <span class="obligatorio">*</span>
                </label>

                <select id="estado" name="estado" required>

                    <?php foreach ($estadosPermitidos as $opcion): ?>

                        <option
                            value="<?= mostrar($opcion) ?>"
                            <?= $estado === $opcion ? "selected" : "" ?>
                        >
                            <?= mostrar($opcion) ?>
                        </option>

                    <?php endforeach; ?>

                </select>
            </div>

        </div>

        <hr class="separador">

        <h2 class="seccion-titulo">
            Producto del pedido
        </h2>

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
                                - $<?= number_format(
                                    (float)$p["precio"],
                                    2,
                                    ",",
                                    "."
                                ) ?>
                            </option>

                        <?php endwhile; ?>
                    <?php endif; ?>

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
                    required
                >

                <span class="ayuda">
                    Cantidad de unidades solicitadas.
                </span>
            </div>

        </div>

        <div class="acciones">

            <a href="index.php" class="cancelar">
                Cancelar
            </a>

            <button type="submit" class="guardar">
                Guardar pedido
            </button>

        </div>

    </form>

</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>