<?php
// Requiere la conexión a la base de datos y la clase Venta
require_once "../models/Conexion.php";

class Venta extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();  // Usar la conexión heredada de la clase Conexion
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
            // Llamar al procedimiento almacenado 'buscar_cliente'
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

    // Buscar productos utilizando el procedimiento almacenado
    public function buscarProducto(string $termino): array
    {
        $result = [];
        try {
            // Llamar al procedimiento almacenado 'buscar_producto'
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
            // Iniciar transacción
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

            // Obtener el ID de la venta recién insertada
            $idventa = $this->pdo->lastInsertId();

            // Insertar los productos relacionados con la venta
            $sql_productos = "INSERT INTO venta_productos (idventa, idproducto, precio, cantidad, descuento, importe)
                          VALUES (:idventa, :idproducto, :precio, :cantidad, :descuento, :importe)";
            $stmt_producto = $this->pdo->prepare($sql_productos);

            // Recorrer los productos y registrarlos
            foreach ($productos as $producto) {
                $stmt_producto->bindParam(':idventa', $idventa);
                $stmt_producto->bindParam(':idproducto', $producto['idproducto']);
                $stmt_producto->bindParam(':precio', $producto['precio']);
                $stmt_producto->bindParam(':cantidad', $producto['cantidad']);
                $stmt_producto->bindParam(':descuento', $producto['descuento']);
                $stmt_producto->bindParam(':importe', $producto['importe']);
                $stmt_producto->execute();
            }

            // Confirmar la transacción
            $this->pdo->commit();
            return ['status' => 'success', 'message' => 'Venta registrada correctamente'];
        } catch (Exception $e) {
            // Si ocurre un error, deshacer la transacción
            $this->pdo->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

}
?>