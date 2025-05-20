-- Test movements SQL for dbfix360
USE dbfix360;


DELIMITER $$
-- call fetchKilometraje(1)
-- select * from kardex;
-- select * from movimientos;
-- Stored procedure to generate test kardex and movimientos for all products
CREATE PROCEDURE test_movimientos()
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE pid INT;
  DECLARE tk INT;
  DECLARE tm_compra INT;
  DECLARE tm_venta INT;
  DECLARE tm_devol INT;
  DECLARE curs CURSOR FOR SELECT idproducto FROM productos;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  -- Get movement type IDs
  SELECT idtipomov INTO tm_compra FROM tipomovimientos WHERE flujo='entrada' AND tipomov='compra' LIMIT 1;
  SELECT idtipomov INTO tm_venta  FROM tipomovimientos WHERE flujo='salida'  AND tipomov='venta' LIMIT 1;
  SELECT idtipomov INTO tm_devol  FROM tipomovimientos WHERE flujo='entrada' AND tipomov='devolucion' LIMIT 1;

  OPEN curs;
  read_loop: LOOP
    FETCH curs INTO pid;
    IF done THEN LEAVE read_loop; END IF;

    -- Create kardex entry
    INSERT INTO kardex (idproducto, fecha, stockmin, stockmax)
    VALUES (pid, '2025-05-01', 0, 100);
    SET tk = LAST_INSERT_ID();

    -- Insert three movements: purchase, sale, purchase
    INSERT INTO movimientos (idkardex, idtipomov, fecha, cantidad, saldorestante)
      VALUES (tk, tm_compra, '2025-05-02', 20, 20);
    INSERT INTO movimientos (idkardex, idtipomov, fecha, cantidad, saldorestante)
      VALUES (tk, tm_venta,  '2025-05-03',  5, 15);
    INSERT INTO movimientos (idkardex, idtipomov, fecha, cantidad, saldorestante)
      VALUES (tk, tm_compra, '2025-05-04', 10, 25);
  END LOOP;
  CLOSE curs;
END$$

DELIMITER ;
-- select * from movimientos
-- Call the procedure and then drop it
CALL test_movimientos();
DROP PROCEDURE IF EXISTS test_movimientos;

SELECT * FROM movimientos;



CREATE OR REPLACE VIEW v_stock_actual AS
SELECT
  k.idproducto,
  COALESCE(
    (
      SELECT m.saldorestante
      FROM movimientos AS m
      WHERE m.idkardex = k.idkardex
      ORDER BY m.idmovimiento DESC
      LIMIT 1
    ),
    k.stockmin
  ) AS stock_actual
FROM kardex AS k;

SELECT * FROM v_stock_actual;



DELIMITER $$
-- select * from movimientos where idkardex = 52
-- select * from kardex where idproducto = 52
-- call spStockActualPorProducto(52)
-- 2) SP para traer el stock actual de un producto
/*
delimiter $$
CREATE PROCEDURE spStockActualPorProducto(
  IN _idproducto INT
)
BEGIN
  DECLARE _idkardex INT;

  -- Buscamos la fila de kardex correspondiente
  SELECT idkardex
    INTO _idkardex
    FROM kardex
   WHERE idproducto = _idproducto
   LIMIT 1;

  IF _idkardex IS NULL THEN
    -- Si no existe kardex para ese producto
    SELECT NULL AS stock_actual;
  ELSE
    -- Último saldo de movimientos o, si no hay, stockmin
    SELECT COALESCE(
      ( SELECT m.saldorestante
          FROM movimientos AS m
         WHERE m.idkardex = _idkardex
         ORDER BY m.idmovimiento DESC
         LIMIT 1
      ),
      ( SELECT k2.stockmin
          FROM kardex AS k2
         WHERE k2.idkardex = _idkardex
      )
    ) AS stock_actual;
  END IF;
END$$
*/
-- select * from movimientos where idmovimiento = 113;
-- CALL spMovimientosPorProducto(42);
-- select * from productos where idproducto = 42
-- CALL spStockActualPorProducto(42);

-- select * from kardex where idproducto = 1;
-- select * from productos;


DROP PROCEDURE IF EXISTS spStockActualPorProducto;
DELIMITER $$
CREATE PROCEDURE spStockActualPorProducto(
  IN _idproducto INT
)
BEGIN
  DECLARE _idkardex INT;

  -- 1) Buscamos el kardex asociado a ese producto
  SELECT idkardex
    INTO _idkardex
    FROM kardex
   WHERE idproducto = _idproducto
   LIMIT 1;

  -- 2) Si no hay kardex, devolvemos NULLs en los tres campos
  IF _idkardex IS NULL THEN
    SELECT 
      NULL AS stock_actual,
      NULL AS stockmin,
      NULL AS stockmax;
  ELSE
    -- 3) Si existe kardex, devolvemos stockmin/stockmax y calculamos stock_actual
    SELECT
      COALESCE(
        ( SELECT m.saldorestante
            FROM movimientos AS m
           WHERE m.idkardex = _idkardex
           ORDER BY m.idmovimiento DESC
           LIMIT 1
        ),
        k.stockmin     -- si no hay movimientos, usamos stockmin
      )                  AS stock_actual,
      k.stockmin,
      k.stockmax
    FROM kardex AS k
    WHERE k.idkardex = _idkardex;
  END IF;
