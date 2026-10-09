
<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../conexion.php";

$sql = "SELECT movimientos_stock.id_movimiento,
               productos.nombre,
               movimientos_stock.tipo,
               movimientos_stock.cantidad,
               movimientos_stock.fecha
        FROM movimientos_stock
        INNER JOIN productos
            ON movimientos_stock.id_producto = productos.id_producto
        ORDER BY movimientos_stock.fecha DESC,
                 movimientos_stock.id_movimiento DESC";

$resultado = $conexion->query($sql);

function mostrar($valor) {
    return htmlspecialchars(
        (string)($valor ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

function claseMovimiento($tipo) {
    switch (strtolower(trim($tipo))) {
        case "ingreso":
        case "entrada":
            return "tipo-entrada";

        case "egreso":
        case "salida":
            return "tipo-salida";

        default:
            return "tipo-otro";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Movimientos de Stock - Chiflete Loco</title>

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

        .cabecera-seccion {
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
            white-space: nowrap;
        }

        .tipo-entrada {
            background: #dcfce7;
            color: #166534;
        }

        .tipo-salida {
            background: #fee2e2;
            color: #b91c1c;
        }

        .tipo-otro {
            background: #e2e8f0;
            color: #475569;
        }

        .cantidad {
            font-weight: bold;
            white-space: nowrap;
        }

        .cantidad-entrada {
            color: #16a34a;
        }

        .cantidad-salida {
            color: #dc2626;
        }

        .fecha {
            color: #64748b;
            white-space: nowrap;
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
        <h1>Movimientos de stock</h1>

        <p class="subtitulo">
            Consultá el historial de ingresos y egresos
            de productos.
        </p>
    </div>

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

                <?php if ($resultado && $resultado->num_rows > 0): ?>

                    <?php while ($fila = $resultado->fetch_assoc()): ?>

                        <?php
                            $id = (int)$fila["id_movimiento"];

                            $tipo = $fila["tipo"];

                            $tipoNormalizado = strtolower(
                                trim($tipo)
                            );

                            $cantidad = (int)$fila["cantidad"];

                            $fechaFormateada = $fila["fecha"];

                            if (!empty($fila["fecha"])) {
                                $timestamp = strtotime(
                                    $fila["fecha"]
                                );

                                if ($timestamp !== false) {
                                    $fechaFormateada = date(
                                        "d/m/Y H:i",
                                        $timestamp
                                    );
                                }
                            }

                            $esIngreso = in_array(
                                $tipoNormalizado,
                                ["ingreso", "entrada"],
                                true
                            );

                            $esEgreso = in_array(
                                $tipoNormalizado,
                                ["egreso", "salida"],
                                true
                            );
                        ?>

                        <tr>
                            <td class="numero">
                                #<?= $id ?>
                            </td>

                            <td class="producto">
                                <?= mostrar($fila["nombre"]) ?>
                            </td>

                            <td>
                                <span class="tipo <?= claseMovimiento($tipo) ?>">
                                    <?= mostrar($tipo) ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($esIngreso): ?>

                                    <span class="cantidad cantidad-entrada">
                                        +<?= $cantidad ?>
                                    </span>

                                <?php elseif ($esEgreso): ?>

                                    <span class="cantidad cantidad-salida">
                                        -<?= $cantidad ?>
                                    </span>

                                <?php else: ?>

                                    <span class="cantidad">
                                        <?= $cantidad ?>
                                    </span>

                                <?php endif; ?>
                            </td>

                            <td class="fecha">
                                <?= mostrar($fechaFormateada) ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="vacio">
                            Todavía no hay movimientos de stock registrados.
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