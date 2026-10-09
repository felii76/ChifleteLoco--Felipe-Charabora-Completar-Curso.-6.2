<?php
require_once("../conexion.php");

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $cliente=$_POST["cliente"];
    $producto=$_POST["producto"];
    $cantidad=$_POST["cantidad"];
    $fecha=$_POST["fecha"];
    $estado=$_POST["estado"];

    $sql="INSERT INTO pedidos(id_cliente,fecha,estado)
          VALUES('$cliente','$fecha','$estado')";

    if($conexion->query($sql)){

    $idPedido = $conexion->insert_id;

    $consulta = $conexion->query("SELECT precio FROM productos WHERE id_producto=$producto");
    $prod = $consulta->fetch_assoc();

    $precio = $prod["precio"];
    $subtotal = $precio * $cantidad;

    $conexion->query("INSERT INTO detalle_pedido(id_pedido,id_producto,cantidad,precio_unitario,subtotal)
                      VALUES($idPedido,$producto,$cantidad,$precio,$subtotal)");

    $conexion->query("UPDATE pedidos SET total=$subtotal WHERE id_pedido=$idPedido");

    header("Location:index.php");
    exit;
}
}

$clientes=$conexion->query("SELECT * FROM clientes");
$productos = $conexion->query("SELECT * FROM productos");
?>



<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Pedido</title>
</head>
<body>

<h1>Nuevo Pedido</h1>

<form method="POST">

Cliente

<br>

<select name="cliente">

<?php while($c=$clientes->fetch_assoc()){ ?>

<option value="<?= $c["id_cliente"] ?>">

<?= $c["nombre"] ?> <?= $c["apellido"] ?>

</option>

<?php } ?>

</select>

<br><br>

Fecha

<br>

<input type="date" name="fecha" required>

<br><br>

Estado

<br>

<select name="estado">

<option>Pendiente</option>

<option>Facturado</option>

<option>Entregado</option>

<option>Anulado</option>

</select>
    
<br><br>

Producto

<br>

<select name="producto">

<?php while($p = $productos->fetch_assoc()){ ?>

<option value="<?= $p["id_producto"] ?>">

<?= $p["nombre"] ?>

</option>

<?php } ?>

</select>

<br><br>

Cantidad

<br>

<input type="number" name="cantidad" value="1" min="1">

<br><br>

<input type="submit" value="Guardar Pedido">

</form>

</body>
</html>