END$$

CALL buscar_producto('s')
DELIMITER $$

DROP PROCEDURE IF EXISTS buscar_producto $$
DELIMITER $$
CREATE PROCEDURE buscar_producto(
    IN in_termino_busqueda VARCHAR(255)
)
BEGIN
    SELECT
        P.idproducto,
        CONCAT(S.subcategoria, ' ', P.descripcion) AS subcategoria_producto,
        P.precio,
        (
            SELECT COALESCE(
                SUM(
                    CASE 
                        WHEN tm.flujo = 'entrada' THEN m.cantidad
                        WHEN tm.flujo = 'salida'  THEN -m.cantidad
                        ELSE 0
                    END
                ), 
                0
            )
            FROM movimientos AS m
            JOIN tipomovimientos AS tm ON m.idtipomov = tm.idtipomov
            WHERE m.idkardex = k.idkardex
        ) AS stock
    FROM productos     AS P
    JOIN subcategorias AS S ON P.idsubcategoria = S.idsubcategoria
    JOIN kardex        AS k ON P.idproducto     = k.idproducto
    WHERE S.subcategoria LIKE CONCAT('%', in_termino_busqueda, '%')
       OR P.descripcion   LIKE CONCAT('%', in_termino_busqueda, '%')
    LIMIT 10;
END $$

DELIMITER ;

-- Orden de servicio y venta

-- 1) Orden de servicio (sin fecharecordatorio)
DROP PROCEDURE IF EXISTS spRegisterOrdenServicio;
DELIMITER $$
CREATE PROCEDURE spRegisterOrdenServicio (
  IN _idadmin        INT,
  IN _idpropietario  INT,
  IN _idcliente      INT,
  IN _idvehiculo     INT,
  IN _kilometraje    DECIMAL(10,2),
  IN _observaciones  VARCHAR(255),
  IN _ingresogrua    BOOLEAN,
  IN _fechaingreso   DATETIME
)
BEGIN
  INSERT INTO ordenservicios (
    idadmin,
    idpropietario,
    idcliente,
    idvehiculo,
    kilometraje,
    observaciones,
    ingresogrua,
    fechaingreso
  )
  VALUES (
    _idadmin,
    _idpropietario,
    _idcliente,
    _idvehiculo,
    _kilometraje,
    NULLIF(_observaciones, ''),
    _ingresogrua,
    _fechaingreso
  );

  SELECT LAST_INSERT_ID() AS idorden;
END$$
DELIMITER ;

-- 2) Venta (y opcionalmente orden)
DROP PROCEDURE IF EXISTS spRegisterVentaConOrden;
DELIMITER $$
CREATE PROCEDURE spRegisterVentaConOrden(
  -- Flag: ¿crear también la orden de servicio?
  IN _conOrden        BOOLEAN,

  -- Parámetros comunes a orden y venta
  IN _idadmin         INT,
  IN _idpropietario   INT,
  IN _idcliente       INT,
  IN _idvehiculo      INT,
  IN _kilometraje     DECIMAL(10,2),
  IN _observaciones   VARCHAR(255),
  IN _ingresogrua     BOOLEAN,
  IN _p_fechaingreso  DATETIME,      -- puede venir NULL desde la app

  -- Parámetros específicos de la venta
  IN _tipocom         VARCHAR(20),   -- 'boleta' o 'factura'
  IN _fechahora       DATETIME,
  IN _numserie        VARCHAR(30),
  IN _numcom          CHAR(20),
  IN _moneda          VARCHAR(20),
  IN _idcolaborador   INT
)
BEGIN
  DECLARE _idorden    INT DEFAULT NULL;
  DECLARE _idventa    INT DEFAULT NULL;
  DECLARE _fechaingreso DATETIME;

  -- Normalizamos fecha de ingreso usando la de venta si vino NULL
  SET _fechaingreso = COALESCE(_p_fechaingreso, _fechahora);

  -- 1) Crear orden si se solicita
  IF _conOrden THEN
    CALL spRegisterOrdenServicio(
      _idadmin,
      _idpropietario,
      _idcliente,
      _idvehiculo,
      _kilometraje,
      _observaciones,
      _ingresogrua,
      _fechaingreso
    );
    SELECT LAST_INSERT_ID() INTO _idorden;
  END IF;

  -- 2) Insertar la venta (con idpropietario = idorden si existe)
  INSERT INTO ventas(
    idpropietario,
    idcliente,
    idcolaborador,
    idvehiculo,
    tipocom,
    fechahora,
    numserie,
    numcom,
    moneda,
    kilometraje
  ) VALUES (
    COALESCE(_idorden, _idpropietario),
    _idcliente,
    _idcolaborador,
    _idvehiculo,
    _tipocom,
    _fechahora,
    _numserie,
    _numcom,
    _moneda,
    _kilometraje
  );
  SELECT LAST_INSERT_ID() INTO _idventa;

  -- 3) Devolver ambos IDs
  SELECT _idventa AS idventa,
         _idorden AS idorden;
