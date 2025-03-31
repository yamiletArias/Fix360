<?php
// Incluir la clase de conexión
require_once '../models/Conexion.php';

class Cliente extends Conexion {

    // Método para buscar clientes
    public function searchClients($query) {
        try {
            // Obtener la conexión a la base de datos utilizando el método estático
            $pdo = Conexion::getConexion();  // Aquí obtenemos la conexión desde la clase Conexion
            
            // Consulta SQL para buscar clientes
            $sql = "SELECT idcliente, nombre FROM clientes WHERE nombre LIKE :query LIMIT 10"; // Ajusta la consulta según tu estructura
            
            // Preparar la consulta
            $stmt = $pdo->prepare($sql);
            
            // Ejecutar la consulta con el parámetro
            $stmt->execute(['query' => '%' . $query . '%']);
            
            // Devolver los resultados
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En caso de error en la base de datos, devolver el error en formato JSON
            return ["error" => "Error en la base de datos: " . $e->getMessage()];
        }
    }
}

// Verificar si se recibe el parámetro 'q' por GET
if (isset($_GET['q'])) {
    $query = $_GET['q']; // Obtener el término de búsqueda
    
    // Validar el parámetro para evitar caracteres inesperados
    $query = htmlspecialchars($query);  // Evitar caracteres maliciosos
    
    // Crear una instancia del modelo Cliente
    $clienteModel = new Cliente();
    
    // Buscar los clientes con el término proporcionado
    $clientes = $clienteModel->searchClients($query);

    // Establecer cabecera para JSON
    header('Content-Type: application/json; charset=utf-8');
    
    // Retornar los resultados como JSON
    echo json_encode($clientes);
}
?>
