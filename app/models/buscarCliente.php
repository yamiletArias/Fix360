<?php
require_once "../models/Conexion.php";

if (isset($_GET['nombre'])) {
    $nombre = $_GET['nombre'];  // Obtener el nombre del cliente enviado desde AJAX

    try {
        // Crear la conexión a la base de datos
        $pdo = new Conexion();
        $conexion = $pdo->getConexion();

        // Buscar los clientes cuyo nombre contenga el término de búsqueda
        $sql = "SELECT idcliente, nombre FROM clientes WHERE nombre LIKE :nombre LIMIT 5"; // Limitar a 5 resultados
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':nombre', '%' . $nombre . '%');
        $stmt->execute();

        // Obtener los resultados
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retornar los resultados en formato JSON
        echo json_encode($clientes);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
