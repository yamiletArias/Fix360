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
            throw new Exception("Error al obtener las ventas: " . $e->getMessage());
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
            throw new Exception("Error al buscar clientes: " . $e->getMessage());
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
            throw new Exception("Error al buscar productos: " . $e->getMessage());
        }
        return $result;
    }

    // Método para registrar la venta y el detalle
    public function registrarVenta($idcliente, $tipocom, $fechahora, $numserie, $numcom, $moneda, $detalle)
    {
        // Validar que el ID de cliente es numérico
        if (!is_numeric($idcliente)) {
            throw new Exception("El ID del cliente no es válido.");
        }

        // Verificar si el cliente existe
        $stmtCliente = $this->pdo->prepare("SELECT COUNT(*) FROM clientes WHERE idcliente = :idcliente");
        $stmtCliente->bindParam(':idcliente', $idcliente, PDO::PARAM_INT);
        $stmtCliente->execute();

        if ($stmtCliente->fetchColumn() == 0) {
            throw new Exception("El cliente con ID $idcliente no existe.");
        }

        // Insertar la venta y el detalle
        $sql = "CALL registrar_venta_detalle(:idcliente, :tipocom, :fechahora, :numserie, :numcom, :moneda, :detalle)";
        $stmt = $this->pdo->prepare($sql);
        $jsonDetalle = json_encode($detalle);

        $stmt->bindParam(':idcliente', $idcliente, PDO::PARAM_INT);
        $stmt->bindParam(':tipocom', $tipocom, PDO::PARAM_STR);
        $stmt->bindParam(':fechahora', $fechahora, PDO::PARAM_STR);
        $stmt->bindParam(':numserie', $numserie, PDO::PARAM_STR);
        $stmt->bindParam(':numcom', $numcom, PDO::PARAM_STR);
        $stmt->bindParam(':moneda', $moneda, PDO::PARAM_STR);
        $stmt->bindParam(':detalle', $jsonDetalle, PDO::PARAM_STR);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al registrar la venta: " . $e->getMessage());
        }
    }
}
?>
