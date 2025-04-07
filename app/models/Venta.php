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

    // Buscar clientes
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

    // Mostrar monedas
    public function getMonedasVentas(): array
    {
        try {
            $query = "CALL spuGetMonedasVentas()";
            $statement = $this->pdo->prepare($query);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error en model: " . $e->getMessage());
        }
    }
    //registrar venta
    public function registerVentas1($params = []): int
    {
        $numRows = 0;
        try {
            $query = "CALL spuRegisterVentas(?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute(array(
                $params["tipocom"],
                $params["fechahora"],
                $params["numserie"],
                $params["numcom"],
                $params["moneda"],
                $params["idcliente"],
                $params["idproducto"],
                $params["cantidad"],
                $params["numserie_detalle"],
                $params["precioventa"],
                $params["descuento"]
            ));

            $numRows = $stmt->rowCount(); // Número de filas afectadas

        } catch (PDOException $e) {
            error_log("Error DB: " . $e->getMessage());
            return $numRows;
        }
        return $numRows;
    }

    public function registerVentas($params = []): int
    {
        try {
            // Se asume que 'productos' es un array y se toma el primer elemento.
            $producto = $params["productos"][0] ?? null;
            if (!$producto) {
                return 0;
            }

            $query = "CALL spuRegisterVentas(?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                $params["tipocom"],
                $params["fechahora"],
                $params["numserie"],
                $params["numcom"],
                $params["moneda"],
                $params["idcliente"],
                $producto["idproducto"],
                $producto["cantidad"],
                $params["numserie"], // Si deseas que el numserie del detalle sea distinto, cámbialo
                $producto["precioventa"],
                $producto["descuento"]
            ]);

            return $stmt->rowCount(); // Retorna el número de filas afectadas
        } catch (PDOException $e) {
            error_log("Error DB: " . $e->getMessage());
            return 0;
        }
    }

}
?>