
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

function formatoStock($numero) {
    return number_format(
        (float)$numero,
        2,
        ",",
        "."
    );
}

// Obtener materias primas
$sql = "SELECT *
        FROM materias_primas
        ORDER BY id_materia_prima DESC";

$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Materias Primas - Chiflete Loco</title>

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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
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

        .boton-agregar {
            display: inline-block;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 13px 19px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .boton-agregar:hover {
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

        .nombre {
            font-weight: bold;
        }

        .unidad {
            color: #64748b;
        }

        .stock {
            display: inline-block;
            padding: 7px 11px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            white-space: nowrap;
        }

        .stock-disponible {
            background: #dcfce7;
            color: #166534;
        }

        .stock-agotado {
            background: #fee2e2;
            color: #b91c1c;
        }

        .acciones {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 9px;
        }

        .boton {
            display: inline-block;
            padding: 9px 13px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            white-space: nowrap;
        }

        .boton-editar {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .boton-editar:hover {
            background: #dbeafe;
        }

        .boton-eliminar {
            background: #fee2e2;
            color: #b91c1c;
        }

        .boton-eliminar:hover {
            background: #fecaca;
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

            .cabecera-seccion {
                align-items: stretch;
                flex-direction: column;
            }

            .boton-agregar {
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

    <a href="../index.php" class="volver">
        ← Volver al panel
    </a>
</header>

<main class="contenedor">

    <div class="cabecera-seccion">

        <div>
            <h1>Materias primas</h1>

            <p class="subtitulo">
                Administrá los materiales utilizados
                y consultá su stock disponible.
            </p>
        </div>

        <a href="agregar.php" class="boton-agregar">
            + Agregar materia prima
        </a>

    </div>

    <div class="tabla-contenedor">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Unidad</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($resultado && $resultado->num_rows > 0): ?>

                    <?php while ($fila = $resultado->fetch_assoc()): ?>

                        <?php
                            $id = (int)$fila["id_materia_prima"];
                            $stock = (float)$fila["stock"];

                            $claseStock = $stock > 0
                                ? "stock-disponible"
                                : "stock-agotado";
                        ?>

                        <tr>

                            <td class="numero">
                                #<?= $id ?>
                            </td>

                            <td class="nombre">
                                <?= mostrar($fila["nombre"]) ?>
                            </td>

                            <td class="unidad">
                                <?= mostrar($fila["unidad"]) ?>
                            </td>

                            <td>
                                <span class="stock <?= $claseStock ?>">
                                    <?= formatoStock($stock) ?>
                                    <?= mostrar($fila["unidad"]) ?>
                                </span>
                            </td>

                            <td>

                                <div class="acciones">

                                    <a
                                        href="editar.php?id=<?= $id ?>"
                                        class="boton boton-editar"
                                    >
                                        Editar
                                    </a>

                                    <a
                                        href="eliminar.php?id=<?= $id ?>"
                                        class="boton boton-eliminar"
                                        onclick="return confirm('¿Estás seguro de eliminar esta materia prima?');"
                                    >
                                        Eliminar
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="vacio">
                            Todavía no hay materias primas registradas.
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