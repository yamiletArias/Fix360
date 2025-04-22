<?php
require_once '../models/Conexion.php';

if (isset($_GET['idcompra'])) {
    $idcompra = $_GET['idcompra'];
    try {
        $conexion = new Conexion();
        $db = Conexion::getConexion();

        $stmt = $db->prepare("SELECT producto, precio, descuento 
        FROM vista_detalle_compra WHERE idcompra = ?");

        $stmt->execute([$idcompra]);

        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($detalles);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Falta parámetro idcotizacion']);
}