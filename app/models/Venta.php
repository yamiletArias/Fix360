<?php

require_once "../models/Conexion.php";

class Venta extends Conexion{
    
    private $pdo;

    public function __CONSTRUCT() {
      $this->pdo = parent::getConexion();
    }
    
    public function getAll(): array{
        $result = [];
        try{
            $sql = "SELECT * FROM vs_ventas ORDER BY id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception($e->getMessage());
        }
        return $result;
    }

    public function add($params = []):int{
        $numRow = 0;
        try{
            $sql = "INSERT INTO ventas (idcliente, idcolaborador, tipocom, fechahora, numserie, numcom)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute(
                array(
                    $params["idcliente"],
                    $params["idcolaborador"],
                    $params["idproducto"],
                    $params["idpromocion"],
                    $params["tipocom"],
                    $params["fechahora"],
                    $params["numserie"],
                    $params["numcom"]
                )
            );
            $numRow = $stmt->rowCount();
        }
        catch(PDOException $e){
            throw new Exception($e->getMessage());
        }
        return $numRow;
    }

}

?>
