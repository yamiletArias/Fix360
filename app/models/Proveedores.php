<?php

require_once "../models/Conexion.php";

class Proveedor extends Conexion {

    protected $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    /**
     * Obtener todos los proveedores
     * @return array
     */
    public function getAll() {
        try {
            $query = "CALL spListProveedores()";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute();
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Registrar un proveedor
     * @param int $idempresa
     * @return array
     */
    public function add($idempresa) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spRegisterProveedor(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idempresa]);
            $resultado["status"] = true;
            $resultado["message"] = "Proveedor registrado correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }

    /**
     * Obtener un proveedor por ID
     * @param int $idproveedor
     * @return array
     */
    public function findById($idproveedor) {
        try {
            $query = "CALL spGetProveedorById(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idproveedor]);
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Obtener un proveedor por ID de empresa
     * @param int $idempresa
     * @return array
     */
    public function findByEmpresaId($idempresa) {
        try {
            $query = "CALL spGetProveedorByEmpresaId(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idempresa]);
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Actualizar un proveedor
     * @param int $idproveedor
     * @param int $idempresa
     * @return array
     */
    public function update($idproveedor, $idempresa) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spUpdateProveedor(?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idproveedor, $idempresa]);
            $resultado["status"] = true;
            $resultado["message"] = "Proveedor actualizado correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }

    /**
     * Eliminar un proveedor
     * @param int $idproveedor
     * @return array
     */
    public function delete($idproveedor) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spDeleteProveedor(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idproveedor]);
            $resultado["status"] = true;
            $resultado["message"] = "Proveedor eliminado correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }
}
