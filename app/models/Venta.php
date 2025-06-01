<?php
require_once "../models/Conexion.php";
require_once __DIR__ . '/Amortizacion.php';

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

    public function detalleCompleto()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['idventa'])) {
            $idventa = $_GET['idventa'];

            try {
                $conexion = new Conexion();
                $pdo = $conexion->getConexion();

                // Consultar la vista completa
                $sql = "SELECT * FROM vista_detalle_venta_pdf WHERE idventa = :idventa ORDER BY idventa";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':idventa', $idventa, PDO::PARAM_INT);
                $stmt->execute();
                $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Retornar datos en formato JSON
                header('Content-Type: application/json');
                echo json_encode($resultado);
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Error al obtener datos: ' . $e->getMessage()]);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Método no permitido o faltan parámetros']);
        }
    }

    public function getPropietarioById(int $idventa): ?array
    {
        $sql = "
      SELECT
        COALESCE(
          CASE
            WHEN pc.idempresa IS NOT NULL THEN ep.nomcomercial
            WHEN pc.idpersona IS NOT NULL THEN CONCAT(pp.nombres, ' ', pp.apellidos)
          END,
          'Sin propietario'
        ) AS propietario
      FROM ventas v
      LEFT JOIN propietarios prop ON v.idpropietario = prop.idpropietario
      LEFT JOIN clientes pc      ON prop.idcliente    = pc.idcliente
      LEFT JOIN empresas ep      ON pc.idempresa      = ep.idempresa
      LEFT JOIN personas pp      ON pc.idpersona      = pp.idpersona
      WHERE v.idventa = :idventa
      LIMIT 1
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idventa' => $idventa]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    /*     public function getPropietarioById(int $idventa): ?array
        {
            try {
                $sql = "SELECT propietario FROM vs_ventas WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$idventa]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ?: null;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener propietario: " . $e->getMessage());
            }
        }
     */

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

    /**
     * Lista ventas de un vehículo en un rango (mes, semestral, anual).
     *
     * @param string $modo        'mes'|'semestral'|'anual'
     * @param string $fecha       'YYYY-MM-DD'
     * @param int    $idvehiculo
     * @return array
     */
    public function listarHistorialPorVehiculo(string $modo, string $fecha, int $idvehiculo, bool $estado = true): array
    {
        try {
            $stmt = $this->pdo->prepare("CALL spHistorialVentasPorVehiculo(:modo, :fecha, :idvehiculo, :estado)");
            $stmt->execute([
                ':modo' => $modo,
                ':fecha' => $fecha,
                ':idvehiculo' => $idvehiculo,
                ':estado' => $estado ? 1 : 0,
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            error_log("Venta::listarHistorialPorVehiculo error: " . $e->getMessage());
            return [];
        }
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

            $stmtVenta = $pdo->prepare("CALL spuRegisterVenta(?,?,?,?,?,?,?,?,?)");
            $stmtVenta->execute([
                $params["tipocom"],
                $params["fechahora"],
                $params["numserie"],
                $params["numcom"],
                $params["moneda"],
                $params["idcliente"],
                $params['idcolaborador'],
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
     * Lista OT (orden de trabajo) por periodo: dia, semana, mes
     * 
     * @param string $modo  dia | semana | mes
     * @param string $fecha YYYY-MM-DD
     * @return array
     */
    public function listarPorPeriodoOT(string $modo, string $fecha): array
    {
        try {
            $stmt = $this->pdo->prepare("CALL spListOTPorPeriodo(:modo, :fecha)");
            $stmt->execute([
                ':modo' => $modo,
                ':fecha' => $fecha,
            ]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $result;
        } catch (Exception $e) {
            error_log("Ventas::listarPorPeriodoOT error: " . $e->getMessage());
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

    private function ensureKardex(int $idproducto): void
    {
        $stm = $this->pdo->prepare("SELECT idkardex FROM kardex WHERE idproducto = ?");
        $stm->execute([$idproducto]);
        if (!$stm->fetch()) {
            // Inserta un kardex con valores por defecto
            $ins = $this->pdo->prepare(
                "INSERT INTO kardex (idproducto, fecha, stockmin, stockmax) VALUES (?, CURDATE(), 0, NULL)"
            );
            $ins->execute([$idproducto]);
        }
    }

    public function registerVentasConOrden(array $params): array
    {
        try {
            $pdo = $this->pdo;
            $pdo->beginTransaction();

            // 1) Llamo al SP unificado
            $sql = "CALL spRegisterVentaConOrden(
            ?,  -- conOrden (BOOLEAN)
            ?,  -- idadmin
            ?,  -- idpropietario
            ?,  -- idcliente
            ?,  -- idvehiculo
            ?,  -- kilometraje
            ?,  -- observaciones
            ?,  -- ingresogrua
            ?,  -- p_fechaingreso (puede venir NULL)
            ?,  -- tipocom
            ?,  -- fechahora
            ?,  -- numserie
            ?,  -- numcom
            ?,  -- moneda
            ?   -- idcolaborador
        )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $params['conOrden'],
                $params['idcolaborador'],        // _idadmin
                $params['idpropietario'],       // si no creas orden, el mismo cliente
                $params['idcliente'],
                $params['idvehiculo'],
                $params['kilometraje'],
                $params['observaciones'],
                $params['ingresogrua'],
                $params['fechaingreso'] ?? null,
                $params['tipocom'],
                $params['fechahora'],
                $params['numserie'],
                $params['numcom'],
                $params['moneda'],
                $params['idcolaborador']
            ]);

            // 2) Capturo el primer conjunto de resultados (idventa, idorden)
            $result = [];
            do {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && isset($row['idventa'])) {
                    $result = $row;
                    break;
                }
            } while ($stmt->nextRowset());
            $stmt->closeCursor();

            if (empty($result['idventa'])) {
                throw new Exception("No se obtuvo idventa");
            }

            $idventa = (int) $result['idventa'];
            $idorden = isset($result['idorden']) ? (int) $result['idorden'] : null;

            // 3) Detalle de productos
            $stmtProd = $pdo->prepare("CALL spuInsertDetalleVenta(?,?,?,?,?,?)");
            foreach ($params['productos'] as $prod) {
                $stmtProd->execute([
                    $idventa,
                    $prod['idproducto'],
                    $prod['cantidad'],
                    $prod['numserie'] ?? null,
                    $prod['precio'],
                    $prod['descuento']
                ]);
            }

            // 4) Detalle de servicios (si conOrden = true y tienes un array 'servicios')
            if ($params['conOrden'] && !empty($params['servicios'])) {
                $stmtServ = $pdo->prepare("CALL spInsertDetalleOrdenServicio(?,?,?,?)");
                foreach ($params['servicios'] as $srv) {
                    $stmtServ->execute([
                        $idorden,
                        $srv['idservicio'],
                        $srv['idmecanico'],
                        $srv['precio']
                    ]);
                }
            }

            $pdo->commit();
            return ['idventa' => $idventa, 'idorden' => $idorden];
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

/*     public function getDetalleCompleto(int $idventa): array
    {
        $sql = "SELECT * FROM vista_detalle_venta WHERE idventa = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idventa]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
 */
public function combinarOtYCrearVenta(array $idsOT, string $tipocom, string $numserie, string $numcom): int
{
    // 1) Validar que haya al menos dos OT y que todas pertenezcan al mismo propietario
    if (count($idsOT) < 2) {
        throw new Exception("Debes combinar al menos dos OT.");
    }

    // Construimos placeholders para el IN (…) de la consulta de propietario
    $placeholders = implode(',', array_fill(0, count($idsOT), '?'));

    // 1.1) Consultamos cuántos propietarios distintos hay entre las ventas que son OT
    $sql = "
        SELECT 
            COUNT(DISTINCT idpropietario) AS cnt,
            MIN(idpropietario)            AS idprop
        FROM ventas
        WHERE idventa IN ($placeholders)
          AND tipocom = 'orden de trabajo'
          AND estado = TRUE
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($idsOT);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ((int) $row['cnt'] !== 1) {
        throw new Exception("Todas las OT deben pertenecer al mismo propietario.");
    }
    $idprop = (int) $row['idprop']; // identificador único del propietario

    // 2) Recoger amortizaciones previas de esas OT
    $sqlAm = "
        SELECT numtransaccion, amortizacion, idformapago
        FROM amortizaciones
        WHERE idventa IN ($placeholders)
    ";
    $stmtAm = $this->pdo->prepare($sqlAm);
    $stmtAm->execute($idsOT);
    $amortPrevias = $stmtAm->fetchAll(PDO::FETCH_ASSOC);

    // 3) Obtener los detalles de productos y servicios de cada OT
    $det = $this->getDetallesOt($idsOT);
    // $det['productos']  → arreglo de productos con keys: idproducto, cantidad, precio, descuento
    // $det['servicios']  → arreglo de servicios con keys: idorden, idservicio, idmecanico, precio

    // 4) Iniciar transacción para asegurar atomicidad
    $this->pdo->beginTransaction();
    try {
        // 4.1) Crear la nueva venta (combinada) invocando el SP spRegisterVentaConOrden
        //      Pasamos: FALSE porque no queremos crear otra OT aquí, 0 en los valores que no se usan,
        //      sí pasamos el tipocom, numserie y numcom recibidos.
        $sp = $this->pdo->prepare("
            CALL spRegisterVentaConOrden(
                FALSE, 0, :idpropietario, 0, 0, 0, '', FALSE, NULL,
                :tipocom, NOW(), :numserie, :numcom, 'SOLES', :idcolaborador
            )
        ");
        // Generamos el id del colaborador actual (mecánico/usuario) desde la sesión
        $idcolab = $_SESSION['login']['idcolaborador'] ?? 0;

        $sp->execute([
            ':idpropietario' => $idprop,
            ':tipocom'       => $tipocom,
            ':numserie'      => $numserie,
            ':numcom'        => $numcom,
            ':idcolaborador' => $idcolab
        ]);

        // 4.2) Capturar el nuevo idventa devuelto por el SP
        $newId = 0;
        do {
            $res = $sp->fetch(PDO::FETCH_ASSOC);
            if (!empty($res['idventa'])) {
                $newId = (int) $res['idventa'];
                break;
            }
        } while ($sp->nextRowset());
        $sp->closeCursor();

        if (!$newId) {
            throw new Exception("No se obtuvo el id de la nueva venta.");
        }

        // 4.3) Insertar productos en detalleventa usando el SP spuInsertDetalleVenta
        $stmtP = $this->pdo->prepare("CALL spuInsertDetalleVenta(?,?,?,?,?,?)");
        foreach ($det['productos'] as $p) {
            $stmtP->execute([
                $newId,           // idventa (la venta combinada)
                $p['idproducto'], // id del producto
                $p['cantidad'],   // cantidad
                $numserie,        // numserie: mismo de la venta
                $p['precio'],     // precio unitario
                $p['descuento']   // descuento unitario
            ]);
        }

        // 4.4) Insertar servicios en detalleordenservicios usando el SP spInsertDetalleOrdenServicio
        $stmtS = $this->pdo->prepare("CALL spInsertDetalleOrdenServicio(?,?,?,?)");
        foreach ($det['servicios'] as $s) {
            $stmtS->execute([
                $newId,            // idventa (la venta combinada se comporta como OT aquí)
                $s['idservicio'],  // id del servicio
                $s['idmecanico'],  // id del mecánico
                $s['precio']       // precio del servicio
            ]);
        }

        // 4.5) Transferir amortizaciones previas a la nueva venta (usando el modelo Amortizacion)
        $amModel = new Amortizacion();
        foreach ($amortPrevias as $prev) {
            $amModel->create(
                'venta',
                $newId,
                (int)   $prev['idformapago'],
                (float) $prev['amortizacion'],
                $prev['numtransaccion']
            );
        }

        // 4.6) Marcar como cerradas (C) las OT originales en ordenservicios
        $upd = $this->pdo->prepare("
            UPDATE ordenservicios
            SET estado = 'C'
            WHERE idorden IN ($placeholders)
        ");
        $upd->execute($idsOT);

        // 4.7) Desactivar (estado = FALSE) las ventas antiguas que correspondían a esas OT
        $updV = $this->pdo->prepare("
            UPDATE ventas
            SET estado = FALSE
            WHERE idventa IN ($placeholders)
        ");
        $updV->execute($idsOT);

        // 5) Commit de toda la transacción
        $this->pdo->commit();

        // Devolver el id de la venta combinada que acaba de crearse
        return $newId;

    } catch (Exception $e) {
        // En caso de error, revertir todos los cambios
        $this->pdo->rollBack();
        throw $e;
    }
}

    private function getDetallesOt(array $idsOT): array
    {
        $in = implode(',', array_fill(0, count($idsOT), '?'));

        // 1) Servicios
        $sqlS = "
      SELECT 
        dos.idorden,
        dos.idservicio,
        dos.idmecanico,
        dos.precio
      FROM detalleordenservicios AS dos
      WHERE dos.idorden IN ($in)
    ";
        $stmtS = $this->pdo->prepare($sqlS);
        $stmtS->execute($idsOT);
        $servicios = $stmtS->fetchAll(PDO::FETCH_ASSOC);

        // 2) Productos
        $sqlP = "
      SELECT 
        dv.idproducto,
        dv.cantidad,
        dv.precioventa AS precio,
        dv.descuento
      FROM detalleventa AS dv
      WHERE dv.iddetorden IN (
        SELECT iddetorden FROM detalleordenservicios WHERE idorden IN ($in)
      )
    ";
        $stmtP = $this->pdo->prepare($sqlP);
        $stmtP->execute($idsOT);
        $productos = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        return ['servicios' => $servicios, 'productos' => $productos];
    }

    /*     private function getDetallesOt(array $idsOT): array
        {
            $in = implode(',', array_fill(0, count($idsOT), '?'));

            // 1) Servicios
            $sqlS = "
          SELECT 
            dos.idorden,
            dos.idservicio,
            dos.idmecanico,
            dos.precio
          FROM detalleordenservicios AS dos
          WHERE dos.idorden IN ($in)
        ";
            $stmtS = $this->pdo->prepare($sqlS);
            $stmtS->execute($idsOT);
            $servicios = $stmtS->fetchAll(PDO::FETCH_ASSOC);

            // 2) Productos (unimos con detalleventa)
            $sqlP = "
          SELECT 
            dos.idorden,
            dv.idproducto,
            dv.cantidad,
            dv.precioventa AS precio,
            dv.descuento
          FROM detalleordenservicios AS dos
          JOIN detalleventa AS dv 
            ON dv.iddetorden = dos.iddetorden
          WHERE dos.idorden IN ($in)
        ";
            $stmtP = $this->pdo->prepare($sqlP);
            $stmtP->execute($idsOT);
            $productos = $stmtP->fetchAll(PDO::FETCH_ASSOC);

            return ['servicios' => $servicios, 'productos' => $productos];
        }
     */
}
