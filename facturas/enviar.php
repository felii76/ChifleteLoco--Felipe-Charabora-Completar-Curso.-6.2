
<?php
require_once __DIR__ . "/../auth.php";
require_once __DIR__ . "/../conexion.php";

// Validar ID de la factura
$idFactura = filter_input(
    INPUT_POST,
    "id",
    FILTER_VALIDATE_INT
);

if (!$idFactura || $idFactura < 1) {
    header("Location: ../pedidos/index.php");
    exit;
}

// Buscar factura y obtener el ID del pedido
$stmt = $conexion->prepare(
    "SELECT id_pedido, enviada
     FROM facturas
     WHERE id_factura = ?"
);

$stmt->bind_param("i", $idFactura);
$stmt->execute();

$factura = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$factura) {
    http_response_code(404);
    exit("La factura no existe.");
}

$idPedido = (int)$factura["id_pedido"];

// Marcar como enviada si todavía no lo está
if ($factura["enviada"] !== "Si") {

    try {
        $stmt = $conexion->prepare(
            "UPDATE facturas
             SET enviada = 'Si'
             WHERE id_factura = ?"
        );

        $stmt->bind_param("i", $idFactura);
        $stmt->execute();
        $stmt->close();

    } catch (Throwable $e) {
        error_log($e->getMessage());

        http_response_code(500);
        exit("No se pudo actualizar el estado de la factura.");
    }
}

// Volver a la factura usando el ID del pedido
header("Location: ver.php?id=" . $idPedido);
exit;
?>