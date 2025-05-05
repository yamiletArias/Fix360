
<?php
require_once '../models/Conexion.php';

if (isset($_GET['idventa'])) {
    $idventa = $_GET['idventa'];
    try {
        $conexion = new Conexion();
        $db = Conexion::getConexion();

        // 1. Intentar primero en la vista de ventas activas
        $stmt = $db->prepare("SELECT * FROM vista_detalle_venta WHERE idventa = ?");
        $stmt->execute([$idventa]);
        $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Si no hay resultados, buscar en la vista de eliminadas
        if (empty($detalles)) {
            $stmt = $db->prepare("SELECT * FROM vista_detalle_venta_eliminada WHERE idventa = ?");
            $stmt->execute([$idventa]);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        header('Content-Type: application/json');
        echo json_encode($detalles);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Falta parámetro idventa']);
}
