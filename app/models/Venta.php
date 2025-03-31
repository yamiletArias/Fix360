<?php

require_once "../models/Conexion.php";

class Venta extends Conexion {

    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
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
            // Inserta en la tabla ventas
            $sql = "INSERT INTO ventas (idcliente, tipocom, fechahora, numserie, numcom, moneda) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $params["idcliente"],
                $params["tipocom"],
                $params["fechahora"],
                $params["numserie"],
                $params["numcom"],
                $params["moneda"]
            ]);

            // Devuelvo el ID de la venta recién registrada
            $ventaId = $this->pdo->lastInsertId(); // Aquí obtienes el ID de la venta

            // Si se registró la venta correctamente, insertamos los detalles
            if ($ventaId) {
                $numRow = 1; // Indicamos que al menos una fila fue afectada.
            }

            return $ventaId; // Devuelvo el ID de la venta registrada
        } catch (PDOException $e) {
            // Si ocurre un error, lanzamos una excepción
            throw new Exception($e->getMessage());
        }
    }

    // Método para agregar un producto a la venta (detalleventa)
    public function addDetalleVenta($params = []): int {
        $numRow = 0;
        try {
            $sql = "INSERT INTO detalleventa (idventa, idproducto, precioventa, cantidad, descuento) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $params["idventa"],     // ID de la venta
                $params["idproducto"],  // ID del producto
                $params["precioventa"], // Precio del producto
                $params["cantidad"],    // Cantidad
                $params["descuento"]    // Descuento
            ]);
            $numRow = $stmt->rowCount(); // Número de filas afectadas
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        return $numRow;  // Regresa el número de filas afectadas
    }
}

?>
