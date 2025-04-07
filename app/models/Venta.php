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

    public function registerVentas($params = []): int
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

            $numRows = $stmt->rowCount();

        } catch (PDOException $e) {
            error_log("Error DB: " . $e->getMessage());
            return $numRows;
        }
        return $numRows;
    }

    public function registrarVenta1(
        string $tipocom,
        string $fechahora,
        string $numserie,
        string $numcom,
        string $moneda,
        int $idcliente,
        int $idproducto,
        int $cantidad,
        string $numserie_detalle,
        float $precioventa,
        float $descuento
    ): bool {
        try {
            // Prepare SQL to call the stored procedure
            $sql = "CALL spuRegisterVentas(
                        :tipocom, :fechahora, :numserie, :numcom, 
                        :moneda, :idcliente, :idproducto, :cantidad, 
                        :numserie_detalle, :precioventa, :descuento
                    )";

            // Prepare statement
            $stmt = $this->pdo->prepare($sql);

            // Bind parameters to the SQL statement
            $stmt->bindParam(':tipocom', $tipocom, PDO::PARAM_STR);
            $stmt->bindParam(':fechahora', $fechahora, PDO::PARAM_STR);
            $stmt->bindParam(':numserie', $numserie, PDO::PARAM_STR);
            $stmt->bindParam(':numcom', $numcom, PDO::PARAM_STR);
            $stmt->bindParam(':moneda', $moneda, PDO::PARAM_STR);
            $stmt->bindParam(':idcliente', $idcliente, PDO::PARAM_INT);
            $stmt->bindParam(':idproducto', $idproducto, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':numserie_detalle', $numserie_detalle, PDO::PARAM_STR); // JSON data
            $stmt->bindParam(':precioventa', $precioventa, PDO::PARAM_STR);
            $stmt->bindParam(':descuento', $descuento, PDO::PARAM_STR);

            // Execute the statement
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al registrar la venta: " . $e->getMessage());
        }
    }

}
?>