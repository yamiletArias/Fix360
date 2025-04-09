<?php

require_once "../models/Conexion.php";

class Compra extends Conexion
{
  protected $pdo;

  public function __CONSTRUCT()
  {
    $this->pdo = parent::getConexion();
  }

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

  public function buscarProductoCompra(string $termino): array
  {
    $result = [];
    try {
      $sql = "CALL buscar_producto_compras(:termino)";
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindParam(':termino', $termino, PDO::PARAM_STR);
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      throw new Exception("Error al buscar productos en compra: " . $e->getMessage());
    }
    return $result;
  }


}