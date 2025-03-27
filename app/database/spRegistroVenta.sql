
DELIMITER $$

CREATE PROCEDURE spRegistroVentas(
    IN _tipo               VARCHAR(10),
    IN _numserie           CHAR(12),
    IN _numcomprobante     CHAR(8),
    IN _nomcliente         VARCHAR(40),
    IN _fecha              DATE,
    IN _tipomoneda         VARCHAR(10),
    IN _producto           JSON,
    IN _precio             JSON,
    IN _cantidad           JSON,
    IN _descuento          JSON
)
BEGIN
    DECLARE _idcliente INT;
    DECLARE _idcolaborador INT;
    DECLARE _idventa INT;
    DECLARE _idproducto INT;
    DECLARE i INT DEFAULT 0;
    DECLARE num_productos INT;
    DECLARE _new_numserie CHAR(12);

    -- Generación automática del número de serie si no se envía uno
    IF _numserie IS NULL THEN
        SET _new_numserie = (SELECT CONCAT('V', LPAD(COALESCE(MAX(CAST(SUBSTRING(numserie, 2) AS UNSIGNED)), 0) + 1, 5, '0'))
                             FROM ventas);
    ELSE
        SET _new_numserie = _numserie;
    END IF;

    -- Obtener el idcliente según el nombre del cliente
    SELECT idcliente INTO _idcliente FROM clientes WHERE nombres = _nomcliente LIMIT 1;

    -- Asignar un idcolaborador (esto debe ser dinámico según el usuario autenticado)
    SET _idcolaborador = 1;  -- Asumiendo que el colaborador es el ID 1 por defecto.

    -- Insertar en la tabla ventas
    INSERT INTO ventas (idcliente, idcolaborador, tipocom, numserie, numcom, fechahora)
    VALUES (_idcliente, _idcolaborador, _tipo, _new_numserie, _numcomprobante, _fecha);

    -- Obtener el id de la venta insertada
    SET _idventa = LAST_INSERT_ID();

    -- Calcular el número de productos en el JSON
    SET num_productos = JSON_LENGTH(_producto);

    -- Insertar los productos
    WHILE i < num_productos DO
        -- Obtener el id del producto desde el JSON
        SET _idproducto = JSON_UNQUOTE(JSON_EXTRACT(_producto, CONCAT('$[', i, ']')));

        -- Obtener el precio, cantidad y descuento desde el JSON
        SET _precio = JSON_UNQUOTE(JSON_EXTRACT(_precio, CONCAT('$[', i, ']')));
        SET _cantidad = JSON_UNQUOTE(JSON_EXTRACT(_cantidad, CONCAT('$[', i, ']')));
        SET _descuento = JSON_UNQUOTE(JSON_EXTRACT(_descuento, CONCAT('$[', i, ']')));

        -- Insertar en la tabla detallecotizacion
        INSERT INTO detallecotizacion (idcotizacion, idproducto, cantidad, precio, descuento)
        VALUES (_idventa, _idproducto, _cantidad, _precio, _descuento);

        -- Incrementar el índice
        SET i = i + 1;
    END WHILE;

END $$

DELIMITER ;



-- listar
DELIMITER $$

CREATE PROCEDURE spListarVentas(
    IN _numserie CHAR(12)  -- Puedes filtrar por número de serie, si es necesario
)
BEGIN
    -- Variables para obtener los detalles de la venta
    DECLARE _idventa INT;
    DECLARE _idcliente INT;
    DECLARE _nombre_cliente VARCHAR(40);
    DECLARE _tipocom VARCHAR(10);
    DECLARE _fecha DATETIME;
    DECLARE _numcom VARCHAR(8);
    DECLARE _total DECIMAL(10, 2);

    -- Mostrar ventas con sus detalles
    -- Si se pasa un _numserie, se filtra por este valor, de lo contrario, se mostrarán todas las ventas
    IF _numserie IS NOT NULL THEN
        -- Obtener la información de la venta
        SELECT v.idventa, v.idcliente, p.nombres, p.apellidos, v.tipocom, v.numserie, v.numcom, v.fechahora
        INTO _idventa, _idcliente, _nombre_cliente, _tipocom, _numserie, _numcom, _fecha
        FROM ventas v
        JOIN clientes c ON v.idcliente = c.idcliente
        JOIN personas p ON c.idpersona = p.idpersona  -- Relacionamos con la tabla personas
        WHERE v.numserie = _numserie;
    ELSE
        -- Si no se pasa un _numserie, mostramos todas las ventas
        SELECT v.idventa, v.idcliente, p.nombres, p.apellidos, v.tipocom, v.numserie, v.numcom, v.fechahora
        INTO _idventa, _idcliente, _nombre_cliente, _tipocom, _numserie, _numcom, _fecha
        FROM ventas v
        JOIN clientes c ON v.idcliente = c.idcliente
        JOIN personas p ON c.idpersona = p.idpersona;  -- Relacionamos con la tabla personas
    END IF;

    -- Mostrar los productos asociados a la venta seleccionada
    SELECT 
        dv.iddetventa, 
        p.descripcion AS producto, 
        dv.cantidad, 
        dv.precioventa, 
        dv.descuento, 
        (dv.cantidad * dv.precioventa * (1 - dv.descuento / 100)) AS total_producto
    FROM detalleventa dv
    JOIN productos p ON dv.idproducto = p.idproducto
    WHERE dv.idventa = _idventa;

    -- Sumar el total de todos los productos
    SELECT SUM(dv.cantidad * dv.precioventa * (1 - dv.descuento / 100)) AS total_venta
    INTO _total
    FROM detalleventa dv
    WHERE dv.idventa = _idventa;
    
    -- Verificar el total
    SELECT _total AS total_venta;

END $$

DELIMITER ;


SHOW PROCEDURE STATUS WHERE Name = 'spListarVentas';

CALL spListarVentas('B001');

CALL spListarVentas(NULL);

SELECT * FROM ventas WHERE numserie = 'B001';   

SELECT * FROM detalleventa WHERE idventa = 2;  -- Asumiendo que la venta con 'B001' tiene idventa = 1.

