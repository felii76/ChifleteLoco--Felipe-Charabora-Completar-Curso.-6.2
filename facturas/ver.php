
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

function formatoPrecio($valor) {
    return "$ " . number_format(
        (float)$valor,
        2,
        ",",
        "."
    );
}

function formatoFecha($fecha) {
    $timestamp = strtotime((string)$fecha);

    if ($timestamp === false) {
        return $fecha;
    }

    return date("d/m/Y", $timestamp);
}

// Validar ID del pedido
$idPedido = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$idPedido || $idPedido < 1) {
    header("Location: ../pedidos/index.php");
    exit;
}

// Buscar pedido y cliente
$stmt = $conexion->prepare(
    "SELECT pedidos.*,
            clientes.nombre,
            clientes.apellido,
            clientes.dni,
            clientes.direccion,
            clientes.telefono,
            clientes.email
     FROM pedidos
     INNER JOIN clientes
         ON pedidos.id_cliente = clientes.id_cliente
     WHERE pedidos.id_pedido = ?"
);

$stmt->bind_param("i", $idPedido);
$stmt->execute();

$pedido = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pedido) {
    http_response_code(404);
    exit("El pedido no existe.");
}

// Buscar factura existente
$stmt = $conexion->prepare(
    "SELECT *
     FROM facturas
     WHERE id_pedido = ?
     LIMIT 1"
);

$stmt->bind_param("i", $idPedido);
$stmt->execute();

$factura = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Si no existe, crear la factura
if (!$factura) {

    $fecha = date("Y-m-d");
    $total = (float)$pedido["total"];

    $stmt = $conexion->prepare(
        "INSERT INTO facturas
         (id_pedido, fecha, total)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "isd",
        $idPedido,
        $fecha,
        $total
    );

    if (!$stmt->execute()) {
        exit("No se pudo generar la factura.");
    }

    $idFactura = $conexion->insert_id;
    $stmt->close();

    $factura = [
        "id_factura" => $idFactura,
        "id_pedido" => $idPedido,
        "fecha" => $fecha,
        "total" => $total,
        "enviada" => "No"
    ];
}

// Obtener detalle del pedido
$stmt = $conexion->prepare(
    "SELECT productos.nombre,
            detalle_pedido.cantidad,
            detalle_pedido.precio_unitario,
            detalle_pedido.subtotal
     FROM detalle_pedido
     INNER JOIN productos
         ON detalle_pedido.id_producto = productos.id_producto
     WHERE detalle_pedido.id_pedido = ?"
);

$stmt->bind_param("i", $idPedido);
$stmt->execute();

