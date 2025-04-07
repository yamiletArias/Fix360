-- registro ventas

DELIMITER $$
CREATE PROCEDURE spuRegisterVentas (
  IN _tipocom VARCHAR(50),
  IN _fechahora VARCHAR(50),
  IN _numserie VARCHAR(30),
  IN _numcom CHAR(20),
  IN _moneda CHAR(11),
  IN _idcliente INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _numserie_detalle JSON,  -- JSON con múltiples números de serie
  IN _precioventa DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
  DECLARE _idventa INT;
  DECLARE _numserie_item VARCHAR(30);
  DECLARE _i INT DEFAULT 0;
  DECLARE _json_length INT;

  -- Insertar en la tabla ventas
  INSERT INTO ventas (
    idcliente,
    tipocom,
    fechahora,
    numserie,
    numcom,
    moneda
  )
  VALUES (
    _idcliente,
    _tipocom,
    _fechahora,
    _numserie,
    _numcom,
    _moneda
  );

  -- Obtener el ID de la venta insertada
  SET _idventa = LAST_INSERT_ID();

  -- Obtener la longitud del JSON (número de elementos)
  SET _json_length = JSON_LENGTH(_numserie_detalle);

  -- Iterar sobre cada número de serie en el JSON
  WHILE _i < _json_length DO
    SET _numserie_item = JSON_UNQUOTE(JSON_EXTRACT(_numserie_detalle, CONCAT('$[', _i, ']')));

    -- Insertar cada detalle en detalleventa
    INSERT INTO detalleventa (
      idproducto,
      idventa,
      cantidad,
      numserie,
      precioventa,
      descuento
    )
    VALUES (
      _idproducto,
      _idventa,
      _cantidad,
      _numserie_item,  -- Número de serie extraído del JSON
      _precioventa,
      _descuento
    );

    -- Incrementar el índice
    SET _i = _i + 1;
  END WHILE;
END $$
DELIMITER ;

DELIMITER $$

CREATE PROCEDURE spuRegisterVentas (
  IN _tipocom VARCHAR(50),
  IN _fechahora VARCHAR(50),
  IN _numserie VARCHAR(30),
  IN _numcom CHAR(20),
  IN _moneda CHAR(11),
  IN _idcliente INT,
  IN _idproducto INT,
  IN _cantidad INT,
  IN _numserie_detalle JSON,
  IN _precioventa DECIMAL(7,2),
  IN _descuento DECIMAL(5,2)
)
BEGIN
  DECLARE _idventa INT;
  
  -- Insertar en la tabla ventas
  INSERT INTO ventas (
    idcliente,
    tipocom,
    fechahora,
    numserie,
    numcom,
    moneda
  )
  VALUES (
    _idcliente,
    _tipocom,
    _fechahora,
    _numserie,
    _numcom,
    _moneda
  );

  -- Obtener el ID de la venta insertada
  SET _idventa = LAST_INSERT_ID();

  -- Insertar en la tabla detalleventa
  INSERT INTO detalleventa (
    idproducto,
    idventa,
    cantidad,
    numserie,
    precioventa,
    descuento
  )
  VALUES (
    _idproducto,
    _idventa,
    _cantidad,
    _numserie_detalle,
    _precioventa,
    _descuento
  );
  
END $$

DELIMITER ;
SELECT * FROM ventas ORDER BY idventa DESC LIMIT 3;
SELECT * FROM detalleventa ORDER BY iddetventa DESC LIMIT 3;


-- fin registro

-- tipomoneda
DELIMITER $$

CREATE PROCEDURE spuGetMonedasVentas()
BEGIN
  SELECT DISTINCT moneda FROM ventas;
END $$

DELIMITER ;


-- fintipomoneda
-- Procedimiento para buscar clientes
DELIMITER $$
CREATE PROCEDURE buscar_cliente(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        C.idcliente,
        CASE
            WHEN C.idempresa IS NOT NULL AND E.nomcomercial IS NOT NULL THEN E.nomcomercial
            WHEN C.idpersona IS NOT NULL AND P.nombres IS NOT NULL THEN P.nombres
        END AS cliente,
        C.idempresa,
        C.idpersona
    FROM clientes C
    LEFT JOIN empresas E ON C.idempresa = E.idempresa
    LEFT JOIN personas P ON C.idpersona = P.idpersona
    WHERE 
        (E.nomcomercial LIKE CONCAT('%', termino_busqueda, '%') AND E.nomcomercial IS NOT NULL)
        OR 
        (P.nombres LIKE CONCAT('%', termino_busqueda, '%') AND P.nombres IS NOT NULL)
    LIMIT 10;
END$$
DELIMITER ;
-- fin busqueda cliente

-- Buscar producto
DELIMITER $$
CREATE PROCEDURE buscar_producto(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        P.idproducto,
        CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
        DV.precioventa
    FROM productos P
    INNER JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria
    LEFT JOIN detalleventa DV ON P.idproducto = DV.idproducto
    WHERE 
        (S.subcategoria LIKE CONCAT('%', termino_busqueda, '%') OR P.descripcion LIKE CONCAT('%', termino_busqueda, '%'))
    LIMIT 10;
END $$
DELIMITER ;

-- producto con precioventa prueba
DELIMITER $$
CREATE PROCEDURE buscar_producto(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        subcategoria_producto,
        precioventa
    FROM vs_registro_venta
    WHERE subcategoria_producto LIKE CONCAT('%', termino_busqueda, '%')
    LIMIT 10;
END $$
DELIMITER ;

-- Buscar producto prueba2
DELIMITER $$
CREATE PROCEDURE buscar_producto(IN termino_busqueda VARCHAR(255))
BEGIN
    SELECT 
        P.idproducto,
        CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
        DV.precioventa
    FROM productos P
    INNER JOIN subcategorias S ON P.idsubcategoria = S.idsubcategoria
    INNER JOIN detalleventa DV ON P.iddetventa= DV.ididdetventa
    WHERE 
        (S.subcategoria LIKE CONCAT('%', termino_busqueda, '%') OR P.descripcion LIKE CONCAT('%', termino_busqueda, '%'))
    LIMIT 10;
END $$
DELIMITER ;


-- fin busqueda producto

DELIMITER $$

CREATE PROCEDURE spRegistroVentas(
    IN _tipo               VARCHAR(10),
    IN _numserie           CHAR(12),
    IN _numcomprobante     CHAR(8),
    IN _nomcliente         VARCHAR(40),
    IN _fecha              DATE,
    IN _tipomoneda         VARCHAR(10),
    IN _productos          JSON,   -- Nombres de productos
    IN _precio             JSON,   -- Precios
    IN _cantidad           JSON,   -- Cantidades
    IN _descuento          JSON    -- Descuentos
)
BEGIN
    DECLARE _idcliente INT;
    DECLARE _idcolaborador INT;
    DECLARE _idventa INT;
    DECLARE _idproducto INT;
    DECLARE i INT DEFAULT 0;
    DECLARE num_productos INT;
    DECLARE _new_numserie CHAR(12);
    DECLARE _precio DECIMAL(7,2);
    DECLARE _cantidad INT;
    DECLARE _descuento DECIMAL(5,2);

    -- Generación automática del número de serie si no se envía uno
    IF _numserie IS NULL THEN
        SET _new_numserie = (SELECT CONCAT('V', LPAD(COALESCE(MAX(CAST(SUBSTRING(numserie, 2) AS UNSIGNED)), 0) + 1, 5, '0'))
                             FROM ventas);
    ELSE
        SET _new_numserie = _numserie;
    END IF;

    -- Obtener el idcliente según el nombre del cliente
    SELECT idcliente INTO _idcliente FROM clientes WHERE nombres = _nomcliente LIMIT 1;

    -- Si no se encuentra el cliente, generar un error
    IF _idcliente IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cliente no encontrado';
    END IF;

    -- Asignar un idcolaborador (esto debe ser dinámico según el usuario autenticado)
    SET _idcolaborador = 1;  -- Asumiendo que el colaborador es el ID 1 por defecto.

    -- Insertar en la tabla ventas
    INSERT INTO ventas (idcliente, idcolaborador, tipocom, numserie, numcom, fechahora, moneda)
    VALUES (_idcliente, _idcolaborador, _tipo, _new_numserie, _numcomprobante, _fecha, _tipomoneda);

    -- Obtener el id de la venta insertada
    SET _idventa = LAST_INSERT_ID();

    -- Calcular el número de productos en el JSON
    SET num_productos = JSON_LENGTH(_productos);

    -- Insertar los productos en la tabla detalleventa
    WHILE i < num_productos DO
        -- Obtener el id del producto desde el nombre (buscando en la tabla productos)
        SET _idproducto = (SELECT idproducto FROM productos WHERE descripcion = JSON_UNQUOTE(JSON_EXTRACT(_productos, CONCAT('$[', i, ']'))) LIMIT 1);

        -- Si no se encuentra el producto, generar un error
        IF _idproducto IS NULL THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Producto no encontrado';
        END IF;

        -- Obtener el precio, cantidad y descuento desde el JSON
        SET _precio = CAST(JSON_UNQUOTE(JSON_EXTRACT(_precio, CONCAT('$[', i, ']'))) AS DECIMAL(7,2)); 
        SET _cantidad = CAST(JSON_UNQUOTE(JSON_EXTRACT(_cantidad, CONCAT('$[', i, ']'))) AS UNSIGNED); 
        SET _descuento = CAST(JSON_UNQUOTE(JSON_EXTRACT(_descuento, CONCAT('$[', i, ']'))) AS DECIMAL(5,2));

        -- Insertar en la tabla detalleventa
        INSERT INTO detalleventa (idventa, idproducto, cantidad, precioventa, descuento, numserie)
        VALUES (_idventa, _idproducto, _cantidad, _precio, _descuento, _new_numserie);

        -- Incrementar el índice
        SET i = i + 1;
    END WHILE;

END $$

DELIMITER ;

DESCRIBE clientes;


SHOW PROCEDURE STATUS WHERE Db = 'dbfix360';

SHOW PROCEDURE STATUS WHERE Name = 'spRegistroVentas';









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

