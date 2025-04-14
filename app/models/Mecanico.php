<?php

require_once "../models/Conexion.php";

class Mecanico extends Conexion{

  protected $pdo;

  public function __CONSTRUCT(){
    $this->pdo = parent::getConexion();
  }

  public function getAllMecanico():array{
    $result = [];

    try {
      $query = "SELECT * FROM vwMecanicos ORDER BY nombres";
      $cmd = $this->pdo->prepare($query);
      $cmd->execute();
      $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
      die($e->getMessage());
  }
  return $result;
  }


}