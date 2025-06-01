<?php

require_once "../models/Conexion.php";

class Servicio extends Conexion
{
  protected $pdo;

  public function __CONSTRUCT()
  {
    $this->pdo = parent::getConexion();
  }

  public function getServicioBySubcategoria($idsubcategoria): array
  {
    $result = [];
    try {
      $query = "CALL spGetServicioBySubcategoria(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute(
        array($idsubcategoria)
      );
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
    }
    return $result;
  }

  /**
   * Registra un servicio (idsubcategoria + servicio) y devuelve el ID recién creado.
   * El procedimiento almacenado debe hacer: INSERT …; SELECT LAST_INSERT_ID() AS idservicio;
   */
  public function registerServicio($params = []): array
  {
    $response = ['idservicio' => 0, 'servicio' => ''];
    try {
      $query = "CALL spRegisterServicio(?, ?)";
      $stmt = $this->pdo->prepare($query);
      $stmt->execute([
        $params["idsubcategoria"],
        $params["servicio"]
      ]);
      // El SP devuelve un SELECT LAST_INSERT_ID() AS idservicio
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row && isset($row['idservicio'])) {
        $response['idservicio'] = (int)$row['idservicio'];
        $response['servicio']   = $params["servicio"];
      }
    } catch (PDOException $e) {
      error_log("Error DB en registerServicio: " . $e->getMessage());
      // En caso de error, devolvemos id = 0
    }
    return $response;
  }
}