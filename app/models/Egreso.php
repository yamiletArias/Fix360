<?php
// app/models/Egreso.php
require_once "../models/Conexion.php";

class Egreso extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    /**
     * Lista egresos por periodo: 'dia', 'semana' o 'mes'
     *
     * @param string $modo  'dia' | 'semana' | 'mes'
     * @param string $fecha Fecha en formato 'YYYY-MM-DD'
     * @return array
     */
    public function listarPorPeriodo(string $modo, string $fecha): array
    {
        try {
            $stmt = $this->pdo->prepare("CALL spListEgresosPorPeriodo(:modo, :fecha)");
            $stmt->execute([
                ':modo'  => $modo,
                ':fecha' => $fecha,
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            error_log("Egreso::listarPorPeriodo error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Registra un nuevo egreso
     *
     * @param array $params {
     *   @var int    idadmin        ID del colaborador que registra
     *   @var int    idcolaborador  ID del colaborador que recibe el dinero
     *   @var int    idformapago    Forma de pago
     *   @var string concepto       Descripción breve
     *   @var float  monto          Monto > 0
     *   @var string numcomprobante Número de comprobante (opcional)
     *   @var string justificacion  Detalle o motivo
     * }
     * @return int ID del egreso (0 si falla)
     */
    public function registerEgreso(array $params): int
    {
        try {
            $pdo = $this->pdo;
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("CALL spRegisterEgreso(?,?,?,?,?,?)");
            $stmt->execute([
                $params['idadmin'],
                $params['idcolaborador'],
                $params['idformapago'],     // forma de pago
                $params['concepto'],        // descripción breve
                $params['monto'],           // monto > 0
                $params['numcomprobante'] ?? '',
            ]);

            // Recuperar el ID generado
            $result = [];
            do {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && isset($row['idegreso'])) {
                    $result = $row;
                    break;
                }
            } while ($stmt->nextRowset());
            $stmt->closeCursor();

            $idegreso = $result['idegreso'] ?? 0;
            if (!$idegreso) {
                throw new Exception("No se obtuvo ID de egreso");
            }

            $pdo->commit();
            return $idegreso;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Egreso::registerEgreso error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * "Elimina" un egreso (marca estado = 'D')
     *
     * @param int    $idegreso
     * @param string $justificacion  Motivo de la desactivación
     * @return int Affected rows (0 si falla)
     */
    public function deleteEgreso(int $idegreso, string $justificacion): int
    {
        try {
            $pdo = $this->pdo;
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("CALL spDeleteEgreso(?, ?)");
            $stmt->execute([
                $idegreso,
                $justificacion
            ]);
            $affected = $stmt->rowCount();
            $stmt->closeCursor();

            $pdo->commit();
            return $affected;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Egreso::deleteEgreso error: " . $e->getMessage());
            return 0;
        }
    }
}