END$$
DELIMITER ;


--
-- 1) Iniciamos transacción para no ensuciar datos
START TRANSACTION;

-- 2) Llamada al SP unificado:
CALL spRegisterVentaConOrden(
  1,                    -- _conOrden         : 1 = sí quiero crear orden
  1,                    -- _idadmin          : id del colaborador que registra
  2,                   -- _idpropietario    : cliente/propietario (puede ser mismo que idcliente)
  2,                   -- _idcliente        : quien paga
  3,                    -- _idvehiculo       : vehículo asociado
  12000.50,             -- _kilometraje      : km actual
  'Cambio de aceite',   -- _observaciones    : texto libre
  0,                    -- _ingresogrua      : 0 = no ingresó en grúa
  NULL,                 -- _fechaingreso     : NULL → tomará la misma que _fechahora
  'boleta',             -- _tipocom          : 'boleta' ó 'factura'
  '2025-05-19 14:30:00',-- _fechahora        : fecha‑hora de la venta
  'B068',               -- _numserie         : serie de comprobante
  'B-3731456',          -- _numcom           : número de comprobante
  'Soles',              -- _moneda           : moneda
  1                    -- _idcolaborador    : quien atiende la venta
);

-- El SP devolverá un result set con:
-- +---------+---------+
-- | idventa | idorden |
-- +---------+---------+
-- |     123 |      45 |
-- +---------+---------+

-- 3) Suponiendo que nos devolvió idventa=123 e idorden=45,
--    insertamos un par de líneas de detalle de venta:
CALL spuInsertDetalleVenta(3,  2,  2, 'SN001,SN002',  150.00,  10.00);
CALL spuInsertDetalleVenta(3, 2,  1, 'SN010',      200.00,   0.00);

-- 4) Y detalle de servicios en la misma orden (si aplica):
CALL spInsertDetalleOrdenServicio(45,  4 /*idservicio*/,  2 /*idmecánico*/, 250.00);

-- 5) Confirmamos
COMMIT;


-- call spRegisterProducto(2,2,'dddddsasd',1,'ssssss','UNDDD',10,null,10,15,100,@newIdProd); SELECT @newIdProd;
-- select * from productos where idproducto = 52;
-- select * from movimientos where idkardex = 52;
-- select * from kardex where idproducto =52;
-- select * from tipomovimientos;
-- insert into tipomovimientos (flujo,tipomov)values ('entrada', 'stock inicial')
DROP PROCEDURE IF EXISTS spRegisterProducto;
DELIMITER $$
CREATE PROCEDURE spRegisterProducto(
  IN  _idsubcategoria INT,
  IN  _idmarca        INT,
  IN  _descripcion    VARCHAR(50),
  IN  _precio         DECIMAL(7,2),
  IN  _presentacion   VARCHAR(40),
  IN  _undmedida      VARCHAR(40),
  IN  _cantidad       DECIMAL(10,2),  -- sólo para presentacion
  IN  _img            VARCHAR(255),
  IN  _stockInicial   INT,            -- NUEVO: stock real inicial
  IN  _stockmin       INT,
  IN  _stockmax       INT,            -- puede ser NULL
  OUT _idproducto     INT
)
BEGIN
  DECLARE _idkardex   INT;
  DECLARE _idtipomov  INT;

  -- 1) Inserto el producto (cantidad = presentacion)
  INSERT INTO productos 
    (idsubcategoria, idmarca, descripcion, precio, presentacion, undmedida, cantidad, img)
  VALUES 
    (_idsubcategoria,
     _idmarca,
     _descripcion,
     _precio,
     _presentacion,
     _undmedida,
     _cantidad,
     NULLIF(_img, '')
    );

  SET _idproducto = LAST_INSERT_ID();

  -- 2) Creo el kardex con los umbrales
  INSERT INTO kardex
    (idproducto, fecha, stockmin, stockmax)
  VALUES
    (_idproducto,
     CURDATE(),
     _stockmin,
     NULLIF(_stockmax,'')
    );
  SET _idkardex = LAST_INSERT_ID();

  -- 3) Obtengo un tipo de movimiento de ENTRADA
  SELECT idtipomov
    INTO _idtipomov
  FROM tipomovimientos
  WHERE flujo = 'entrada' AND tipomov = 'stock inicial'
  ORDER BY idtipomov
  LIMIT 1;

  -- 4) Registro el movimiento inicial con el stock real
  INSERT INTO movimientos
    (idkardex, idtipomov, fecha, cantidad, saldorestante)
  VALUES
    (_idkardex,
     _idtipomov,
     CURDATE(),
     _stockInicial,
     _stockInicial
    );

