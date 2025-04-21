<?php
require_once '../models/Conexion.php';

if (isset($_GET['idcotizacion'])) {
    $idcotizacion = $_GET['idcotizacion'];
    try {
        $conexion = new Conexion();
        $db = Conexion::getConexion();

        $stmt = $db->prepare("SELECT producto, precio, descuento 
        FROM vista_detalle_venta WHERE idcotizacion = ?");

        $stmt->execute([$idcotizacion]);

        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($detalles);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Falta parámetro idcotizacion']);
}