<?php require_once __DIR__ . "/../auth.php"; ?>
<?php
require_once("../conexion.php");

$id = $_GET["id"];
$estado = $_GET["estado"];


$sql = "UPDATE pedidos SET estado='$estado' WHERE id_pedido=$id";

if ($conexion->query($sql)) {

    
    if ($estado == "Entregado") {

        
        $detalle = $conexion->query("
            SELECT id_producto, cantidad
            FROM detalle_pedido
            WHERE id_pedido = $id
        ");

        while ($fila = $detalle->fetch_assoc()) {

            $idProducto = $fila["id_producto"];
            $cantidad = $fila["cantidad"];

            
            $conexion->query("
                UPDATE productos
                SET stock = stock - $cantidad
                WHERE id_producto = $idProducto
            ");

            
            $fecha = date("Y-m-d");

            $conexion->query("
                INSERT INTO movimientos_stock
                (id_producto, tipo, cantidad, fecha)
                VALUES
                ($idProducto, 'Salida', $cantidad, '$fecha')
            ");
        }
    }

    header("Location: ver.php?id=$id");
    exit;

} else {
    echo "Error al cambiar el estado.";
}
?>