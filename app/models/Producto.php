<?php

require_once "Conexion.php";

class Producto extends Conexion
{

    private $pdo;

    public function __CONSTRUCT()
    {
        $this->pdo = parent::getConexion();
    }

    /**
     * Lista todos los productos
     * @return array
     */
    public function getAll(): array
    {
        $result = [];
        try {
            $query = "SELECT * FROM vwproductos ORDER BY subcategoria";
            $cmd = $this->pdo->prepare($query);
            $cmd->execute();
            $result = $cmd->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $result;
    }

    /**
     * Obtiene un producto por su ID
     * @param int $idproducto
     * @return array
     */
    public function find($idproducto)
    {
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
     * Agrega un nuevo producto + kardex (stockmin, stockmax)
     * @param array $params {
     *   @var int    idsubcategoria
     *   @var int    idmarca
     *   @var string descripcion
     *   @var float  precio
     *   @var string presentacion
     *   @var string undmedida
     *   @var float  cantidad
     *   @var string img          -- ruta o ''
     *   @var int    stockmin
     *   @var int    stockmax
     * }
     * @return int  El nuevo idproducto (0 si falla)
     */
    public function add(array $params): int
    {
        $idProducto = 0;
        try {
            // Nótese que ya pasamos 10 IN y luego usamos @idproducto como OUT
            $sql = "CALL spRegisterProducto(?,?,?,?,?,?,?,?,?,?,@idproducto)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $params["idsubcategoria"],
                $params["idmarca"],
                $params["descripcion"],
                $params["precio"],
                $params["presentacion"],
                $params["undmedida"],
                $params["cantidad"],
                $params["img"],
                $params["stockmin"],   // nuevo
                $params["stockmax"]    // nuevo
            ]);
            

            // Recuperar la variable OUT
            $idProducto = (int) $this->pdo
                ->query("SELECT @idproducto")
                ->fetchColumn();

        } catch (Exception $e) {
            error_log("Producto::add error: " . $e->getMessage());
        }

        return $idProducto;
    }

    /**
     * Actualiza un producto
     * @param array $params
     * @return array
     */
    public function update($params = [])
    {
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
    public function delete($idproducto)
    {
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
