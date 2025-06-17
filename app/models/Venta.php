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
            WHEN c.idempresa IS NOT NULL THEN e.nomcomercial
            WHEN c.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
          END,
          'Sin propietario'
        ) AS propietario
      FROM ventas v
      LEFT JOIN clientes c ON v.idpropietario = c.idcliente
      LEFT JOIN empresas e ON c.idempresa    = e.idempresa
      LEFT JOIN personas p ON c.idpersona    = p.idpersona
      WHERE v.idventa = :idventa
      LIMIT 1
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idventa' => $idventa]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
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
        try {
            $sql = "CALL buscar_producto(:termino, 'venta')";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al buscar productos (venta): " . $e->getMessage());
        }
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

    //Registrar ventas con detalle de venta (SIN USO)
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
            $sql = "SELECT idventa, cliente, tipocom, numcom FROM vs_ventas_eliminadas";
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
        // 1) VALIDACIÓN PREVIA
        $conOrden = !empty($params['servicios']);
        $tipocom = $params['tipocom'] ?? '';
        $esOrdenTrabajo = $conOrden || (strcasecmp($tipocom, 'orden de trabajo') === 0);

        if ($esOrdenTrabajo) {
            if (empty($params['idvehiculo'])) {
                throw new Exception("Orden de Trabajo: debe especificarse un vehículo.");
            }
            $kilometraje = isset($params['kilometraje']) ? floatval($params['kilometraje']) : 0;
            if ($kilometraje <= 0) {
                throw new Exception("Orden de Trabajo: el kilometraje debe ser mayor que cero.");
            }
        }

        try {
            $pdo = $this->pdo;
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();

            // 2) Llamada al SP unificado con los 15 parámetros en orden
            $sql = "CALL spRegisterVentaConOrden(
            ?,  -- _conOrden
            ?,  -- _idadmin
            ?,  -- _idpropietario
            ?,  -- _idcliente
            ?,  -- _idvehiculo
            ?,  -- _kilometraje
            ?,  -- _observaciones
            ?,  -- _ingresogrua
            ?,  -- _fechaingreso
            ?,  -- _tipocom
            ?,  -- _fechahora
            ?,  -- _numserie
            ?,  -- _numcom
            ?,  -- _moneda
            ?   -- _idcolaborador
        )";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $conOrden,                           // 1) true|false según servicios
                $params['idcolaborador'],            // 2) idadmin
                $params['idpropietario'],            // 3) idpropietario
                $params['idcliente'],                // 4) idcliente
                $params['idvehiculo'],               // 5) idvehiculo
                $params['kilometraje'],              // 6) kilometraje
                $params['observaciones'],            // 7) observaciones
                $params['ingresogrua'],              // 8) ingresogrua
                $params['fechaingreso'] ?? null,     // 9) fechaingreso
                $params['tipocom'],                  // 10) tipocom
                $params['fechahora'],                // 11) fechahora
                $params['numserie'],                 // 12) numserie
                $params['numcom'],                   // 13) numcom
                $params['moneda'],                   // 14) moneda
                $params['idcolaborador'],            // 15) idcolaborador
            ]);

            // 3) Recuperar idventa / idorden
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
                throw new Exception("No se obtuvo idventa tras invocar spRegisterVentaConOrden");
            }
            $idventa = (int) $result['idventa'];
            $idorden = isset($result['idorden']) ? (int) $result['idorden'] : null;

            // 4) Detalle de productos
            if (!empty($params['productos'])) {
                $stmtProd = $pdo->prepare("CALL spuInsertDetalleVenta(?,?,?,?,?,?,?)");
                foreach ($params['productos'] as $prod) {
                    $stmtProd->execute([
                        $idventa,
                        $prod['idproducto'],
                        $prod['cantidad'],
                        $prod['numserie'] ?? null,
                        $prod['precio'],
                        $prod['descuento'],
                        true
                    ]);
                }
            }

            // 5) Detalle de servicios (solo si hay orden y servicios)
            if ($conOrden && !empty($params['servicios'])) {
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

    /**
     * Summary of combinarOtYCrearVenta
     * @param array $idsOT
     * @param string $tipocom
     * @param string $numserie
     * @param string $numcom
     * @throws \Exception
     * @return int
     */
    public function combinarOtYCrearVenta(array $idsOT, string $tipocom, string $numserie, string $numcom): int
    {
        // 1) Validar que haya al menos dos OT y que todas pertenezcan al mismo propietario
        if (count($idsOT) < 2) {
            throw new Exception("Debes combinar al menos dos OT.");
        }

        // Preparo placeholders para las consultas IN (...)
        $placeholders = implode(',', array_fill(0, count($idsOT), '?'));

        // 1.1) Obtener directamente todos los idpropietario para esas OT y luego filtrar en PHP
        $sqlProp = "
            SELECT idpropietario
            FROM ventas
            WHERE idventa IN ($placeholders)
            AND tipocom = 'orden de trabajo'
            AND estado = 1
        ";
        $stmtProp = $this->pdo->prepare($sqlProp);
        $stmtProp->execute($idsOT);
        $propRows = $stmtProp->fetchAll(PDO::FETCH_COLUMN, 0); // Array de idpropietario
        $sqlCli = "
        SELECT DISTINCT idcliente
        FROM ventas
        WHERE idventa IN ($placeholders)
          AND tipocom = 'orden de trabajo'
          AND estado = TRUE
    ";
        $stmtCli = $this->pdo->prepare($sqlCli);
        $stmtCli->execute($idsOT);
        $clientes = $stmtCli->fetchAll(PDO::FETCH_COLUMN, 0);

        if (empty($clientes)) {
            // Ninguna OT tenía cliente → dejamos NULL
            $idClienteCombinada = null;
        } else {
            // Aseguramos que sean todos iguales:
            $unicos = array_unique($clientes);
            if (count($unicos) > 1) {
                throw new Exception("Las OT seleccionadas tienen diferentes clientes.");
            }
            $idClienteCombinada = (int) $unicos[0];
        }

        // Elimina duplicados:
        $propUnicos = array_unique($propRows);
        if (count($propUnicos) !== 1) {
            throw new Exception("Todas las OT deben pertenecer al mismo propietario.");
        }
        // Ahora sí sabemos que es un único propietario:
        $idprop = (int) array_values($propUnicos)[0];

        // 1.2) Obtener idvehiculo y kilometraje MÁXIMO entre esas OT
        //     (para heredar el vehículo y el último kilometraje)
        $sqlKm = "
            SELECT idvehiculo, kilometraje
            FROM ventas
            WHERE idventa IN ($placeholders)
            AND tipocom = 'orden de trabajo'
            AND estado = TRUE
            ORDER BY kilometraje DESC
            LIMIT 1
        ";
        $stmtKm = $this->pdo->prepare($sqlKm);
        $stmtKm->execute($idsOT);
        $kmRow = $stmtKm->fetch(PDO::FETCH_ASSOC);
        // Si hay al menos una fila, heredamos esos valores; de lo contrario, 0/0
        $idVehiculoMasAlto = isset($kmRow['idvehiculo']) ? (int) $kmRow['idvehiculo'] : 0;
        $kilometrajeMasAlto = isset($kmRow['kilometraje']) ? (float) $kmRow['kilometraje'] : 0.0;

        // 2) Recoger amortizaciones previas de esas OT (si las hay)
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

        // 4) Iniciar transacción
        $this->pdo->beginTransaction();
        try {
            // 4.1) Crear la nueva venta COMBINADA, pero CUIDADO: ponemos conOrden = TRUE
            //      para que el SP cree primero un registro en ordenservicios y luego la venta.
            $sp = $this->pdo->prepare(
                "CALL spRegisterVentaConOrden(
                            TRUE,             -- 1) conOrden = TRUE para crear la orden
                            :idadmin,         -- 2) idadmin (debe ser un colaborador válido)
                            :idpropietario,   -- 3) idpropietario
                            :idcliente,       -- 4) idcliente (en tu caso puede ser 0 o NULL si no hay cliente)
                            :idvehiculo,      -- 5) idvehiculo heredado
                            :kilometraje,     -- 6) kilometraje heredado
                            :observaciones,   -- 7) observaciones (puede ser cadena vacía si no hay)
                            :ingresogrua,     -- 8) ingresogrua (1/0 o TRUE/FALSE)
                            :fechaingreso,    -- 9) fechaingreso (DATETIME o NULL)
                            :tipocom,         -- 10) “orden de trabajo”
                            :fechahora,       -- 11) fecha/hora actual
                            :numserie,        -- 12) número de serie que pasaste
                            :numcom,          -- 13) número de comprobante que pasaste
                            :moneda,          -- 14) “SOLES” (o la moneda que uses)
                            :idcolaborador    -- 15) idcolaborador (de la sesión)
                        )"
            );

            $idcolab = $_SESSION['login']['idcolaborador'] ?? null;
            if (!$idcolab) {
                throw new Exception("No hay colaborador logueado para asignar a la orden.");
            }

            // Podemos dejar observaciones en blanco (o usar $algunasObservaciones si las tuvieras)
            $observaciones = '';

            // Si no ingresa por grúa:
            $ingresoGrua = FALSE;

            // Para fechaingreso podemos pasar NULL y dejar que el SP use NULL
            $fechaIngreso = NULL;

            // El resto de variables viene de tu lógica previa ($idVehiculoMasAlto, $kilometrajeMasAlto, etc.)

            $sp->execute([
                ':idadmin' => $idcolab,             // 2)
                ':idpropietario' => $idprop,              // 3)
                ':idcliente' => $idClienteCombinada,  // 4)
                ':idvehiculo' => $idVehiculoMasAlto,   // 5)
                ':kilometraje' => $kilometrajeMasAlto,  // 6)
                ':observaciones' => $observaciones,       // 7)
                ':ingresogrua' => $ingresoGrua,         // 8)
                ':fechaingreso' => $fechaIngreso,        // 9)
                ':tipocom' => $tipocom,             // 10)
                ':fechahora' => date('Y-m-d H:i:s'),  // 11)
                ':numserie' => $numserie,            // 12)
                ':numcom' => $numcom,              // 13)
                ':moneda' => 'SOLES',              // 14)
                ':idcolaborador' => $idcolab              // 15)
            ]);

            // 4.2) Capturar idventa y idorden que devuelve el SP
            $newVentaId = 0;
            $newOrdenId = 0;
            do {
                $fila = $sp->fetch(PDO::FETCH_ASSOC);
                if (!empty($fila['idventa'])) {
                    $newVentaId = (int) $fila['idventa'];
                    $newOrdenId = isset($fila['idorden']) ? (int) $fila['idorden'] : 0;
                    break;
                }
            } while ($sp->nextRowset());
            $sp->closeCursor();

            if (!$newVentaId || !$newOrdenId) {
                throw new Exception("No se obtuvo idventa o idorden de la venta combinada.");
            }

            // 4.3) Insertar productos fusionados en detalleventa usando el nuevo idventa
            $stmtP = $this->pdo->prepare("CALL spuInsertDetalleVenta(?,?,?,?,?,?,?)");
            foreach ($det['productos'] as $p) {
                $stmtP->execute([
                    $newVentaId,        // idventa = la nueva venta combinada
                    $p['idproducto'],   // id del producto
                    $p['cantidad'],     // cantidad total
                    $numserie,          // numserie de referencia
                    $p['precio'],       // precio unitario
                    $p['descuento'],     // descuento unitario
                    false
                ]);
            }

            // 4.4) Insertar servicios en detalleordenservicios usando el nuevo idorden
            $stmtS = $this->pdo->prepare("CALL spInsertDetalleOrdenServicio(?,?,?,?)");
            foreach ($det['servicios'] as $s) {
                $stmtS->execute([
                    $newOrdenId,        // <<– AQUÍ: idorden real que sí existe en ordenservicios
                    $s['idservicio'],   // id del servicio
                    $s['idmecanico'],   // id del mecánico
                    $s['precio']        // precio del servicio
                ]);
            }

            // 4.5') ——> Re-asignar amortizaciones de las OT originales a la nueva venta
            if (!empty($amortPrevias)) {
                // Preparamos placeholders para el IN (...)
                $inOt = implode(',', array_fill(0, count($idsOT), '?'));
                $sqlUpd = "
                    UPDATE amortizaciones
                    SET idventa = ?
                    WHERE idventa IN ($inOt)
                    ";
                $stmtUpd = $this->pdo->prepare($sqlUpd);
                $paramsUpd = array_merge([$newVentaId], $idsOT);
                $stmtUpd->execute($paramsUpd);
            }


            // 4.6) Marcar como “C” (cerradas) las OT originales en ordenservicios
            $sqlOrd2 = "
                SELECT idexpediente_ot AS idorden
                FROM ventas
                WHERE idventa IN ($placeholders)
                AND tipocom = 'orden de trabajo'
                AND idexpediente_ot IS NOT NULL
            ";
            $stmtOrd2 = $this->pdo->prepare($sqlOrd2);
            $stmtOrd2->execute($idsOT);
            $ordenes = array_column($stmtOrd2->fetchAll(PDO::FETCH_ASSOC), 'idorden');
            if (!empty($ordenes)) {
                $inOrdenes = implode(',', array_fill(0, count($ordenes), '?'));
                $upd = $this->pdo->prepare("
                    UPDATE ordenservicios
                    SET estado = 'C'
                    WHERE idorden IN ($inOrdenes)
                ");
                $upd->execute($ordenes);
            }

            // 4.7) Desactivar las ventas antiguas (OT) en la tabla ventas
            $updV = $this->pdo->prepare("
                UPDATE ventas
                SET estado = FALSE
                WHERE idventa IN ($placeholders)
            ");
            $updV->execute($idsOT);

            // 5) Confirmar toda la transacción
            $this->pdo->commit();
            return $newVentaId;

        } catch (Exception $e) {
            // Si algo falla, hacemos rollback y relanzamos la excepción
            $this->pdo->rollBack();
            throw $e;
        }
    }


    /**
     * Devuelve dos arrays:
     *   - 'productos': cada producto único con la suma de cantidades de todas las OT
     *   - 'servicios' : lista de servicios a partir de detalleordenservicios
     *
     * @param array $idsOT  Lista de idventa (OT) a combinar
     * @return array {productos: array, servicios: array}
     */
    private function getDetallesOt(array $idsOT): array
    {
        $pdo = $this->pdo;
        // 1) Placeholder para IN (…) con los idventa de OT
        $inVentas = implode(',', array_fill(0, count($idsOT), '?'));

        // ─── PASO A: OBTENER LOS idorden (PK en ordenservicios) DE CADA idventa de OT ───
        $sqlOrd = "
            SELECT idexpediente_ot AS idorden
            FROM ventas
            WHERE idventa IN ($inVentas)
            AND tipocom = 'orden de trabajo'
            AND idexpediente_ot IS NOT NULL
        ";
        $stmtOrd = $pdo->prepare($sqlOrd);
        $stmtOrd->execute($idsOT);
        $ordenes = array_column($stmtOrd->fetchAll(PDO::FETCH_ASSOC), 'idorden');

        // ─── PASO B: TOMAR TODOS LOS SERVICIOS DE ESAS OT (usando idorden) ───
        if (empty($ordenes)) {
            $servicios = [];
        } else {
            $inOrdenes = implode(',', array_fill(0, count($ordenes), '?'));
            $sqlS = "
                SELECT 
                    dos.idorden,
                    dos.idservicio,
                    dos.idmecanico,
                    dos.precio
                FROM detalleordenservicios AS dos
                WHERE dos.idorden IN ($inOrdenes)
                ";
            $stmtS = $pdo->prepare($sqlS);
            $stmtS->execute($ordenes);
            $servicios = $stmtS->fetchAll(PDO::FETCH_ASSOC);
        }

        // ─── PASO C: TOMAR TODOS LOS PRODUCTOS ASOCIADOS A CADA idventa ───
        $sqlP = "
            SELECT 
                dv.idproducto,
                dv.cantidad,
                dv.precioventa AS precio,
                dv.descuento
            FROM detalleventa AS dv
            WHERE dv.idventa IN ($inVentas)
            ";
        $stmtP = $pdo->prepare($sqlP);
        $stmtP->execute($idsOT);
        $productosRaw = $stmtP->fetchAll(PDO::FETCH_ASSOC);

        // ─── PASO D: AGREGAR (FUSIONAR) POR idproducto ───
        $productosAgregados = [];
        foreach ($productosRaw as $fila) {
            $pid = (int) $fila['idproducto'];
            $cantidad = (float) $fila['cantidad'];
            $precio = (float) $fila['precio'];
            $descuento = (float) $fila['descuento'];

            if (!isset($productosAgregados[$pid])) {
                // Primera vez que aparece este idproducto
                $productosAgregados[$pid] = [
                    'idproducto' => $pid,
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'descuento' => $descuento
                ];
            } else {
                // Ya existía: sumamos la cantidad
                $productosAgregados[$pid]['cantidad'] += $cantidad;
            }
        }
        $productos = array_values($productosAgregados);

        return [
            'productos' => $productos,
            'servicios' => $servicios
        ];
    }

}