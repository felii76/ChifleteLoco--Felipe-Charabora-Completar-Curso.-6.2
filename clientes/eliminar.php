<?php
require_once("../conexion.php");

$id = $_GET["id"];

$sql = "DELETE FROM clientes WHERE id_cliente = $id";

if ($conexion->query($sql)) {
    header("Location: index.php");
    exit;
} else {
    echo "Error al eliminar el cliente.";
}
?>