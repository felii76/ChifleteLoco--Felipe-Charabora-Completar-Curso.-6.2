
<?php
require_once "auth.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chiflete Loco - Sistema de Gestión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            text-align: center;
            padding: 40px 15px;
        }

        h1 { color: #263238; }

        .menu {
            max-width: 450px;
            margin: 30px auto;
        }

        .boton {
            display: block;
            background: #2563eb;
            color: white;
            padding: 15px;
            margin: 12px 0;
            border-radius: 8px;
            text-decoration: none;
        }

        .boton:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>

    <h1>Chiflete Loco</h1>
    <p>Sistema de Gestión Web</p>

    <div class="menu">
        <a class="boton" href="clientes/">Clientes</a>
        <a class="boton" href="productos/">Productos</a>
        <a class="boton" href="pedidos/">Pedidos</a>
        <a class="boton" href="stock/">Stock</a>
        <a class="boton" href="materias_primas/">Materias Primas</a>
        <a class="boton" href="informes/">Informes</a>
        <a class="boton" href="logout.php" style="background: #dc2626;">
    Cerrar sesión
</a>
    </div>

</body>
</html>
