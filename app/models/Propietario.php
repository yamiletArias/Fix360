<?php
require_once "../models/Conexion.php";

class Propietario extends Conexion {

  private $pdo;

  public function __CONSTRUCT() {
    $this->pdo = parent::getConexion();
  }

  /**
   * Busca propietarios (clientes) según el tipo (persona o empresa),
   * el método de búsqueda y el valor a buscar.
   *
   * Para personas:
   *   - Si $metodo es 'dni', se busca en el campo numdoc.
   *   - Si $metodo es 'nombre', se busca concatenando nombres y apellidos.
   *
   * Para empresas:
   *   - Si $metodo es 'ruc', se busca en el campo ruc.
   *   - Si $metodo es 'razonsocial', se busca en el campo razonsocial.
   *
   * @param string $tipo      'persona' o 'empresa'
   * @param string $metodo    Método de búsqueda (ej. 'dni', 'nombre', 'ruc', 'razonsocial')
   * @param string $valor     Valor a buscar
   * @return array            Array de resultados
   */
  public function buscarPropietario($tipo, $metodo, $valor): array {
    try {
      if ($tipo === 'persona') {
        if ($metodo === 'dni') {
          $query = "SELECT c.idcliente, CONCAT(p.nombres, ' ', p.apellidos) AS nombre, p.numdoc AS documento 
                    FROM clientes c 
                    INNER JOIN personas p ON c.idpersona = p.idpersona 
                    WHERE p.numdoc LIKE ?";
          $stmt = $this->pdo->prepare($query);
          $stmt->execute(["%$valor%"]);
        } elseif ($metodo === 'nombre') {
          $query = "SELECT c.idcliente, CONCAT(p.nombres, ' ', p.apellidos) AS nombre, p.numdoc AS documento 
                    FROM clientes c 
                    INNER JOIN personas p ON c.idpersona = p.idpersona 
                    WHERE CONCAT(p.nombres, ' ', p.apellidos) LIKE ?";
          $stmt = $this->pdo->prepare($query);
          $stmt->execute(["%$valor%"]);
        }
      } else { // empresa
        if ($metodo === 'ruc') {
          $query = "SELECT c.idcliente, e.nomcomercial AS nombre, e.ruc AS documento 
                    FROM clientes c 
                    INNER JOIN empresas e ON c.idempresa = e.idempresa 
                    WHERE e.ruc LIKE ?";
          $stmt = $this->pdo->prepare($query);
          $stmt->execute(["%$valor%"]);
        } elseif ($metodo === 'razonsocial') {
          $query = "SELECT c.idcliente, e.nomcomercial AS nombre, e.ruc AS documento 
                    FROM clientes c 
                    INNER JOIN empresas e ON c.idempresa = e.idempresa 
                    WHERE e.razonsocial LIKE ?";
          $stmt = $this->pdo->prepare($query);
          $stmt->execute(["%$valor%"]);
        }
      }
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Error DB en Propietario::buscarPropietario: " . $e->getMessage());
      return [];
    }
  }
}
?>
