<?php

require_once __DIR__ . '/Conexion.php';

class Agenda extends Conexion{
    private $pdo;

    public function __CONSTRUCT(){
        $this->pdo = parent::getConexion();
    }

    public function getRecordatoriosHoy(): array{
        $result = [];

        try {
            $sql = "SELECT * FROM vwRecordatoriosHoy";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }

    public function RegisterRecordatorio(array $params): int{
        $numRows = 0;
        try {
            $query = "CALL spRegisterRecordatorio(?,?,?)";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                $params['idpropietario'],
                $params['fchproxvisita'],
                $params['comentario']
            ]);
            $numRows = $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error DB (RegisterRecordatorio): " . $e->getMessage());
        }

        return $numRows;
    }

    public function ListAgendasPorPeriodo(array $params): array{
        $result = [];
        try {
            $sql = "CALL spListAgendasPorPeriodo(?,?,?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $params['modo'],
                $params['fecha'],
                $params['estado']
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
        } catch (Exception $e) {
            error_log("Error en ListAgendasPorPeriodo: " . $e->getMessage());
        }

        return $result;
    }

    // Actualiza el estado de un recordatorio (P, R, C, H)
    public function updateEstado(int $idagenda, string $estado): int{
        $numRows = 0;
        try {
            $sql = "CALL spUpdateEstado(?,?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idagenda, $estado]);
            $numRows = $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error DB (updateEstado): " . $e->getMessage());
        }

        return $numRows;
    }

    // Reprograma la fecha y marca el recordatorio como 'R'
    public function reprogramarRecordatorio(int $idagenda, string $nuevaFecha): int{
        $numRows = 0;
        try {
            $sql = "CALL spReprogramarRecordatorio(?,?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idagenda, $nuevaFecha]);
            $numRows = $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Error DB (reprogramarRecordatorio): " . $e->getMessage());
        }

        return $numRows;
    }
}
