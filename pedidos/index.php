
<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../conexion.php";

$sql = "SELECT pedidos.id_pedido,
               clientes.nombre,
               clientes.apellido,
               pedidos.fecha,
               pedidos.estado,
               pedidos.total
        FROM pedidos
        INNER JOIN clientes
        ON pedidos.id_cliente = clientes.id_cliente
        ORDER BY pedidos.id_pedido DESC";

$resultado = $conexion->query($sql);

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

        case "anulado":
            return "estado-anulado";

        default:
            return "estado-otro";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Chiflete Loco</title>

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
            max-width: 1150px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .cabecera-seccion {
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

        .agregar {
            background: #2563eb;
            color: white;
            padding: 13px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            white-space: nowrap;
        }

        .agregar:hover {
            background: #1d4ed8;
        }

        .tabla-contenedor {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
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

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .numero-pedido {
            font-weight: bold;
            color: #2563eb;
        }

        .cliente {
            font-weight: bold;
        }

        .fecha {
            color: #64748b;
            white-space: nowrap;
        }

        .total {
            font-weight: bold;
            white-space: nowrap;
        }

        .estado {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .estado-pendiente {
            background: #fef3c7;
            color: #92400e;
        }

        .estado-facturado {
            background: #dcfce7;
            color: #166534;
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
            gap: 8px;
        }

        .ver, .editar {
            display: inline-block;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            white-space: nowrap;
        }

        .ver {
            background: #f1f5f9;
            color: #334155;
        }

        .editar {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .ver:hover {
            background: #e2e8f0;
        }

        .editar:hover {
            background: #bfdbfe;
        }

        .vacio {
            text-align: center;
            padding: 35px;
            color: #64748b;
        }

        .pie {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            padding: 35px 15px;
        }

        @media (max-width: 650px) {
            .cabecera-seccion {
                flex-direction: column;
                align-items: flex-start;
            }

            .encabezado {
                padding: 18px;
            }

            .contenedor {
                margin-top: 25px;
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

    <div class="cabecera-seccion">
        <div>
            <h1>Gestión de pedidos</h1>
            <p class="subtitulo">
                Consultá los pedidos, sus estados y los importes.
            </p>
        </div>

        <a href="agregar.php" class="agregar">
            + Nuevo pedido
        </a>
    </div>

    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>

                    <?php while ($fila = $resultado->fetch_assoc()): ?>

                        <?php
                            $id = (int)$fila["id_pedido"];
                            $estado = $fila["estado"];
                            $total = (float)$fila["total"];

                            $fecha = $fila["fecha"];
                            $fechaFormateada = $fecha;

                            if (!empty($fecha)) {
                                $timestamp = strtotime($fecha);

                                if ($timestamp !== false) {
                                    $fechaFormateada = date(
                                        "d/m/Y",
                                        $timestamp
                                    );
                                }
                            }
                        ?>

                        <tr>
                            <td class="numero-pedido">
                                #<?= $id ?>
                            </td>

                            <td class="cliente">
                                <?= mostrar($fila["nombre"]) ?>
                                <?= mostrar($fila["apellido"]) ?>
                            </td>

                            <td class="fecha">
                                <?= mostrar($fechaFormateada) ?>
                            </td>

                            <td>
                                <span class="estado <?= claseEstado($estado) ?>">
                                    <?= mostrar($estado) ?>
                                </span>
                            </td>

                            <td class="total">
                                $<?= number_format($total, 2, ",", ".") ?>
                            </td>

                            <td>
                                <div class="acciones">

                                    <a
                                        href="ver.php?id=<?= $id ?>"
                                        class="ver"
                                    >
                                        Ver
                                    </a>

                                    <?php if ($estado === "Pendiente"): ?>
                                        <a
                                            href="editar.php?id=<?= $id ?>"
                                            class="editar"
                                        >
                                            Editar
                                        </a>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="vacio">
                            Todavía no hay pedidos registrados.
                        </td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>