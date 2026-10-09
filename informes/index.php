
<?php
require_once __DIR__ . "/../auth.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Informes - Chiflete Loco</title>

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
            margin-bottom: 30px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 27px;
        }

        .subtitulo {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .seccion-titulo {
            font-size: 18px;
            font-weight: bold;
            color: #334155;
            margin: 0 0 20px;
        }

        .tarjetas {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .tarjeta {
            display: flex;
            flex-direction: column;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .tarjeta:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.07);
            border-color: #bfdbfe;
        }

        .icono {
            width: 48px;
            height: 48px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 10px;
            font-size: 23px;
            margin-bottom: 18px;
        }

        .nombre-informe {
            font-size: 17px;
            font-weight: bold;
            margin: 0 0 10px;
            color: #1e293b;
        }

        .descripcion {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            margin: 0 0 20px;
            flex-grow: 1;
        }

        .abrir {
            color: #2563eb;
            font-size: 13px;
            font-weight: bold;
        }

        .pie {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            padding: 35px 15px;
        }

        @media (max-width: 900px) {
            .tarjetas {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .encabezado {
                padding: 18px;
            }

            .contenedor {
                margin-top: 25px;
            }

            .tarjetas {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 23px;
            }

            .tarjeta {
                padding: 22px;
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
        <h1>Informes</h1>

        <p class="subtitulo">
            Consultá información sobre pedidos,
            materias primas y clientes.
        </p>
    </div>

    <h2 class="seccion-titulo">
        Informes disponibles
    </h2>

    <div class="tarjetas">

        <!-- Pedidos pendientes -->
        <a href="pedidos_pendientes.php" class="tarjeta">

            <div class="icono">◷</div>

            <h3 class="nombre-informe">
                Pedidos pendientes
            </h3>

            <p class="descripcion">
                Consultá los pedidos que todavía
                se encuentran pendientes.
            </p>

            <span class="abrir">
                Ver informe →
            </span>

        </a>

        <!-- Pedidos facturados -->
        <a href="pedidos_facturados.php" class="tarjeta">

            <div class="icono">▤</div>

            <h3 class="nombre-informe">
                Pedidos facturados
            </h3>

            <p class="descripcion">
                Revisá los pedidos que ya fueron
                facturados en el sistema.
            </p>

            <span class="abrir">
                Ver informe →
            </span>

        </a>

        <!-- Pedidos anulados -->
        <a href="pedidos_anulados.php" class="tarjeta">

            <div class="icono">⊗</div>

            <h3 class="nombre-informe">
                Pedidos anulados
            </h3>

            <p class="descripcion">
                Consultá el registro de pedidos
                que fueron anulados.
            </p>

            <span class="abrir">
                Ver informe →
            </span>

        </a>

        <!-- Stock de materias primas -->
        <a href="stock_materias_primas.php" class="tarjeta">

            <div class="icono">▦</div>

            <h3 class="nombre-informe">
                Stock de materias primas
            </h3>

            <p class="descripcion">
                Consultá las cantidades disponibles
                de los materiales registrados.
            </p>

            <span class="abrir">
                Ver informe →
            </span>

        </a>

        <!-- Clientes PDF -->
        <a href="clientes_pdf.php" class="tarjeta">

            <div class="icono">▧</div>

            <h3 class="nombre-informe">
                Clientes en PDF
            </h3>

            <p class="descripcion">
                Generá un documento PDF con
                el listado de clientes del sistema.
            </p>

            <span class="abrir">
                Generar PDF →
            </span>

        </a>

    </div>

</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>