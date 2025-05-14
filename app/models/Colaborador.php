<?php
require_once "Conexion.php";
class Colaborador extends Conexion {

    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    /**
     * Iniciar sesión de un colaborador.
     * @param string $namuser Nombre de usuario.
     * @param string $passuser Contraseña en texto plano.
     * @return array|null Resultado del login: ['status'=>'SUCCESS','idcolaborador'=>x] o ['status'=>'FAILURE']
     */
    public function login($namuser, $passuser) {
        try {
            $stmt = $this->pdo->prepare("CALL spLoginColaborador(:namuser, :passuser)");
            $stmt->bindParam(':namuser', $namuser, PDO::PARAM_STR);
            $stmt->bindParam(':passuser', $passuser, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: ['status' => 'FAILURE'];
        } catch (PDOException $e) {
            return ['status' => 'FAILURE', 'message' => 'Error en login: ' . $e->getMessage()];
        }
    }
    /**
     * Obtiene todos los colaboradores activos y con contrato vigente.
     * @return array Lista de colaboradores.
     */
    public function getAll() {
        try {
            $query = "SELECT * FROM vwColaboradoresActivosVigentes";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute();
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Registra un nuevo colaborador.
     * @param array $params Datos: idcontrato, namuser, passuser
     * @return array Estado del registro.
     */
    public function add($params = []) {
        try {
            $stmt = $this->pdo->prepare("CALL spRegisterColaborador(:idcontrato, :namuser, :passuser)");
            $stmt->bindParam(':idcontrato', $params['idcontrato'], PDO::PARAM_INT);
            $stmt->bindParam(':namuser',    $params['namuser'],    PDO::PARAM_STR);
            $stmt->bindParam(':passuser',   $params['passuser'],   PDO::PARAM_STR);
            $stmt->execute();
            return ['status' => true, 'message' => 'Colaborador registrado correctamente.'];
        } catch (PDOException $e) {
            return ['status' => false, 'message' => 'Error al registrar: ' . $e->getMessage()];
        }
    }
}
