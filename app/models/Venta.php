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

    public function add($data) {
        $numRow = 0;
        try {
            // Usamos los datos que vienen en el JSON
            $stmt = $this->pdo->prepare("CALL registrarVenta(?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['idcliente'],  // Asegúrate de que este valor venga correctamente
                $data['tipocom'],
                $data['numserie'],
                $data['numcom'],
                $data['fechahora'],
                $data['moneda'],
                json_encode($data['productos'])  // Convertimos productos a JSON
            ]);
            $numRow = $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        return $numRow;
    }
    
}
?>