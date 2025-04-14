<?php

require_once "../models/Conexion.php";

class Compra extends Conexion
{
  protected $pdo;

  public function __CONSTRUCT()
  {
    $this->pdo = parent::getConexion();
  }

  public function getAll(): array
  {
      $result = [];
      try {
          $sql = "SELECT * FROM vs_compras ORDER BY id DESC";
          $stmt = $this->pdo->prepare($sql);
          $stmt->execute();
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
          throw new Exception("Error al obtener las compras: " . $e->getMessage());
      }
      return $result;
  }

  //obtener los proveedores
  public function getProveedoresCompra(): array
  {
    try {
      $query = "CALL spuGetProveedores()";
      $statement = $this->pdo->prepare($query);
      $statement->execute();
      return $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      // Detallar mejor el error, especialmente útil en producción
      die("Error en model: " . $e->getMessage());
    }
  }

  //buscar producto
  public function buscarProductoCompra(string $termino): array
  {
    $result = [];
    try {
      $sql = "CALL buscar_producto(:termino)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception("Error al buscar productos en compra: " . $e->getMessage());
    }
    return $result;
  }

  //registrar compra y detalle compra
  public function registerCompras($params = []): int
  {
    try {
      $pdo = $this->pdo;
      $pdo->beginTransaction();

      error_log("Parametros para spuRegisterCompra: " . print_r($params, true));

      // Llamada al Stored Procedure spuRegisterCompra
      $stmtCompra = $pdo->prepare("CALL spuRegisterCompra(?, ?, ?, ?, ?, ?)");
      $stmtCompra->execute([
        $params["fechacompra"],
        $params["tipocom"],
        $params["numserie"],
        $params["numcom"],
        $params["moneda"],
        $params["idproveedor"]
      ]);

      error_log("Stored Procedure spuRegisterCompra ejecutado.");

      // Captura del resultado con idcompra
      $result = [];
      do {
        $tmp = $stmtCompra->fetch(PDO::FETCH_ASSOC);
        error_log("Resultado fetch: " . print_r($tmp, true));
        if ($tmp && isset($tmp['idcompra'])) {
          $result = $tmp;
          break;
        }
      } while ($stmtCompra->nextRowset());

      $stmtCompra->closeCursor();

      $idcompra = $result['idcompra'] ?? 0;

      if (!$idcompra) {
        error_log("SP ejecutado pero no devolvió ID de compra.");
        throw new Exception("No se pudo obtener el id de la compra.");
      }

      // Insertar detalle por producto
      $stmtDetalle = $pdo->prepare("CALL spuInsertDetalleCompra(?, ?, ?, ?, ?)");
      $idcompra = $result['idcompra'] ?? 0;

      foreach ($params["productos"] as $producto) {
        $stmtDetalle->execute([
          $idcompra, // ← este es el idcompra generado que se pasa al detalle
          $producto["idproducto"],
          $producto["cantidad"],
          $producto["precio"],
          $producto["descuento"]
        ]);
      }

      $pdo->commit();
      error_log("Compra registrada con id: " . $idcompra);
      return $idcompra;

    } catch (PDOException $e) {
      $pdo->rollBack();
      error_log("Error DB: " . $e->getMessage());
      return 0;

    } catch (Exception $ex) {
      error_log("Error general: " . $ex->getMessage());
      return 0;
    }
  }



}