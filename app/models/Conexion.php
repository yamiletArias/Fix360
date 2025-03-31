
<?php

class Conexion{

  private static $host = "localhost";
  private static $dbname = "dbfix360";
  private static $username = "root";
  private static $password = "";
  private static $charset = "utf8mb4";
  private static $conexion = null; //objeto conexion


  //se reutilizara la conexion activa
public static function getConexion() {
  if(self::$conexion === null) {
    try {
      //cadena de conexion
      //mysql:host=localhost;port=3306;dbname=tecnoperu;charset=utf8mb4
      $DSN = "mysql:host=" . self::$host .";port=3306;dbname=" . self::$dbname . ";charset=". self::$charset;

      $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false 
      ];

      self::$conexion = new PDO($DSN, self::$username,self::$password, $options);

    } catch (PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }
  return self::$conexion;

}
public function desconectar(){
  self::$conexion = null;
}

}
