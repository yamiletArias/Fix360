<?php
require_once "/app/models/Conexion.php";

class Contactabilidad extends Conexion {
   private $pdo;

   public function __construct() {
      $this->pdo = parent::getConexion();
   }

   public function getContactabilidad(): array {
      try {
         $query = "CALL spGetAllContactabilidad()";
         $statement = $this->pdo->prepare($query);
         $statement->execute();
         return $statement->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
         die("Error en model" . $e->getMessage());
      }
   }
}
?>
