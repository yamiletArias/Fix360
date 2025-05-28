<?php
require_once '../models/Conexion.php';

header('Content-Type: application/json');

if (empty($_GET['idventa'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Falta parámetro idventa'
    ]);
    exit;
}

$idventa = $_GET['idventa'];

try {
    $db = Conexion::getConexion();

    // Función auxiliar para obtener datos de una vista (activa o eliminada)
    function fetchByTipo(PDO $db, $idventa, $tipo)
    {
        $sql = "
          SELECT *
          FROM vista_detalle_venta
          WHERE idventa = :idventa
            AND registro_tipo = :tipo
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':idventa' => $idventa,
            ':tipo' => $tipo
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Si no hay en la vista activa, buscamos en la de eliminadas
        if (empty($rows)) {
            $sql2 = str_replace('vista_detalle_venta', 'vista_detalle_venta_eliminada', $sql);
            $stmt2 = $db->prepare($sql2);
            $stmt2->execute([
                ':idventa' => $idventa,
                ':tipo' => $tipo
            ]);
            $rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        return $rows;
    }

    // Obtener lista de productos
    $productos = fetchByTipo($db, $idventa, 'producto');

    // Obtener lista de servicios
    $servicios = fetchByTipo($db, $idventa, 'servicio');

    echo json_encode([
        'status' => 'success',
        'data' => [
            'productos' => $productos,
            'servicios' => $servicios
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}