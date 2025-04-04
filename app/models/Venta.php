<?php

require_once "../models/Conexion.php";

class Venta extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion(); 
    }

    public function getAll(): array
    {
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


    // Buscar clientes utilizando el procedimiento almacenado
    public function buscarCliente(string $termino): array
    {
        $result = [];
        try {
            $sql = "CALL buscar_cliente(:termino)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        return $result;
    }

    // Buscar productos 
    public function buscarProducto(string $termino): array
    {
        $result = [];
        try {
            $sql = "CALL buscar_producto(:termino)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        return $result;
    }

    // Método para registrar una venta
    public function registrarVenta($tipocom, $numserie, $numcom, $idcliente, $fechahora, $moneda, $productos)
    {
        try {

            $this->pdo->beginTransaction();

            // Registrar la venta principal en la tabla 'ventas'
            $sql = "INSERT INTO ventas (tipocom, numserie, numcom, idcliente, fechahora, moneda)
                VALUES (:tipocom, :numserie, :numcom, :idcliente, :fechahora, :moneda)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':tipocom', $tipocom);
            $stmt->bindParam(':numserie', $numserie);
            $stmt->bindParam(':numcom', $numcom);
            $stmt->bindParam(':idcliente', $idcliente);
            $stmt->bindParam(':fechahora', $fechahora);
            $stmt->bindParam(':moneda', $moneda);
            $stmt->execute();

            $idventa = $this->pdo->lastInsertId();

            $sql_productos = "INSERT INTO productos (idventa, idproducto, precio, cantidad, descuento)
                          VALUES (:idventa, :idproducto, :precio, :cantidad, :descuento)";
            $stmt_producto = $this->pdo->prepare($sql_productos);

            // Recorrer los productos y registrarlos
            foreach ($productos as $producto) {
                $stmt_producto->bindParam(':idventa', $idventa);
                $stmt_producto->bindParam(':idproducto', $producto['idproducto']);
                $stmt_producto->bindParam(':precio', $producto['precio']);
                $stmt_producto->bindParam(':cantidad', $producto['cantidad']);
                $stmt_producto->bindParam(':descuento', $producto['descuento']);
                //$stmt_producto->bindParam(':importe', $producto['importe']);
                $stmt_producto->execute();
            }

        } catch (Exception $e) {
            // Si ocurre un error, deshacer la transacción
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

}
?>