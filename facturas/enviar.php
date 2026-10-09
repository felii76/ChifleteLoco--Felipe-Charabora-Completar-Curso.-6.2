<?php
require_once("../conexion.php");

$id = $_GET["id"];

$sql = "UPDATE facturas
        SET enviada = 'Si'
        WHERE id_factura = $id";

if ($conexion->query($sql)) {
    header("Location: ver.php?id=" . $id);
    exit;
} else {
    echo "Error al enviar la factura.";
}
?>