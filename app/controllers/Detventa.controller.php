<?php
require_once '../models/Conexion.php';

if (isset($_GET['idventa'])) {
    $idventa = $_GET['idventa'];
    try {
        $conexion = new Conexion();
        $db = Conexion::getConexion();

        $detalle = [];
        $justificacion = null;

        // 1. Intentar obtener detalle de venta activa
        $stmt = $db->prepare("SELECT * FROM vista_detalle_venta WHERE idventa = ?");
        $stmt->execute([$idventa]);
        $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Si no hay resultados, buscar en vista de eliminadas + obtener justificación
        if (empty($detalle)) {
            $stmt = $db->prepare("SELECT * FROM vista_detalle_venta_eliminada WHERE idventa = ?");
            $stmt->execute([$idventa]);
            $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener justificación de la venta eliminada
            $stmt = $db->prepare("SELECT justificacion FROM vista_justificacion_venta WHERE idventa = ?");
            $stmt->execute([$idventa]);
            $justificacionData = $stmt->fetch(PDO::FETCH_ASSOC);
            $justificacion = $justificacionData['justificacion'] ?? null;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'detalle' => $detalle,
            'justificacion' => $justificacion
        ]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Falta parámetro idventa']);
}
