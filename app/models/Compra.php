<?php

require_once "../models/Conexion.php";

class Compra extends Conexion {
  protected $pdo;
  public function __CONSTRUCT(){
    $this->pdo = parent::getConexion();
  }

  public function getAll() {
    try {
      $query = "CALL spListCompras()";
      $cmd = $this->pdo->prepare($query);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  public function add($params = []) {
    $resultado = [
      "status" => false,
      "message" => ""
    ];

    try {
      $query = "CALL spRegisterCompra (?,?,?,?,?,?,?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idproveedor"],
        $params["idcolaborador"],
        $params["fechacompra"],
        $params["tipocom"],
        $params["numserie"],
        $params["numcom"],
        $params["moneda"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Registro de compra hecha correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }

  public function find($params = []) {
    try {
      $query = "CALL spGetCompraById(?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([$params["idcompra"]]);
    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  public function update($params = []){
    $resultado = [
      "status" => false,
      "message" => ""
    ];
    try {
      $query = "CALL spUpdateCompra(?,?,?,?,?,?,?,?)";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute([
        $params["idcompra"],
        $params["idproveedor"],
        $params["idcolaborador"],
        $params["fechacompra"],
        $params["tipocom"],
        $params["numserie"],
        $params["numcom"],
        $params["moneda"]
      ]);
      $resultado["status"] = true;
      $resultado["message"] = "Registro actualizado correctamente";
    } catch (Exception $e) {
      $resultado["message"] = $e->getMessage();
    } finally {
      return $resultado;
    }
  }
}