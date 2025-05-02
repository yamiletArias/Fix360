<?php
// app/models/OrdenServicio.php
require_once "../models/Conexion.php";

class OrdenServicio extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    /**
     * Registra la orden (cabecera + detalle) usando SP separados
     * @param array $p {
     *   @var int    idadmin
     *   @var int    idmecanico
     *   @var int    idpropietario
     *   @var int    idcliente
     *   @var int    idvehiculo
     *   @var float  kilometraje
     *   @var string observaciones
     *   @var bool   ingresogrua
     *   @var string fechaingreso  Formato 'YYYY-MM-DD HH:MM:SS'
     *   @var string fecharecordatorio Formato 'YYYY-MM-DD'
     *   @var array  detalle      Array de {idservicio, precio}
     * }
     * @return int ID de la orden (0 si falla)
     */
    public function registerOrdenServicio($params = []): int
    {
        try {
            $pdo= $this->pdo;
            $pdo->beginTransaction();
            error_log("Parametros para spRegisterOrdenServicio: ". print_r($params,true));

            $stmtOrden = $pdo->prepare("CALL spRegisterOrdenServicio(?,?,?,?,?,?,?,?)");
            $stmtOrden->execute([
                $params["idadmin"],
                $params["idpropietario"],
                $params["idcliente"],
                $params["idvehiculo"],
                $params["kilometraje"],
                $params["observaciones"],
                $params["ingresogrua"],
                $params["fechaingreso"]
            ]);
            error_log("Sp SpRegisterOrdenServicio ejecutado");

            $result = [];
            do{
                $tmp = $stmtOrden->fetch(PDO::FETCH_ASSOC);
                error_log("Resultado fetch: " . print_r($tmp,true));
                if($tmp && isset($tmp['idorden'])){
                    $result = $tmp;
                    break;
                }
            } while($stmtOrden->nextRowset());
            $stmtOrden->closeCursor();

            $idorden = $result['idorden'] ?? 0;

            if (!$idorden){
                error_log("SP ejecutado pero no devolvio ID de orden");
                throw new Exception("No se pudo obtener el id de orden");
            }
            $stmtDetalle = $pdo->prepare("CALL spInsertDetalleOrden(?,?,?,?)");
            $idorden = $result['idorden'] ?? 0;

            foreach ($params['servicios'] as $servicio){
                $stmtDetalle->execute([
                    $idorden,
                    $servicio["idservicio"],
                    $servicio["idmecanico"],
                    $servicio["precio"]
                ]);
            }
            $pdo->commit();
            error_log("Orden registrada con id: " . $idorden);
            return $idorden;

            } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Error DB: " . $e->getMessage());
            return 0;
      
          } catch (Exception $ex) {
            error_log("Error general: " . $ex->getMessage());
            return 0;
          }
    }
    /**
     * Lista órdenes por periodo: 'dia', 'semana' o 'mes'
     *
     * @param string $modo  'dia' | 'semana' | 'mes'
     * @param string $fecha Fecha en formato 'YYYY-MM-DD'
     * @return array
     */
    public function listarPorPeriodo(string $modo, string $fecha): array
    {
        try {
            $stmt = $this->pdo->prepare("CALL spListOrdenesPorPeriodo(:modo, :fecha)");
            $stmt->execute([
                ':modo'  => $modo,
                ':fecha' => $fecha,
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            error_log("OrdenServicio::listarPorPeriodo error: " . $e->getMessage());
            return [];
        }
    }
}
