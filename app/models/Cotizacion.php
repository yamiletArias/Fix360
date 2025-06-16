<?php
require_once "../models/Conexion.php";

class Cotizacion extends Conexion
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

  //LLEVAR DATOS
  public function getCabeceraById(int $id)
  {
    $sql = "
    SELECT 
      c.idcotizacion,
      c.idcliente,
      COALESCE(CONCAT(p.nombres,' ',p.apellidos), e.nomcomercial) AS cliente,
      c.fechahora,
      c.vigenciadias,
      c.estado
    FROM cotizaciones c
    JOIN clientes cli ON c.idcliente = cli.idcliente
    LEFT JOIN personas p ON cli.idpersona = p.idpersona
    LEFT JOIN empresas e ON cli.idempresa = e.idempresa
    WHERE c.idcotizacion = ?
    LIMIT 1
  ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * Devuelve el detalle completo (productos, precio, cantidad, descuento) de una cotización.
   */
public function getDetalleById(int $idcotizacion): array
{
  $sql = "
    SELECT 
      dc.iddetallecotizacion,
      dc.cantidad,
      dc.precio,
      dc.descuento,
      CASE
        WHEN dc.idproducto IS NOT NULL THEN p.descripcion
        ELSE s.servicio
      END AS item,
      CASE
        WHEN dc.idproducto IS NOT NULL THEN 'producto'
        ELSE 'servicio'
      END AS tipo
    FROM detallecotizacion dc
    LEFT JOIN productos p   ON p.idproducto  = dc.idproducto
    LEFT JOIN servicios s   ON s.idservicio  = dc.idservicio
    WHERE dc.idcotizacion = ?
    ORDER BY dc.iddetallecotizacion
  ";
  $stmt = $this->pdo->prepare($sql);
  $stmt->execute([$idcotizacion]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


  public function getAll(): array
  {
    $result = [];
    try {
      $sql = "SELECT * FROM vs_cotizaciones ORDER BY idcotizacion DESC";
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception("Error al obtener las Cotizaciones: " . $e->getMessage());
    }
    return $result;
  }

  // Anular cotización (soft delete)
  public function deleteCotizacion(int $idcotizacion, string $justificacion): bool
  {
    try {
      $sql = "CALL spuDeleteCotizacion(:idcotizacion, :justificacion)";
      $stmt = $this->pdo->prepare($sql);
      return $stmt->execute([
        ':idcotizacion' => $idcotizacion,
        ':justificacion' => $justificacion
      ]);
    } catch (PDOException $e) {
      error_log("Error al anular cotización #{$idcotizacion}: " . $e->getMessage());
      return false;
    }
  }

  // Listar cotizaciones anuladas
  public function getCotizacionesEliminadas(): array
  {
    try {
      $sql = "SELECT 
          idcotizacion, 
          cliente, 
          total,
          vigencia
        FROM vs_cotizaciones_eliminadas";

      $stmt = $this->pdo->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      // En caso de error, devolvemos array vacío o lanzamos excepción controlada
      error_log("Error al obtener cotizaciones eliminadas: " . $e->getMessage());
      return [];
    }
  }

  // Obtener justificación de anulación de cotización
  public function getJustificacion(int $idcotizacion): ?string
  {
    $sql = "SELECT justificacion FROM vista_justificacion_cotizacion WHERE idcotizacion = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$idcotizacion]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['justificacion'] ?? null;
  }

  //buscar cliente
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
      throw new Exception("Error al buscar cliente: " . $e->getMessage());
    }
    return $result;
  }

  //buscar productos
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

  public function registerCotizacion($params = []): int
  {
    try {
      $pdo = $this->pdo;
      $pdo->beginTransaction();

      error_log("Parametros para spuRegisterCotizacion: " . print_r($params, true));

      $stmt = $pdo->prepare("CALL spuRegisterCotizaciones(?,?,?,?,?)");
      $stmt->execute([
        $params["fechahora"],
        $params["vigenciadias"],
        $params["moneda"],
        $params['idcolaborador'],
        $params["idcliente"]
      ]);


      $result = [];

      do {
        $tmp = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Resultado fetch" . print_r($tmp, true));
        if ($tmp && isset($tmp['idcotizacion'])) {
          $result = $tmp;
          break;
        }
      } while ($stmt->nextRowset());

      $stmt->closeCursor();
      $idcotizacion = $result['idcotizacion'] ?? 0;

      if (!$idcotizacion) {
        error_log("SP ejecutado paro devuelve el ID DE COTIZACION");
        throw new Exception("Nose pudo obtener el id de la cotizacion");
      }

      $stmtDetalle = $pdo->prepare("CALL spuInsertDetalleCotizacion(?,?,?,?,?,?)");
      foreach ($params["items"] as $item) {
    $stmtDetalle->execute([
      $idcotizacion,
      $item["idproducto"] ?? null,
      $item["idservicio"] ?? null,
      $item["cantidad"],
      $item["precio"],
      $item["descuento"]
    ]);
}

      $pdo->commit();
      error_log("Venta registrada con id: " . $idcotizacion);
      return $idcotizacion;
    } catch (Exception $e) {
      $pdo->rollBack();
      error_log("Error DB: " . $e->getMessage());
      return 0;
    } catch (PDOException $e) {
      error_log("Error" . $e->getMessage());
      return 0;
    }
  }

  /**
   * Lista Cotizacion por periodo: dia, semana, mes
   * 
   * @param string $modo dia - semana - mes
   * @param string $fecha Fecha en formato YYYY-MM-DD
   * @return array
   */
  public function listarPorPeriodoCotizacion(string $modo, string $fecha): array
  {
    try {
      $stmt = $this->pdo->prepare("CALL spListCotizacionesPorPeriodo(:modo, :fecha)");
      $stmt->execute([
        ':modo' => $modo,
        ':fecha' => $fecha,
      ]);
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      return $result;
    } catch (Exception $e) {
      error_log("Cotizacion::listarPorPeriodoCotizacion error: " . $e->getMessage());
      return [];
    }
  }
}
?>