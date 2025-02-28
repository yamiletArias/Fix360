<?php

require_once "../models/Conexion.php";

class Cliente extends Conexion {

    protected $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    /**
     * Obtener todos los clientes
     * @return array
     */
    public function getAll() {
        try {
            $query = "CALL spListClientes()";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute();
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Registrar un cliente
     * @param int|null $idempresa
     * @param int|null $idpersona
     * @return array
     */
    public function add($idempresa = null, $idpersona = null) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spRegisterCliente(?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idempresa, $idpersona]);
            $resultado["status"] = true;
            $resultado["message"] = "Cliente registrado correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }

    /**
     * Obtener un cliente por ID
     * @param int $idcliente
     * @return array
     */
    public function findById($idcliente) {
        try {
            $query = "CALL spGetClienteById(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idcliente]);
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Obtener un cliente por ID de empresa o persona
     * @param int|null $idempresa
     * @param int|null $idpersona
     * @return array
     */
    public function findByEmpresaOPersona($idempresa = null, $idpersona = null) {
        try {
            $query = "CALL spGetClienteByEmpresaOPersona(?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idempresa, $idpersona]);
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Actualizar un cliente
     * @param int $idcliente
     * @param int|null $idempresa
     * @param int|null $idpersona
     * @return array
     */
    public function update($idcliente, $idempresa = null, $idpersona = null) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spUpdateCliente(?, ?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idcliente, $idempresa, $idpersona]);
            $resultado["status"] = true;
            $resultado["message"] = "Cliente actualizado correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }

    /**
     * Eliminar un cliente
     * @param int $idcliente
     * @return array
     */
    public function delete($idcliente) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spDeleteCliente(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idcliente]);
            $resultado["status"] = true;
            $resultado["message"] = "Cliente eliminado correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }
}