$detalle = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$idFactura = (int)$factura["id_factura"];
$enviada = $factura["enviada"] === "Si";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Factura #<?= $idFactura ?> - Chiflete Loco</title>

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
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .factura {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 40px;
        }

        .cabecera-factura {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding-bottom: 28px;
            border-bottom: 2px solid #e2e8f0;
        }

        .empresa {
            font-size: 27px;
            font-weight: bold;
            color: #1d4ed8;
            margin: 0 0 8px;
        }

        .empresa-subtitulo {
            color: #64748b;
            font-size: 13px;
            margin: 0;
        }

        .numero-factura {
            text-align: right;
        }

        .numero-factura h1 {
            font-size: 25px;
            margin: 0 0 8px;
            color: #1e293b;
        }

        .numero-factura p {
            font-size: 14px;
            color: #64748b;
            margin: 5px 0;
        }

        .datos {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px 0;
        }

        .titulo-seccion {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 17px;
        }

        .dato {
            margin: 10px 0;
            font-size: 14px;
            line-height: 1.5;
        }

        .dato strong {
            color: #475569;
        }

        .tabla-contenedor {
            overflow-x: auto;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #eff6ff;
            color: #1d4ed8;
            padding: 15px;
            font-size: 13px;
        }

        td {
            padding: 16px 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .importe {
            text-align: right;
            white-space: nowrap;
        }

        .vacio {
            text-align: center;
            color: #64748b;
        }

        .total-contenedor {
            display: flex;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .total {
            min-width: 270px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 18px 22px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
        }

        .estado-contenedor {
            border-top: 1px solid #e2e8f0;
            margin-top: 35px;
            padding-top: 25px;
        }

        .estado-titulo {
            font-size: 14px;
            font-weight: bold;
            margin-right: 10px;
        }

        .estado {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .estado-enviada {
            background: #dcfce7;
            color: #166534;
        }

        .estado-pendiente {
            background: #fef3c7;
            color: #92400e;
        }

        .acciones {
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 25px;
        }

        .boton {
            display: inline-block;
            padding: 13px 19px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }

        .boton-enviar {
            background: #2563eb;
            color: white;
        }

        .boton-enviar:hover {
            background: #1d4ed8;
        }

        .boton-imprimir {
            background: #f1f5f9;
            color: #334155;
        }

        .boton-imprimir:hover {
            background: #e2e8f0;
        }

        .pie-factura {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 35px;
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
                margin-top: 20px;
            }

            .factura {
                padding: 22px;
            }

            .cabecera-factura {
                flex-direction: column;
            }

            .numero-factura {
                text-align: left;
            }

            .datos {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .total {
                width: 100%;
                min-width: 0;
                font-size: 16px;
            }

            .acciones {
                flex-direction: column;
            }

            .boton {
                text-align: center;
                width: 100%;
            }
        }

        /* Diseño para impresión */
        @media print {
            body {
                background: white;
            }

            .encabezado,
            .acciones,
            .pie {
                display: none !important;
            }

            .contenedor {
                max-width: none;
                margin: 0;
                padding: 0;
            }

            .factura {
                border: none;
                border-radius: 0;
                padding: 0;
            }

            .cabecera-factura,
            .datos,
            .total-contenedor,
            .estado-contenedor {
                break-inside: avoid;
            }

            tr {
                break-inside: avoid;
            }

            @page {
                size: A4;
                margin: 15mm;
            }
        }
    </style>
</head>

<body>

<header class="encabezado">

    <a href="../index.php" class="marca">
        Chiflete Loco
    </a>

    <a
        href="../pedidos/ver.php?id=<?= $idPedido ?>"
        class="volver"
    >
        ← Volver al pedido
    </a>

</header>

<main class="contenedor">

    <article class="factura">

        <div class="cabecera-factura">

            <div>
                <h2 class="empresa">
                    CHIFLETE LOCO
                </h2>

                <p class="empresa-subtitulo">
                    Comprobante del sistema de gestión
                </p>
            </div>

            <div class="numero-factura">
                <h1>Factura #<?= $idFactura ?></h1>

                <p>
                    Fecha:
                    <?= mostrar(formatoFecha($factura["fecha"])) ?>
                </p>

                <p>
                    Pedido #<?= $idPedido ?>
                </p>
            </div>

        </div>

        <div class="datos">

            <section>
                <h3 class="titulo-seccion">
                    Datos del cliente
                </h3>

                <p class="dato">
                    <strong>Nombre:</strong>
                    <?= mostrar($pedido["nombre"]) ?>
                    <?= mostrar($pedido["apellido"]) ?>
                </p>

                <p class="dato">
                    <strong>DNI:</strong>
                    <?= mostrar($pedido["dni"]) ?>
                </p>

                <p class="dato">
                    <strong>Dirección:</strong>
                    <?= mostrar($pedido["direccion"]) ?>
                </p>

                <p class="dato">
                    <strong>Teléfono:</strong>
                    <?= mostrar($pedido["telefono"]) ?>
                </p>

                <p class="dato">
                    <strong>Email:</strong>
                    <?= mostrar($pedido["email"]) ?>
                </p>
            </section>

            <section>
                <h3 class="titulo-seccion">
                    Información del comprobante
                </h3>

                <p class="dato">
                    <strong>Número:</strong>
                    #<?= $idFactura ?>
                </p>

                <p class="dato">
                    <strong>Fecha de emisión:</strong>
                    <?= mostrar(formatoFecha($factura["fecha"])) ?>
                </p>

                <p class="dato">
                    <strong>Pedido asociado:</strong>
                    #<?= $idPedido ?>
                </p>
            </section>

        </div>

        <h3 class="titulo-seccion">
            Detalle de productos
        </h3>

        <div class="tabla-contenedor">

            <table>

                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th class="importe">Precio unitario</th>
                        <th class="importe">Subtotal</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($detalle)): ?>

                        <?php foreach ($detalle as $fila): ?>

                            <tr>
                                <td>
                                    <?= mostrar($fila["nombre"]) ?>
                                </td>

                                <td>
                                    <?= mostrar($fila["cantidad"]) ?>
                                </td>

                                <td class="importe">
                                    <?= formatoPrecio(
                                        $fila["precio_unitario"]
                                    ) ?>
                                </td>

                                <td class="importe">
                                    <?= formatoPrecio(
                                        $fila["subtotal"]
                                    ) ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="4" class="vacio">
                                No hay productos registrados
                                para este pedido.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <div class="total-contenedor">
            <div class="total">

                <span>Total</span>

                <span>
                    <?= formatoPrecio($factura["total"]) ?>
                </span>

            </div>
        </div>

        <div class="estado-contenedor">

            <span class="estado-titulo">
                Estado de envío:
            </span>

            <?php if ($enviada): ?>

                <span class="estado estado-enviada">
                    Enviada
                </span>

            <?php else: ?>

                <span class="estado estado-pendiente">
                    Pendiente de envío
                </span>

            <?php endif; ?>

        </div>

        <p class="pie-factura">
            Chiflete Loco - Sistema de Gestión
        </p>

    </article>

    <div class="acciones">

        <button
            type="button"
            class="boton boton-imprimir"
            onclick="window.print()"
        >
            Imprimir / Guardar PDF
        </button>

        <?php if (!$enviada): ?>

            <a
                href="enviar.php?id=<?= $idFactura ?>"
                class="boton boton-enviar"
            >
                Enviar factura
            </a>

        <?php endif; ?>

    </div>

</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>