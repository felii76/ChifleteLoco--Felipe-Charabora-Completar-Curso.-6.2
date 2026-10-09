
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

function claseEstado($estado) {
    switch (strtolower(trim($estado))) {
        case "pendiente":
            return "estado-pendiente";
        case "facturado":
            return "estado-facturado";
        case "entregado":
            return "estado-entregado";
        case "anulado":
            return "estado-anulado";
        default:
            return "estado-otro";
    }
}

// Validar el ID del pedido
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

// Buscar información del pedido
$sqlPedido = "SELECT pedidos.*,
                     clientes.nombre,
                     clientes.apellido
              FROM pedidos
              INNER JOIN clientes
              ON pedidos.id_cliente = clientes.id_cliente
              WHERE pedidos.id_pedido = ?";

$stmt = $conexion->prepare($sqlPedido);
$stmt->bind_param("i", $id);
$stmt->execute();

$pedido = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pedido) {
    http_response_code(404);
    exit("El pedido no existe.");
}

// Buscar los productos del pedido
$sqlDetalle = "SELECT productos.nombre,
                      detalle_pedido.cantidad,
                      detalle_pedido.precio_unitario,
                      detalle_pedido.subtotal
               FROM detalle_pedido
               INNER JOIN productos
               ON detalle_pedido.id_producto = productos.id_producto
               WHERE detalle_pedido.id_pedido = ?";

$stmt = $conexion->prepare($sqlDetalle);
$stmt->bind_param("i", $id);
$stmt->execute();

$detalle = $stmt->get_result();
$productosPedido = $detalle->fetch_all(MYSQLI_ASSOC);

$stmt->close();

$estado = $pedido["estado"];

// Formatear fecha
$fechaFormateada = $pedido["fecha"];

