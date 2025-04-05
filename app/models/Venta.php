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

    // Registrar la venta y los detalles utilizando el procedimiento almacenado
    public function registrarVentaDetalle(
        int $idcliente,
        string $tipocom,
        string $fechahora,
        string $numserie,
        string $numcom,
        string $moneda,
        array $detalleventa
    ): bool {
        try {
            $sql = "CALL registrar_venta_detalle(:idcliente, :tipocom, :fechahora, :numserie, :numcom, :moneda, :detalleventa)";
            $stmt = $this->pdo->prepare($sql);
    
            $detalle_json = json_encode($detalleventa);

            $stmt->bindParam(':idcliente', $idcliente, PDO::PARAM_INT);
            $stmt->bindParam(':tipocom', $tipocom, PDO::PARAM_STR);
            $stmt->bindParam(':fechahora', $fechahora, PDO::PARAM_STR);
            $stmt->bindParam(':numserie', $numserie, PDO::PARAM_STR);
            $stmt->bindParam(':numcom', $numcom, PDO::PARAM_STR);
            $stmt->bindParam(':moneda', $moneda, PDO::PARAM_STR);
            $stmt->bindParam(':detalleventa', $detalle_json, PDO::PARAM_STR);
    
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            // Capturar y mostrar el error exacto
            throw new Exception("Error al registrar la venta: " . $e->getMessage());
        }
    }
}
?>