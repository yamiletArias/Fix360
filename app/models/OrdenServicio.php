<?php
require_once "../models/Conexion.php";

class OrdenServicio extends Conexion
{
    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    /**
     * Registra la orden (cabecera + detalle)
     * @param array $p {
     *   @var int    idadmin
     *   @var int    idmecanico
     *   @var int    idpropietario
     *   @var int    idcliente
     *   @var int    idvehiculo
     *   @var float  kilometraje
     *   @var string observaciones
     *   @var bool   ingresogrua
     *   @var string fechaingreso
     *   @var string fecharecordatorio
     *   @var array  detalle [ ['idservicio'=>…, 'precio'=>…], … ]
     * }
     * @return int ID de la orden (0 si falla)
     */
    public function registerOrden(array $p): int
    {
        try {
            $this->pdo->beginTransaction();

            // 1) insert cabecera
            $stmt = $this->pdo->prepare("CALL spuRegisterOrdenServicio(?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $p['idadmin'],
                $p['idmecanico'],
                $p['idpropietario'],
                $p['idcliente'],
                $p['idvehiculo'],
                $p['kilometraje'],
                $p['observaciones'],
                $p['ingresogrua'],
                $p['fechaingreso'],
                $p['fecharecordatorio']
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $idorden = $row['idorden'] ?? 0;
            $stmt->closeCursor();

            if (!$idorden) {
                throw new Exception("No se obtuvo ID");
            }

            // 2) insert detalle
            $stmtDet = $this->pdo->prepare("CALL spuInsertDetalleOrden(?,?,?)");
            foreach ($p['detalle'] as $item) {
                $stmtDet->execute([
                    $idorden,
                    $item['idservicio'],
                    $item['precio']
                ]);
            }

            $this->pdo->commit();
            return $idorden;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("OrdenServicio::registerOrden error: " . $e->getMessage());
            return 0;
        }
    }
}
