
<?php
require_once "auth.php";

$usuario = htmlspecialchars($_SESSION["usuario"] ?? "Administrador", ENT_QUOTES, "UTF-8");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chiflete Loco - Panel de administración</title>
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
            background: #ffffff;
            padding: 20px 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .marca h1 {
            margin: 0;
            font-size: 23px;
            color: #1d4ed8;
        }

        .marca p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .salir {
            background: #fee2e2;
            color: #b91c1c;
            text-decoration: none;
            padding: 11px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .salir:hover {
            background: #fecaca;
        }

        .contenedor {
            max-width: 1050px;
            margin: 45px auto;
            padding: 0 20px;
        }

        .bienvenida {
            background: #2563eb;
            color: white;
            padding: 30px;
            border-radius: 14px;
            margin-bottom: 30px;
        }

        .bienvenida h2 {
            margin: 0 0 10px;
            font-size: 25px;
        }

        .bienvenida p {
            margin: 0;
            color: #dbeafe;
        }

        .titulo-seccion {
            margin-bottom: 20px;
            font-size: 20px;
        }

        .menu {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .tarjeta {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-decoration: none;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            transition: 0.2s;
        }

        .tarjeta:hover {
            transform: translateY(-4px);
            border-color: #93c5fd;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        }

        .icono {
            font-size: 30px;
            margin-bottom: 18px;
        }

        .tarjeta h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .tarjeta p {
            margin: 0;
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
        }

        .pie {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            padding: 35px 15px;
        }

        @media (max-width: 750px) {
            .menu {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .menu {
                grid-template-columns: 1fr;
            }

            .contenedor {
                margin-top: 25px;
            }

            .bienvenida {
                padding: 23px;
            }

            .encabezado {
                padding: 18px;
            }
        }
    </style>
</head>
<body>

<header class="encabezado">
    <div class="marca">
        <h1>Chiflete Loco</h1>
        <p>Sistema de Gestión Web</p>
    </div>

    <a href="logout.php" class="salir">Cerrar sesión</a>
</header>

<main class="contenedor">

    <section class="bienvenida">
        <h2>¡Bienvenido, <?= $usuario ?>!</h2>
        <p>Administrá tu negocio desde un solo lugar.</p>
    </section>

    <h2 class="titulo-seccion">Panel de administración</h2>

    <div class="menu">

        <a href="clientes/" class="tarjeta">
            <div class="icono">👥</div>
            <h3>Clientes</h3>
            <p>Consultá, registrá y administrá tus clientes.</p>
        </a>

        <a href="productos/" class="tarjeta">
            <div class="icono">📦</div>
            <h3>Productos</h3>
            <p>Gestioná los productos y sus precios.</p>
        </a>

        <a href="pedidos/" class="tarjeta">
            <div class="icono">📋</div>
            <h3>Pedidos</h3>
            <p>Registrá pedidos y consultá sus estados.</p>
        </a>

        <a href="stock/" class="tarjeta">
            <div class="icono">🏬</div>
            <h3>Stock</h3>
            <p>Controlá las existencias de tus productos.</p>
        </a>

        <a href="materias_primas/" class="tarjeta">
            <div class="icono">🧰</div>
            <h3>Materias primas</h3>
            <p>Administrá los materiales utilizados.</p>
        </a>

        <a href="informes/" class="tarjeta">
            <div class="icono">📊</div>
            <h3>Informes</h3>
            <p>Consultá reportes y descargá documentos PDF.</p>
        </a>

    </div>
</main>

<footer class="pie">
    Chiflete Loco - Sistema de Gestión
</footer>

</body>
</html>