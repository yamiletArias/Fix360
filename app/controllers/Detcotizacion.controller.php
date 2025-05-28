<?php
require_once '../models/Conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['idcotizacion'])) {
    $idcotizacion = $_GET['idcotizacion'];
    try {
        $db = Conexion::getConexion();
        $stmt = $db->prepare(
            "SELECT * FROM vista_detalle_cotizacion WHERE idcotizacion = ?;"
        );
        $stmt->execute([$idcotizacion]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($detalles)) {
            $stmt = $db->prepare("SELECT * FROM vista_detalle_cotizacion_eliminada WHERE idcotizacion = ?");
            $stmt->execute([$idcotizacion]);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($detalles);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Falta parámetro idcotizacion']);
}
