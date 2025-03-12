<?php

require_once "Conexion.php";

class Producto extends Conexion {

    private $pdo;

    public function __CONSTRUCT() {
        $this->pdo = parent::getConexion();
    }

    /**
     * Lista todos los productos
     * @return array
     */
    public function getAll() {
        try {
            $query = "CALL spListProductos()";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute();
            return $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Obtiene un producto por su ID
     * @param int $idproducto
     * @return array
     */
    public function find($idproducto) {
        try {
            $query = "CALL spGetProductoById(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idproducto]);
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     * Agrega un nuevo producto
     * @param array $params
     * @return array
     */
    public function add($params = []) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spRegisterProducto(?, ?, ?, ?, ?, ?, ?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([
                $params["idmarca"],
                $params["idsubcategoria"],
                $params["descripcion"],
                $params["precio"],
                $params["presentacion"],
                $params["undmedida"],
                $params["cantidad"],
                $params["img"]
            ]);
            $resultado["status"] = true;
            $resultado["message"] = "Producto registrado correctamente";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        }
        return $resultado;
    }

    /**
     * Actualiza un producto
     * @param array $params
     * @return array
     */
    public function update($params = []) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spUpdateProducto(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([
                $params["idproducto"],
                $params["idmarca"],
                $params["idsubcategoria"],
                $params["descripcion"],
                $params["precio"],
                $params["presentacion"],
                $params["undmedida"],
                $params["cantidad"],
                $params["img"]
            ]);
            $resultado["status"] = true;
            $resultado["message"] = "Producto actualizado correctamente";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        }
        return $resultado;
    }

    /**
     * Elimina un producto
     * @param int $idproducto
     * @return array
     */
    public function delete($idproducto) {
        $resultado = ["status" => false, "message" => ""];
        try {
            $query = "CALL spDeleteProducto(?)";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute([$idproducto]);
            $resultado["status"] = true;
            $resultado["message"] = "Producto eliminado correctamente";
        } catch (Exception $e) {
            $resultado["message"] = $e->getMessage();
        }
        return $resultado;
    }
}
