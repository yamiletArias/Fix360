<?php

require_once "../models/Conexion.php";

class Persona extends Conexion{

  protected $pdo;

  public function __CONSTRUCT(){
    $this->pdo = parent::getConexion();
  }

  //Todos los métodos tendrán nombre estandarizados
  //Todos los métodos retornarán valores

  /**
   * Retorna todos los registros activos de personas
   * @return array retorna una arreglo
   */
  public function getAll(){
    try{
      //Consulta - T-SQL / STORE PROCEDURE
      $query = "SELECT * FROM personas";
      //Preparar la consulta
      $cmd = $this->pdo->prepare($query);
      //Ejecutar la consulta y pasarle parámetros (opcional)
      $cmd->execute();
      //Retornar la colección de datos completa (ALL) en formato arreglo asociativo
      return $cmd->fetchAll(PDO::FETCH_ASSOC);

    }catch(Exception $e){
      die($e->getMessage());
    }
  }

  public function add($params = []){

    //Objeto que tenga información sobre el proceso
    $resultado = [
      "status"    => false,
      "message"   => ""
    ];

    try{
      //Consulta - T-SQL / STORE PROCEDURE
      $query = "INSERT INTO personas (apellidos, nombres, dni, fechanac, direccion, telefono) VALUES (?,?,?,?,?,?)";
      //Preparar la consulta
      $cmd = $this->pdo->prepare($query);
      //Ejecutar la consulta y pasarle parámetros (opcional)
      $cmd->execute(array(
        $params["apellidos"],
        $params["nombres"],
        $params["dni"],
        $params["fechanac"],
        $params["direccion"],
        $params["telefono"]
      ));

      $resultado["status"] = true;
      $resultado["message"] = "El proceso finalizó correctamente"; //SweetAlert
    }catch(Exception $e){
      $resultado["message"] = $e->getCode();
    }finally{
      return $resultado;
    }
  }

  public function update(){}
  public function delete(){}
  public function find(){}

}