END$$
DELIMITER ;



-- 1) Eliminar viejo SP si existiera

DROP PROCEDURE IF EXISTS spUpdateProducto;
DELIMITER $$
CREATE PROCEDURE spUpdateProducto(
  IN  _idproducto   INT,
  IN  _descripcion  VARCHAR(50),
  IN  _cantidad     DECIMAL(10,2),
  IN  _precio       DECIMAL(7,2),
  IN  _img          VARCHAR(255),    -- ruta o '' para no cambiar
  IN  _stockmin     INT,
  IN  _stockmax     INT               -- puede venir NULL para no cambiar
)
BEGIN
  -- 0) Validaciones de rangos
  IF _cantidad < 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: La cantidad no puede ser negativa';
  END IF;

  IF _precio < 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: El precio no puede ser negativo';
  END IF;

  IF _stockmin < 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: stockmin no puede ser negativo';
  END IF;

  IF _stockmax IS NOT NULL AND _stockmax < 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: stockmax no puede ser negativo';
  END IF;

  IF _stockmax IS NOT NULL AND _stockmax < _stockmin THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: stockmax debe ser mayor o igual a stockmin';
  END IF;

  -- 1) Verificar que el producto exista
  IF NOT EXISTS (SELECT 1 FROM productos WHERE idproducto = _idproducto) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spUpdateProducto: Producto no existe';
  END IF;

  -- 2) Actualizar la tabla productos
  UPDATE productos
  SET
    descripcion = _descripcion,
    cantidad    = _cantidad,
    precio      = _precio,
    -- Solo actualizar imagen si se envía un valor no vacío
    img         = CASE 
                    WHEN TRIM(_img) <> '' THEN _img 
                    ELSE img 
                  END
  WHERE idproducto = _idproducto;

  -- 3) Actualizar la tabla kardex asociada
  UPDATE kardex
  SET
    stockmin = _stockmin,
    -- Solo actualizar stockmax si no es NULL
    stockmax = CASE 
                 WHEN _stockmax IS NOT NULL THEN _stockmax 
                 ELSE stockmax 
               END
  WHERE idproducto = _idproducto;
END$$


DELIMITER ;


CALL spUpdateProducto(
  53,               -- _idproducto
  'Nueva descripción del producto',
  24,               -- _cantidad (presentación)
  15.75,            -- _precio
  'ruta/nueva.jpg', -- _img (o '' para dejar la misma)
  5,                -- _stockmin
  100               -- _stockmax (o NULL para no cambiar)
);

DROP PROCEDURE IF EXISTS spGetProductoById;
DELIMITER $$
CREATE PROCEDURE spGetProductoById(
  IN  _idproducto   INT
)
BEGIN
  -- 1) Verificar que el producto existe
  IF NOT EXISTS (SELECT 1 FROM productos WHERE idproducto = _idproducto) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'spGetProductoById: Producto no existe';
  END IF;

  -- 2) Devolver datos de producto + marca, categoría, subcategoría + stocks
  SELECT
    p.idproducto,
    p.idmarca,                  -- PARA el <select> de Marca
    sc.idcategoria,             -- PARA el <select> de Categoría
    p.idsubcategoria,           -- PARA el <select> de Subcategoría
    p.descripcion,
    p.presentacion,
    p.undmedida,
    p.cantidad       AS cantidad_por_presentacion,
    p.precio,
    p.img,
    k.stockmin,
    k.stockmax,
    COALESCE(
      (
        SELECT m.saldorestante
        FROM movimientos AS m
        WHERE m.idkardex = k.idkardex
        ORDER BY m.idmovimiento DESC
        LIMIT 1
      ), 
      0
    ) AS stock_actual
  FROM productos AS p
  LEFT JOIN kardex AS k
    ON p.idproducto = k.idproducto
  LEFT JOIN subcategorias AS sc
    ON p.idsubcategoria = sc.idsubcategoria
  WHERE p.idproducto = _idproducto;
END$$

DELIMITER ;

-- select * from ordenservicios;