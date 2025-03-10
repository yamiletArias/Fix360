<?php

require_once "Conexion.php";

/**
 * Clase Colaborador
 */
class Colaborador extends Conexion {

    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    /**
     * Iniciar sesión de un colaborador.
     * @param string $namuser Nombre de usuario.
     * @return array Datos del colaborador si existe.
     */
    public function login($namuser) {
        try {
            $stmt = $this->pdo->prepare("CALL spu_colaboradores_login(:namuser)");
            $stmt->bindParam(":namuser", $namuser, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: null;
        } catch (PDOException $e) {
            return ["esCorrecto" => false, "mensaje" => "Error en login: " . $e->getMessage()];
        }
    }

    /**
     * Obtiene todos los colaboradores con su información detallada.
     * @return array Lista de colaboradores.
     */
    public function getAll() {
        try {
            $query = "SELECT * FROM vw_colaboradores";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute();
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Registra un nuevo colaborador.
     * @param array $params Datos del colaborador.
     * @return array Resultado del proceso.
     */
    public function add($params = []) {
        try {
            $query = "CALL spRegisterColaborador(?, ?, ?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([
                $params["idcontrato"],
                $params["namuser"],
                password_hash($params["passuser"], PASSWORD_BCRYPT),
                $params["estado"]
            ]);
            return ["status" => true, "message" => "Colaborador registrado correctamente."];
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    /**
     * Busca un colaborador por su nombre de usuario.
     * @param string $namuser Nombre de usuario.
     * @return array Datos del colaborador.
     */
    public function find($namuser) {
        try {
            $query = "CALL spGetColaboradorByUser(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$namuser]);
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Actualiza la información de un colaborador.
     * @param array $params Datos actualizados.
     * @return array Resultado del proceso.
     */
    public function update($params = []) {
        try {
            $query = "CALL spUpdateColaborador(?, ?, ?, ?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([
                $params["idcolaborador"],
                $params["idcontrato"],
                $params["namuser"],
                password_hash($params["passuser"], PASSWORD_BCRYPT),
                $params["estado"]
            ]);
            return ["status" => true, "message" => "Colaborador actualizado correctamente."];
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    /**
     * Elimina un colaborador por su ID.
     * @param int $idcolaborador ID del colaborador.
     * @return array Resultado del proceso.
     */
    public function delete($idcolaborador) {
        try {
            $query = "CALL spDeleteColaborador(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idcolaborador]);
            return ["status" => true, "message" => "Colaborador eliminado correctamente."];
        } catch (Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }
}
