<?php
//require_once '../models/Conexion.php';
//require_once '../models/Venta.php';

if (isset($_SERVER['REQUEST_METHOD'])) {

    header('Content-Type: application/json; charset=utf-8');

    require_once '../models/Venta.php';
    $venta = new Venta();

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            echo json_encode($venta->getAll());
            break;

        case 'POST':
            // Obtener los datos enviados desde el frontend (AJAX)
            $input = file_get_contents('php://input');
            $dataJSON = json_decode($input, true);
            
            // Obtener el nombre del cliente desde los datos
            $nombreCliente = $dataJSON['cliente'];
            
            // Buscar el idcliente a partir del nombre del cliente
            try {
                $pdo = Conexion::getConexion();
                
                // Consultar el idcliente basado en el nombre del cliente
                $stmt = $pdo->prepare("SELECT idcliente FROM clientes WHERE nombres = :nombreCliente LIMIT 1");
                $stmt->bindParam(':nombreCliente', $nombreCliente);
                $stmt->execute();
                $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($cliente) {
                    $idcliente = $cliente['idcliente'];  // Obtener el idcliente encontrado
                } else {
                    echo json_encode(["status" => "error", "message" => "Cliente no encontrado"]);
                    exit;
                }

            } catch (PDOException $e) {
                echo json_encode(["status" => "error", "message" => "Error al obtener el cliente: " . $e->getMessage()]);
                exit;
            }

            // Preparar los datos de la venta
            $ventaData = [
                'tipo' => $dataJSON['tipocom'],
                'numserie' => isset($dataJSON['numserie']) ? $dataJSON['numserie'] : null,
                'numcomprobante' => $dataJSON['numcom'],
                'idcliente' => $idcliente, // Usamos el idcliente encontrado
                'fecha' => $dataJSON['fechahora'],
                'tipomoneda' => $dataJSON['moneda'],
                'producto' => json_encode(array_column($dataJSON['productos'], 'idproducto')),
                'precio' => json_encode(array_map('floatval', array_column($dataJSON['productos'], 'precioventa'))),
                'cantidad' => json_encode(array_map('intval', array_column($dataJSON['productos'], 'cantidad'))),
                'descuento' => json_encode(array_map('floatval', array_column($dataJSON['productos'], 'descuento')))
            ];

            // Llamar al procedimiento para registrar la venta
            try {
                $stmt = $pdo->prepare("CALL spRegistroVentas(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $ventaData['tipo'],              // Parám. 1
                    $ventaData['numserie'],          // Parám. 2
                    $ventaData['numcomprobante'],    // Parám. 3
                    $ventaData['idcliente'],         // Parám. 4
                    $ventaData['fecha'],             // Parám. 5
                    $ventaData['tipomoneda'],        // Parám. 6
                    $ventaData['producto'],          // Parám. 7
                    $ventaData['precio'],            // Parám. 8
                    $ventaData['cantidad'],          // Parám. 9
                    $ventaData['descuento']          // Parám. 10
                ]);
                
                echo json_encode(["status" => "success", "message" => "Venta registrada con éxito."]);
            
            } catch (PDOException $e) {
                echo json_encode(["status" => "error", "message" => "Error al registrar la venta: " . $e->getMessage()]);
            }
            break;
    }
}
?>