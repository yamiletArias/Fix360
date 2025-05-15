<?php
require_once "../models/Conexion.php";

class Venta extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
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
            // Loguea en el server
            error_log("Error DB en registerVentas: " . $e->getMessage());
            // Devuélvelo como JSON y termina la ejecución:
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Lista ventas por periodo: dia, semana, mes
     * 
     * @param string $modo dia - semana - mes
     * @param string $fecha Fecha en formato YYYY-MM-DD
     * @return array
     */
    public function listarPorPeriodoVentas(string $modo, string $fecha): array
    {
        try {
            $stmt = $this->pdo->prepare("CALL spListVentasPorPeriodo(:modo, :fecha)");
            $stmt->execute([
                ':modo' => $modo,
                ':fecha' => $fecha,
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            error_log("Ventas::listarPorPeriodoVentas error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * VISTA DE VENTAS ELIMINADAS (estado = FALSE)
     */
    public function getVentasEliminadas(): array
    {
        $result = [];
        try {
            // Consulta la vista vs_ventas_eliminadas
            $sql = "SELECT id, cliente, tipocom, numcom FROM vs_ventas_eliminadas";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            // Obtiene todos los resultados de la consulta
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las ventas eliminadas: " . $e->getMessage());
        }
        return $result;
    }

    /**  
     * Devuelve la justificación de eliminación para una venta  
     */
    public function getJustificacion(int $idventa): ?string
    {
        $sql = "SELECT justificacion FROM vista_justificacion_venta WHERE idventa = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idventa]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['justificacion'] : null;
    }

}
?>