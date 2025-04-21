<?php
require_once '../models/Conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['idcompra'])) {
    $idcompra = $_GET['idcompra'];
    try {
        $db = Conexion::getConexion();
        $stmt = $db->prepare(
          "SELECT producto, precio, descuento
             FROM vista_detalle_compra
            WHERE idcompra = ?"
        );
        $stmt->execute([$idcompra]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($detalles);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Falta parámetro idcompra']);
}
