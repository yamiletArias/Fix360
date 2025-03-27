<?php
require_once '../config/Server.php';

if (isset($_GET['search']) && isset($_GET['type'])) {
    $search = $_GET['search'];
    $type = $_GET['type']; // 'cliente' o 'producto'

    try {
        // Conexión a la base de datos
        $pdo = new PDO(SGBD, USER, PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($type == 'cliente') {
            // Consulta SQL para buscar clientes
            $stmt = $pdo->prepare("SELECT c.idcliente, CONCAT(p.nombres, ' ', p.apellidos) AS nomcliente 
                                   FROM clientes c
                                   JOIN personas p ON c.idpersona = p.idpersona
                                   WHERE CONCAT(p.nombres, ' ', p.apellidos) LIKE :search LIMIT 10");
            $stmt->execute(['search' => '%' . $search . '%']);

            // Obtener los resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif ($type == 'producto') {
            // Consulta SQL para buscar productos
            $stmt = $pdo->prepare("SELECT p.idproducto, p.descripcion, p.precio 
                                   FROM productos p 
                                   WHERE p.descripcion LIKE :search LIMIT 10");
            $stmt->execute(['search' => '%' . $search . '%']);

            // Obtener los resultados
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Si el tipo no es válido, devolver error
            echo json_encode(['error' => 'Tipo de búsqueda no válido']);
            exit;
        }

        // Devolver los resultados en formato JSON
        echo json_encode($resultados);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al realizar la búsqueda: ' . $e->getMessage()]);
    }
}
?>

