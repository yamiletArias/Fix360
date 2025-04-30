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
            $sql = "SELECT * FROM vs_ventas ORDER BY id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las ventas: " . $e->getMessage());
        }
        return $result;
    }

    //ELIMINAR VENTA
    public function deleteVenta(int $idventa, string $justificacion = null): bool
    {
      try {
        $sql = "CALL spuDeleteVenta(:idventa, :justificacion)";
        $stmt = $this->pdo->prepare($sql);
        $res = $stmt->execute([
          ':idventa' => $idventa,
          ':justificacion' => $justificacion
        ]);
    
        error_log("Procedimiento spuDeleteVenta ejecutado.");
        return $res;
      } catch (PDOException $e) {
        error_log("Error al ejecutar spuDeleteVenta para compra #{$idventa}: " . $e->getMessage());
        return false;
      }
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

    //Registrar ventas con detalle de venta
    public function registerVentas($params = []): int
    {
        try {
            $pdo = $this->pdo;
            $pdo->beginTransaction();

            error_log("Parametros para spuRegisterVenta: " . print_r($params, true));

            $stmtVenta = $pdo->prepare("CALL spuRegisterVenta(?,?,?,?,?,?,?,?)");
            $stmtVenta->execute([
                $params["tipocom"],
                $params["fechahora"],
                $params["numserie"],
                $params["numcom"],
                $params["moneda"],
                $params["idcliente"],
                $params["idvehiculo"],
                $params["kilometraje"]
            ]);

            $result = [];

            do {
                $tmp = $stmtVenta->fetch(PDO::FETCH_ASSOC);
                error_log("Resultado fetch: " . print_r($tmp, true)); // NUEVO LOG
                if ($tmp && isset($tmp['idventa'])) {
                    $result = $tmp;
                    break;
                }
            } while ($stmtVenta->nextRowset());

            $stmtVenta->closeCursor();

            $idventa = $result['idventa'] ?? 0;

            if (!$idventa) {
                error_log("SP ejecutado pero no devolvió ID de venta.");
                throw new Exception("No se pudo obtener el id de la venta.");
            }

            $stmtDetalle = $pdo->prepare("CALL spuInsertDetalleVenta(?,?,?,?,?,?)");
            foreach ($params["productos"] as $producto) {
                error_log("Insertando producto ID: " . $producto["idproducto"]);
                $stmtDetalle->execute([
                    $idventa,
                    $producto["idproducto"],
                    $producto["cantidad"],
                    $params["numserie"],
                    $producto["precio"],
                    $producto["descuento"]
                ]);
            }

            $pdo->commit();
            error_log("Venta registrada con id: " . $idventa);
            return $idventa;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error DB: " . $e->getMessage());
            return 0;
        } catch (Exception $ex) {
            error_log("Error: " . $ex->getMessage());
            return 0;
        }
    }
}
?>