if (!empty($pedido["fecha"])) {
    $timestamp = strtotime($pedido["fecha"]);

    if ($timestamp !== false) {
        $fechaFormateada = date("d/m/Y", $timestamp);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pedido #<?= $id ?> - Chiflete Loco</title>

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
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .cabecera-pedido {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
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
            padding: 25px;
            margin-bottom: 25px;
        }

        .titulo-tarjeta {
            font-size: 17px;
            margin: 0 0 22px;
            color: #334155;
        }

        .datos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .dato {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .etiqueta {
            font-size: 13px;
            color: #64748b;
        }

        .valor {
            font-size: 15px;
            font-weight: bold;
        }

        .estado {
            display: inline-block;
            width: fit-content;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
        }

        .estado-pendiente {
            background: #fef3c7;
            color: #92400e;
        }

        .estado-facturado {
            background: #dcfce7;
            color: #166534;
        }

        .estado-entregado {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .estado-anulado {
            background: #fee2e2;
            color: #b91c1c;
        }

        .estado-otro {
            background: #e2e8f0;
            color: #475569;
        }

        .acciones {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 25px;
        }

        .boton {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        .boton-azul {
            background: #2563eb;
            color: white;
        }

        .boton-azul:hover {
            background: #1d4ed8;
        }

        .boton-verde {
            background: #16a34a;
            color: white;
        }

        .boton-verde:hover {
            background: #15803d;
        }

        .boton-gris {
            background: #f1f5f9;
            color: #334155;
        }

        .boton-gris:hover {
            background: #e2e8f0;
        }

        .mensaje {
            margin-top: 22px;
            padding: 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .mensaje-entregado {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .mensaje-anulado {
            background: #fee2e2;
            color: #b91c1c;
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
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 16px 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .producto {
            font-weight: bold;
        }

        .importe {
            white-space: nowrap;
        }

        .vacio {
            text-align: center;
            padding: 25px;
            color: #64748b;
        }

        .resumen-total {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 22px;
            margin-top: 15px;
        }

        .texto-total {
            color: #64748b;
            font-size: 15px;
            font-weight: bold;
        }

        .monto-total {
            font-size: 26px;
            font-weight: bold;
            color: #1d4ed8;
        }

        .pie {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            padding: 35px 15px;
        }

        @media (max-width: 650px) {
            .datos {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .contenedor {
                margin-top: 25px;
            }

            .tarjeta {
                padding: 20px;
            }

            h1 {
                font-size: 23px;
            }

            .resumen-total {
                justify-content: space-between;
            }

            .monto-total {
                font-size: 21px;
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

    <div class="cabecera-pedido">
        <div>
            <h1>Pedido #<?= $id ?></h1>
            <p class="subtitulo">
                Información y detalle del pedido.
            </p>
        </div>
    </div>

    <!-- Información general -->
    <section class="tarjeta">

        <h2 class="titulo-tarjeta">
            Información del pedido
        </h2>

        <div class="datos">

            <div class="dato">
                <span class="etiqueta">Cliente</span>

                <span class="valor">
                    <?= mostrar($pedido["nombre"]) ?>
                    <?= mostrar($pedido["apellido"]) ?>
                </span>
            </div>

            <div class="dato">
                <span class="etiqueta">Fecha</span>

                <span class="valor">
                    <?= mostrar($fechaFormateada) ?>
                </span>
            </div>

            <div class="dato">
                <span class="etiqueta">Estado</span>

                <span class="estado <?= claseEstado($estado) ?>">
                    <?= mostrar($estado) ?>
                </span>
            </div>

        </div>

        <!-- Acciones según el estado -->
        <?php if ($estado === "Pendiente"): ?>

            <div class="acciones">
                <a
                    href="cambiar_estado.php?id=<?= $id ?>&estado=Facturado"
                    class="boton boton-verde"
                    onclick="return confirm('¿Querés facturar este pedido?');"
                >
                    Facturar pedido
                </a>
            </div>

        <?php elseif ($estado === "Facturado"): ?>

            <div class="acciones">

                <a
                    href="cambiar_estado.php?id=<?= $id ?>&estado=Entregado"
                    class="boton boton-azul"
                    onclick="return confirm('¿Marcar este pedido como entregado?');"
                >
                    Marcar como entregado
                </a>

                <a
                    href="../facturas/ver.php?id=<?= $id ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="boton boton-gris"
                >
                    Ver factura
                </a>

            </div>

        <?php elseif ($estado === "Entregado"): ?>

            <div class="mensaje mensaje-entregado">
                Este pedido ya fue entregado.
            </div>

            <div class="acciones">
                <a
                    href="../facturas/ver.php?id=<?= $id ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="boton boton-gris"
                >
                    Ver factura
                </a>
            </div>

        <?php elseif ($estado === "Anulado"): ?>

            <div class="mensaje mensaje-anulado">
                Este pedido fue anulado.
            </div>

        <?php endif; ?>

    </section>

    <!-- Productos del pedido -->
    <section class="tarjeta">

        <h2 class="titulo-tarjeta">
            Productos del pedido
        </h2>

        <div class="tabla-contenedor">

            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($productosPedido) > 0): ?>

                        <?php foreach ($productosPedido as $fila): ?>

                            <tr>
                                <td class="producto">
                                    <?= mostrar($fila["nombre"]) ?>
                                </td>

                                <td>
                                    <?= (int)$fila["cantidad"] ?>
                                </td>

                                <td class="importe">
                                    $<?= number_format(
                                        (float)$fila["precio_unitario"],
                                        2,
                                        ",",
                                        "."
                                    ) ?>
                                </td>

                                <td class="importe">
                                    <strong>
                                        $<?= number_format(
                                            (float)$fila["subtotal"],
                                            2,
                                            ",",
                                            "."
                                        ) ?>
                                    </strong>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="4" class="vacio">
                                No hay productos asociados a este pedido.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>

        </div>

        <div class="resumen-total">

            <span class="texto-total">
                Total del pedido
            </span>

            <span class="monto-total">
                $<?= number_format(
                    (float)$pedido["total"],
                    2,
                    ",",
                    "."
                ) ?>
            </span>

        </div>

    </section>

    <a href="index.php" class="boton boton-gris">
        ← Volver al listado
    </a>

</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>