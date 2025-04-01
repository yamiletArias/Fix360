<?php
// Requiere la conexión a la base de datos y la clase Venta
require_once "../models/Conexion.php";

class Venta extends Conexion {
    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();  // Usar la conexión heredada de la clase Conexion
    }

    public function getAll(): array {
        $result = [];
        try {
            $sql = "SELECT * FROM vs_ventas ORDER BY id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        return $result;
    }

    public function add($params = []): int {
        $numRow = 0;
        try {
            // Llamar al procedimiento almacenado para registrar la venta
            $stmt = $this->pdo->prepare("CALL spRegistroVentas(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $params['tipo'],
                $params['numserie'],
                $params['numcomprobante'],
                $params['nomcliente'], // El nombre del cliente, no el id
                $params['fecha'],
                $params['moneda'],
                $params['producto'],   // Los productos en formato JSON
                $params['precio'],     // Los precios en formato JSON
                $params['cantidad'],   // Las cantidades en formato JSON
                $params['descuento']   // Los descuentos en formato JSON
            ]);
    
            // Si la ejecución fue exitosa, devuelve el ID de la venta.
            $numRow = $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            // Si ocurre un error, lanza una excepción
            throw new Exception($e->getMessage());
        }
        return $numRow;  // Regresa el número de filas afectadas (el ID de la venta)
    }
}
?>