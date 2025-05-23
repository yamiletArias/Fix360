-- Test movements SQL for dbfix360
USE dbfix360;


DELIMITER $$
-- call fetchKilometraje(1)
-- select * from kardex;
-- select * from movimientos ;
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
-- select * from produc
DELIMITER ;
-- select * from movimientos
-- Call the procedure and then drop it
CALL test_movimientos();
DROP PROCEDURE IF EXISTS test_movimientos;

SELECT * FROM movimientos;


/*
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

-- SELECT * FROM v_stock_actual;


*/
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

-- CALL buscar_producto('s')
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
/*
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
/*
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
-- 5) Confirm
-- call spRegisterProducto(2,2,'dddddsasd',1,'ssssss','UNDDD',10,null,10,15,100,@newIdProd); SELECT @newIdProd;
-- select * from productos where idproducto = 52;
-- select * from movimientos where idkardex = 52;
-- select * from kardex where idproducto =52;
-- select * from tipomovimientos;
-- insert into tipomovimientos (flujo,tipomov)values ('entrada', 'stock inicial')

-- 1) Eliminar viejo SP si existiera
/*
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



DROP PROCEDURE IF EXISTS spRegisterColaboradorCompleto;
DELIMITER $$
CREATE PROCEDURE spRegisterColaboradorCompleto(
  -- Datos de la persona
  IN  p_nombres        VARCHAR(50),
  IN  p_apellidos      VARCHAR(50),
  IN  p_tipodoc        VARCHAR(30),
  IN  p_numdoc         CHAR(20),
  IN  p_numruc         CHAR(11),
  IN  p_direccion      VARCHAR(70),
  IN  p_correo         VARCHAR(100),
  IN  p_telprincipal   VARCHAR(20),
  IN  p_telalternativo VARCHAR(20),

  -- Datos del contrato
  IN  p_idrol          INT,
  IN  p_fechainicio    DATE,
  IN  p_fechafin       DATE,       -- puede ser NULL

  -- Credenciales del colaborador
  IN  p_namuser        VARCHAR(50),
  IN  p_passuser       VARCHAR(255)
)
BEGIN
  DECLARE v_idpersona   INT;
  DECLARE v_idcontrato  INT;
  DECLARE v_hashed      VARCHAR(64);
  DECLARE v_idcolaborador INT;

  -- 1) insertar persona
  INSERT INTO personas
    (nombres, apellidos, tipodoc, numdoc,
     numruc, direccion, correo,
     telprincipal, telalternativo)
  VALUES
    (p_nombres, p_apellidos, p_tipodoc, p_numdoc,
     NULLIF(p_numruc,''), NULLIF(p_direccion,''), NULLIF(p_correo,''),
     p_telprincipal, NULLIF(p_telalternativo,''));
  SET v_idpersona = LAST_INSERT_ID();

  -- 2) insertar contrato
  INSERT INTO contratos
    (idrol, idpersona, fechainicio, fechafin)
  VALUES
    (p_idrol, v_idpersona, p_fechainicio, p_fechafin);
  SET v_idcontrato = LAST_INSERT_ID();

  -- 3) crear colaborador
  SET v_hashed = SHA2(p_passuser,256);
  INSERT INTO colaboradores
    (idcontrato, namuser, passuser, estado)
  VALUES
    (v_idcontrato, p_namuser, v_hashed, TRUE);
  SET v_idcolaborador = LAST_INSERT_ID();

  -- 4) devolver el id del colaborador
  SELECT v_idcolaborador AS idcolaborador;
END $$
DELIMITER ;

*/
-- CALL spGetHistorialVehiculo(1)
DROP PROCEDURE IF EXISTS spGetHistorialVehiculo;
DELIMITER $$
CREATE PROCEDURE spGetHistorialVehiculo (
  IN _idvehiculo INT
)
BEGIN
  SELECT 
    -- Datos del vehículo
    v.idvehiculo,
    v.placa,
    CONCAT(tv.tipov, ' ', ma.nombre, ' ', m.modelo) AS vehiculo,
    -- Propietarios
    p.fechainicio    AS fecha_evento,
    p.fechafinal     AS fecha_fin_evento,
    'Propietario'    AS tipo_evento,
    COALESCE(pe.apellidos, em.nomcomercial) AS descripcion,
    NULL             AS detalle_extra,
    NULL             AS monto
  FROM propietarios p
    JOIN clientes cprop   ON p.idcliente   = cprop.idcliente
    LEFT JOIN personas pe ON cprop.idpersona = pe.idpersona
    LEFT JOIN empresas em ON cprop.idempresa = em.idempresa
    JOIN vehiculos v       ON p.idvehiculo = v.idvehiculo
    JOIN modelos m         ON v.idmodelo   = m.idmodelo
    JOIN marcas ma         ON m.idmarca    = ma.idmarca
    JOIN tipovehiculos tv  ON m.idtipov    = tv.idtipov
  WHERE v.idvehiculo = _idvehiculo

  UNION ALL

  SELECT 
    v.idvehiculo,
    v.placa,
    CONCAT(tv.tipov, ' ', ma.nombre, ' ', m.modelo),
    o.fechaingreso      AS fecha_evento,
    o.fechasalida       AS fecha_fin_evento,
    'Orden de Servicio' AS tipo_evento,
    o.observaciones     AS descripcion,
    NULL                AS detalle_extra,
    NULL                AS monto
  FROM ordenservicios o
    JOIN vehiculos v      ON o.idvehiculo = v.idvehiculo
    JOIN modelos m        ON v.idmodelo   = m.idmodelo
    JOIN marcas ma        ON m.idmarca    = ma.idmarca
    JOIN tipovehiculos tv ON m.idtipov    = tv.idtipov
  WHERE v.idvehiculo = _idvehiculo

  UNION ALL

  SELECT 
    v.idvehiculo,
    v.placa,
    CONCAT(tv.tipov, ' ', ma.nombre, ' ', m.modelo),
     ven.fechahora        AS fecha_evento,
    NULL                AS fecha_fin_evento,
    'Venta'             AS tipo_evento,
    CONCAT(ven.tipocom,' ',ven.numserie,'-',ven.numcom) AS descripcion,
    NULL                AS detalle_extra,
    SUM(dv.cantidad * dv.precioventa * (1 - dv.descuento/100))
                        AS monto
  FROM ventas ven
    JOIN detalleventa dv  ON ven.idventa = dv.idventa
    JOIN vehiculos v      ON ven.idvehiculo = v.idvehiculo
    JOIN modelos m        ON v.idmodelo     = m.idmodelo
    JOIN marcas ma        ON m.idmarca      = ma.idmarca
    JOIN tipovehiculos tv ON m.idtipov      = tv.idtipov
  WHERE v.idvehiculo = _idvehiculo
  GROUP BY ven.idventa

  UNION ALL

  SELECT 
    v.idvehiculo,
    v.placa,
    CONCAT(tv.tipov, ' ', ma.nombre, ' ', m.modelo),
    ag.fchproxvisita    AS fecha_evento,
    NULL                AS fecha_fin_evento,
    'Recordatorio'      AS tipo_evento,
    ag.comentario       AS descripcion,
    ag.estado           AS detalle_extra,
    NULL                AS monto
  FROM agendas ag
    JOIN propietarios p  ON ag.idpropietario = p.idcliente
    JOIN vehiculos v     ON p.idvehiculo     = v.idvehiculo
    JOIN modelos m       ON v.idmodelo       = m.idmodelo
    JOIN marcas ma       ON m.idmarca        = ma.idmarca
    JOIN tipovehiculos tv ON m.idtipov       = tv.idtipov
  WHERE v.idvehiculo = _idvehiculo

  ORDER BY fecha_evento;
END$$
DELIMITER ;

-- select * from ordenservicios;
-- select * from tipocombustibles