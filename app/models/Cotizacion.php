<?php
require_once "../models/Conexion.php";

class Cotizacion extends Conexion
{

  private $pdo;

  public function __CONSTRUCT()
  {
    $this->pdo = parent::getConexion();
  }

  //LLEVAR DATOS
  public function getCabeceraById(int $id): array
    {
        $sql = "
            SELECT 
                c.idcotizacion,
                c.idcliente,
                cli.nombre AS cliente,
                c.moneda,
                DATE_FORMAT(c.fechahora, '%Y-%m-%d %H:%i:%s') AS fechahora
            FROM cotizaciones c
            JOIN clientes cli ON cli.idcliente = c.idcliente
            WHERE c.idcotizacion = ?
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
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

      $stmt = $pdo->prepare("CALL spuRegisterCotizaciones(?,?,?,?)");
      $stmt->execute([
        $params["fechahora"],
        $params["vigenciadias"],
        $params["moneda"],
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

      $stmtDetalle = $pdo->prepare("CALL spuInsertDetalleCotizacion(?,?,?,?,?)");
      foreach ($params["productos"] as $producto) {
        error_log("Insertando productos ID: " . $producto["idproducto"]);
        $stmtDetalle->execute([
          $idcotizacion,
          $producto["idproducto"],
          $producto["cantidad"],
          $producto["precio"],
          $producto["descuento"]
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
}
?>