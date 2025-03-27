// buscar_producto.php
<?php
require_once '../config/Server.php';

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    try {
        $pdo = new PDO(SGBD, USER, PASS);
        $stmt = $pdo->prepare("SELECT idproducto, nombre, precio FROM productos WHERE nombre LIKE :search LIMIT 10");
        $stmt->execute(['search' => "%$search%"]);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($productos);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
