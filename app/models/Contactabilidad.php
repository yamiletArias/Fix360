<?php
require_once __DIR__ . "/Conexion.php";

class Contactabilidad extends Conexion {
   private $pdo;

   public function __construct() {
      $this->pdo = parent::getConexion();
   }

   /**
    * Devuelve todos los canales de contactabilidad.
    * (usa el SP spGetAllContactabilidad).
    */
   public function getContactabilidad(): array {
      try {
         $query = "CALL spGetAllContactabilidad()";
         $stmt = $this->pdo->prepare($query);
         $stmt->execute();  // No hay parámetros en este SP
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
         die("Error en Contactabilidad::getContactabilidad → " . $e->getMessage());
      }
   }

   /**
    * Llama al SP sp_grafico_contactabilidad_periodo para devolver la
    * cantidad de clientes por contactabilidad según el periodo y rango de fechas.
    *
    * @param string $periodo     'ANUAL', 'MENSUAL' o 'SEMANAL'
    * @param string $fechaDesde  Fecha en formato 'YYYY-MM-DD'
    * @param string $fechaHasta  Fecha en formato 'YYYY-MM-DD'
    * @return array              Array asociativo con las filas devueltas por el SP
    */
   public function getGraficoContactabilidad($params = []): array {
      try {
         // Preparamos la llamada al procedimiento con 3 parámetros posicionales
         $query = "CALL spGraficoContactabilidadPorPeriodo(?, ?, ?)";
         $stmt = $this->pdo->prepare($query);

         // Ejecutamos pasando los valores en el mismo orden de los "?" del CALL
         $stmt->execute([
            $params["periodo"],
            $params["fechaDesde"],
            $params["fechaHasta"]
         ]);

         return $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
         die("Error en Contactabilidad::getGraficoContactabilidad → " . $e->getMessage());
      }
   }
}
?>
