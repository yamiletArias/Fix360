<?php
require_once '../models/Conexion.php';

// Verificar si se está enviando el parámetro 'search' a través del método GET
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    try {
        // Obtener la conexión a la base de datos usando la clase Conexion
        $pdo = Conexion::getConexion();

        // Preparar la consulta SQL para buscar los clientes que coincidan con el texto ingresado
        $stmt = $pdo->prepare("SELECT idcliente, nomcliente FROM clientes WHERE nomcliente LIKE :search LIMIT 10");
        $stmt->execute(['search' => "%$search%"]);
        
        // Obtener los resultados como un arreglo asociativo
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Devolver los resultados como JSON
        echo json_encode($clientes);
    } catch (PDOException $e) {
        // Si ocurre un error, devolver el mensaje de error
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
