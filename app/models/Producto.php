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
    try {
        // 13 placeholders + @idproducto
        $sql = "CALL spRegisterProducto(?,?,?,?,?,?,?,?,?,?,?,?,?,@idproducto)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $params["idsubcategoria"], // 1
            $params["idmarca"],        // 2
            $params["descripcion"],    // 3
            $params["precioc"],        // 4
            $params["preciov"],        // 5
            $params["presentacion"],   // 6
            $params["undmedida"],      // 7
            $params["cantidad"],       // 8
            $params["img"],            // 9
            $params["codigobarra"],    // 10
            $params["stockInicial"],   // 11
            $params["stockmin"],       // 12
            $params["stockmax"]        // 13
        ]);

        $idProducto = (int) $this->pdo
            ->query("SELECT @idproducto")
            ->fetchColumn();

        return $idProducto;
    } catch (Exception $e) {
        throw new Exception("Producto::add ERROR SQL: " . $e->getMessage());
    }
}



    /**
     * Actualiza sólo descripción, presentación, precio, img, stockmin y stockmax
     * @param array $params {
     *   @var int     idproducto
     *   @var string  descripcion
     *   @var float   cantidad      // presentación
     *   @var float   precio
     *   @var string  img           // ruta o ''
     *   @var int     stockmin
     *   @var int     stockmax      // o NULL
     * }
     * @return bool  true si se actualizó correctamente
     */
    public function update(array $params): bool
    {
        try {
            $sql = "CALL spUpdateProducto(?,?,?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $params['idproducto'],
                $params['descripcion'],
                $params['cantidad'],
                $params['precioc'],     // nuevo
                $params['preciov'],     // nuevo
                $params['img']   ?? '',
                $params["codigobarra"],
                $params['stockmin'],
                $params['stockmax']
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Producto::update error: " . $e->getMessage());
            return false;
        }
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

    public function find($idproducto)
    {
        try {
            $query = "CALL spGetProductoById(?)";
            $cmd   = $this->pdo->prepare($query);
            $cmd->execute([$idproducto]);
            return $cmd->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
