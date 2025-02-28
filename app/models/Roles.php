<?php

require_once "../models/Conexion.php";

class RolModel extends Conexion {
    protected $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    /**
     * Obtener todos los roles
     * @return array
     */
    public function getAll() {
        try {
            $query = "CALL spListRoles()";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute();
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Registrar un rol
     * @param string $rol
     * @return array
     */
    public function add($rol) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spRegisterRol(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$rol]);
            $resultado["status"] = true;
            $resultado["message"] = "Rol registrado correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }

    /**
     * Obtener un rol por ID
     * @param int $idrol
     * @return array
     */
    public function findById($idrol) {
        try {
            $query = "CALL spGetRolById(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idrol]);
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Actualizar un rol
     * @param int $idrol
     * @param string $rol
     * @return array
     */
    public function update($idrol, $rol) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spUpdateRol(?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idrol, $rol]);
            $resultado["status"] = true;
            $resultado["message"] = "Rol actualizado correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }

    /**
     * Eliminar un rol
     * @param int $idrol
     * @return array
     */
    public function delete($idrol) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spDeleteRol(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idrol]);
            $resultado["status"] = true;
            $resultado["message"] = "Rol eliminado correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }
}
