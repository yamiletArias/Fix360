<?php

require_once "../models/Conexion.php";

class Categoria extends Conexion {

    protected $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    /**
     * Retorna todas las categorías registradas.
     * @return array Lista de categorías.
     */
    public function getAll() {
        try {
            $query = "CALL spListCategorias()";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute();
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Registra una nueva categoría.
     * @param array $params Contiene el nombre de la categoría.
     * @return array Resultado del proceso.
     */
    public function add($params = []) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spRegisterCategoria(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$params["categoria"]]);
            $resultado["status"] = true;
            $resultado["message"] = "Categoría registrada correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }

    /**
     * Busca una categoría por su ID.
     * @param int $idcategoria Identificador de la categoría.
     * @return array Información de la categoría.
     */
    public function find($idcategoria) {
        try {
            $query = "CALL spGetCategoriaById(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idcategoria]);
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Actualiza una categoría existente.
     * @param array $params Contiene el ID y el nuevo nombre de la categoría.
     * @return array Resultado del proceso.
     */
    public function update($params = []) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spUpdateCategoria(?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$params["idcategoria"], $params["categoria"]]);
            $resultado["status"] = true;
            $resultado["message"] = "Categoría actualizada correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }

    /**
     * Elimina una categoría por su ID.
     * @param int $idcategoria Identificador de la categoría.
     * @return array Resultado del proceso.
     */
    public function delete($idcategoria) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spDeleteCategoria(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idcategoria]);
            $resultado["status"] = true;
            $resultado["message"] = "Categoría eliminada correctamente.";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        } finally {
            return $resultado;
        }
    }
}
