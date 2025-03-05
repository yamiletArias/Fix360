<?php

require_once "../models/Conexion.php";

class Subcategoria extends Conexion {

    protected $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    /**
     * Retorna todas las subcategorías con su respectiva categoría.
     * @return array
     */
    public function getAll() {
        try {
            $query = "CALL spListSubcategorias()";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute();
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Agrega una nueva subcategoría.
     * @param array $params Datos de la subcategoría.
     * @return array
     */
    public function add($params = []) {
        $resultado = [
            "status"  => false,
            "message" => ""
        ];
        try {
            $query = "CALL spRegisterSubcategoria(?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([
                $params["idcategoria"],
                $params["subcategoria"]
            ]);
            $resultado["status"] = true;
            $resultado["message"] = "Subcategoría registrada correctamente";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }

    /**
     * Obtiene una subcategoría por su ID.
     * @param int $idsubcategoria ID de la subcategoría.
     * @return array
     */
    public function find($idsubcategoria) {
        try {
            $query = "CALL spGetSubcategoriaById(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idsubcategoria]);
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Actualiza una subcategoría.
     * @param array $params Datos de la subcategoría.
     * @return array
     */
    public function update($params = []) {
        $resultado = [
            "status"  => false,
            "message" => ""
        ];
        try {
            $query = "CALL spUpdateSubcategoria(?, ?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([
                $params["idsubcategoria"],
                $params["idcategoria"],
                $params["subcategoria"]
            ]);
            $resultado["status"] = true;
            $resultado["message"] = "Subcategoría actualizada correctamente";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }

    /**
     * Elimina una subcategoría.
     * @param int $idsubcategoria ID de la subcategoría.
     * @return array
     */
    public function delete($idsubcategoria) {
        $resultado = [
            "status"  => false,
            "message" => ""
        ];
        try {
            $query = "CALL spDeleteSubcategoria(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idsubcategoria]);
            $resultado["status"] = true;
            $resultado["message"] = "Subcategoría eliminada correctamente";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }
}
