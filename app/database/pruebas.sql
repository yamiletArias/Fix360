-- Test movements SQL for dbfix360
USE dbfix360;

-- select * from productos;
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



DELIMITER $$

CREATE OR REPLACE PROCEDURE seed_movimientos(
  IN minMov INT,
  IN maxMov INT,
  IN fechaInicio DATE,
  IN fechaFin   DATE
)
BEGIN
  DECLARE done         INT DEFAULT FALSE;
  DECLARE p_idproducto INT;
  DECLARE p_idkardex    INT;
  DECLARE nMov          INT;
  DECLARE i             INT;
  DECLARE rndTipo       INT;
  DECLARE rndCant       INT;
  DECLARE rndPU         DECIMAL(10,2);
  DECLARE rndFecha      DATE;
  DECLARE saldo         INT;

  DECLARE curProds CURSOR FOR
    SELECT idproducto FROM productos;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  OPEN curProds;
  prod_loop: LOOP
    FETCH curProds INTO p_idproducto;
    IF done THEN LEAVE prod_loop; END IF;

    -- Obtenemos el registro de kardex
    SELECT idkardex
      INTO p_idkardex
      FROM kardex
     WHERE idproducto = p_idproducto
     LIMIT 1;

    SET saldo = 0;
    SET nMov = FLOOR(RAND() * (maxMov - minMov + 1)) + minMov;
    SET i = 0;

    mov_loop: WHILE i < nMov DO
      -- 1) Fecha aleatoria
      SET rndFecha = DATE_ADD(
        fechaInicio,
        INTERVAL FLOOR(RAND() * DATEDIFF(fechaFin, fechaInicio) + 1) DAY
      );
      -- 2) Tipo aleatorio
      SET rndTipo = IF(RAND() < 0.5, 1, 2);
      -- Si es una salida pero saldo es 0, la convertimos en entrada
      IF rndTipo = 2 AND saldo = 0 THEN
        SET rndTipo = 1;
      END IF;
      -- 3) Cantidad aleatoria
      SET rndCant = FLOOR(RAND() * 50) + 1;
      -- 4) Precio unitario
      SET rndPU = ROUND(RAND() * 99 + 1, 2);

      -- Ajuste: si salida y rndCant > saldo, recortamos a saldo
      IF rndTipo = 2 AND rndCant > saldo THEN
        SET rndCant = saldo;
      END IF;

      -- Recalculamos saldo
      IF rndTipo = 1 THEN
        SET saldo = saldo + rndCant;
      ELSE
        SET saldo = saldo - rndCant;
      END IF;

      -- Insertamos movimiento
      INSERT INTO movimientos
        (idkardex, idtipomov, fecha, cantidad, preciounit, saldorestante)
      VALUES
        (p_idkardex, rndTipo, rndFecha, rndCant, rndPU, saldo);

      SET i = i + 1;
    END WHILE mov_loop;

  END LOOP prod_loop;

  CLOSE curProds;

  SELECT CONCAT('Seed completado para ', COUNT(*), ' productos') AS resultado
    FROM productos;
END$$

DELIMITER ;


-- Restauramos el delimitador
DELIMITER ;
CALL seed_movimientos(5, 20, '2024-01-01', CURDATE());
-- select * from movimientos where idkardex = 1;
-- select * from productos where idproducto = 57
-- select * from kardex where idkardex = 67;
-- select * from ordenservicios;
-- select * from tipocombustibles
-- select * from productos where codigobarra = 'S8M0PH038476JTY'
-- select * from egresos
-- SP 1: Datos generales del vehículo
-- call spGetDatosGeneralesVehiculo(1)
DROP PROCEDURE IF EXISTS spGetDatosGeneralesVehiculo;
DELIMITER $$
CREATE PROCEDURE spGetDatosGeneralesVehiculo(
    IN _idvehiculo INT
)
BEGIN
    SELECT
      v.idvehiculo,
      v.placa,
      v.anio,
      v.color,
      v.numserie,
      v.vin,
      tv.tipov AS tipo_vehiculo,
      tc.tcombustible,
      m.nombre AS marca,
      mo.modelo,
      c.idcliente AS id_propietario,
      -- Propietario actual (persona o empresa)
      CASE
        WHEN p.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
        ELSE e.nomcomercial
      END AS propietario,
      COALESCE(p.numdoc, e.ruc) AS documento_propietario,
      pr.fechainicio AS propiedad_desde,
      pr.fechafinal  AS propiedad_hasta
    FROM vehiculos v
    JOIN modelos mo       ON mo.idmodelo = v.idmodelo
    JOIN marcas m         ON mo.idmarca   = m.idmarca
    JOIN tipovehiculos tv ON tv.idtipov   = mo.idtipov
    JOIN tipocombustibles tc ON tc.idtcombustible = v.idtcombustible
    LEFT JOIN propietarios pr ON pr.idvehiculo = v.idvehiculo
                               AND (pr.fechafinal IS NULL OR pr.fechafinal >= CURRENT_DATE)
    LEFT JOIN clientes c      ON c.idcliente = pr.idcliente
    LEFT JOIN personas p      ON p.idpersona = c.idpersona
    LEFT JOIN empresas e      ON e.idempresa = c.idempresa
    WHERE v.idvehiculo = _idvehiculo;
END $$
DELIMITER ;
-- call spListOrdenesPorVehiculo(1)
-- SP 2: Listado de órdenes de servicio por vehículo
DROP PROCEDURE IF EXISTS spListOrdenesPorVehiculo;
DELIMITER $$
CREATE PROCEDURE spListOrdenesPorVehiculo(
    IN in_idvehiculo INT
)
BEGIN
    SELECT
      o.idorden,
      o.fechaingreso,
      o.fechasalida,
      o.kilometraje,
      o.ingresogrua,
      o.estado,
      o.observaciones,
      col.namuser AS tecnico,
      -- total de mano de obra y repuestos (se asume cantidad = 1 por registro)
      SUM(CASE WHEN srv.servicio LIKE '%mano%' THEN dos.precio ELSE 0 END) AS total_mano_obra,
      SUM(CASE WHEN srv.servicio NOT LIKE '%mano%' THEN dos.precio ELSE 0 END) AS total_repuestos
    FROM ordenservicios o
    JOIN detalleordenservicios dos ON dos.idorden = o.idorden
    JOIN servicios srv             ON srv.idservicio = dos.idservicio
    JOIN colaboradores col         ON col.idcolaborador = dos.idmecanico
    WHERE o.idvehiculo = in_idvehiculo
    GROUP BY
      o.idorden, o.fechaingreso, o.fechasalida,
      o.kilometraje, o.ingresogrua, o.estado,
      o.observaciones, col.namuser;
END $$
-- call spListVentasPorVehiculo(2)
-- SP 3: Listado de ventas por vehículo
DROP PROCEDURE IF EXISTS spListVentasPorVehiculo;
DELIMITER $$
CREATE PROCEDURE spListVentasPorVehiculo(
    IN in_idvehiculo INT
)
BEGIN
    SELECT
      v.idventa,
      v.fechahora,
      v.tipocom,
      CONCAT(v.numserie, '-', v.numcom) AS comprobante,
      v.moneda,
      v.kilometraje,
      col.namuser AS vendedor,
      SUM(dv.precioventa * dv.cantidad * (1 - dv.descuento/100)) AS total_neto,
      COUNT(DISTINCT dv.idproducto) AS items_vendidos
    FROM ventas v
    JOIN detalleventa dv ON dv.idventa = v.idventa
    JOIN colaboradores col ON col.idcolaborador = v.idcolaborador
    WHERE v.idvehiculo = in_idvehiculo
    GROUP BY
      v.idventa, v.fechahora, v.tipocom,
      v.numserie, v.numcom, v.moneda,
      v.kilometraje, col.namuser;
END $$
DELIMITER ;
-- call spGetDatosGeneralesVehiculo(1)
-- SP 1: Datos generales del vehículo
DROP PROCEDURE IF EXISTS spGetDatosGeneralesVehiculo;
DELIMITER $$
CREATE PROCEDURE spGetDatosGeneralesVehiculo(
    IN in_idvehiculo INT
)
BEGIN
    SELECT
      v.idvehiculo,
      v.placa,
      v.anio,
      v.color,
      v.numserie,
      v.vin,
      tv.tipov AS tipo_vehiculo,
      tc.tcombustible,
      m.nombre AS marca,
      mo.modelo,
      c.idcliente AS id_propietario,
      -- Propietario actual (persona o empresa)
      CASE
        WHEN p.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
        ELSE e.nomcomercial
      END AS propietario,
      COALESCE(p.numdoc, e.ruc) AS documento_propietario,
      pr.fechainicio AS propiedad_desde,
      pr.fechafinal  AS propiedad_hasta
    FROM vehiculos v
    JOIN modelos mo       ON mo.idmodelo = v.idmodelo
    JOIN marcas m         ON mo.idmarca   = m.idmarca
    JOIN tipovehiculos tv ON tv.idtipov   = mo.idtipov
    JOIN tipocombustibles tc ON tc.idtcombustible = v.idtcombustible
    LEFT JOIN propietarios pr ON pr.idvehiculo = v.idvehiculo
                               AND (pr.fechafinal IS NULL OR pr.fechafinal >= CURRENT_DATE)
    LEFT JOIN clientes c      ON c.idcliente = pr.idcliente
    LEFT JOIN personas p      ON p.idpersona = c.idpersona
    LEFT JOIN empresas e      ON e.idempresa = c.idempresa;
END $$
DELIMITER ;

-- SP 2: Listado de órdenes de servicio por vehículo
DROP PROCEDURE IF EXISTS spListOrdenesPorVehiculo;
DELIMITER $$
CREATE PROCEDURE spListOrdenesPorVehiculo(
    IN in_idvehiculo INT
)
BEGIN
    SELECT
      o.idorden,
      o.fechaingreso,
      o.fechasalida,
      o.kilometraje,
      o.ingresogrua,
      o.estado,
      o.observaciones,
      col.namuser AS tecnico,
      -- total de mano de obra y repuestos (se asume cantidad = 1 por registro)
      SUM(CASE WHEN srv.servicio LIKE '%mano%' THEN dos.precio ELSE 0 END) AS total_mano_obra,
      SUM(CASE WHEN srv.servicio NOT LIKE '%mano%' THEN dos.precio ELSE 0 END) AS total_repuestos
    FROM ordenservicios o
    JOIN detalleordenservicios dos ON dos.idorden = o.idorden
    JOIN servicios srv             ON srv.idservicio = dos.idservicio
    JOIN colaboradores col         ON col.idcolaborador = dos.idmecanico
    WHERE o.idvehiculo = in_idvehiculo
    GROUP BY
      o.idorden, o.fechaingreso, o.fechasalida,
      o.kilometraje, o.ingresogrua, o.estado,
      o.observaciones, col.namuser;
END $$
DELIMITER ;
-- call spListVentasPorVehiculo(5)
-- call spListVentasPorVehiculo(1)
-- SP 3: Listado de ventas por vehículo
-- call spListVentasPorVehiculo(1)
DROP PROCEDURE IF EXISTS spListVentasPorVehiculo;
DELIMITER $$
CREATE PROCEDURE spListVentasPorVehiculo(
    IN in_idvehiculo INT
)
BEGIN
    SELECT
      v.idventa,
      v.fechahora,
      v.tipocom,
      CONCAT(v.numserie, '-', v.numcom) AS comprobante,
      v.moneda,
      v.kilometraje,
      col.namuser AS vendedor,
      -- Nombre del propietario (persona o empresa)
      CASE
        WHEN p.idpersona IS NOT NULL THEN CONCAT(p.nombres, ' ', p.apellidos)
        ELSE e.nomcomercial
      END AS propietario,
      SUM(dv.precioventa * dv.cantidad * (1 - dv.descuento/100)) AS total_neto,
      COUNT(DISTINCT dv.idproducto) AS items_vendidos
    FROM ventas v
    JOIN detalleventa dv     ON dv.idventa = v.idventa
    JOIN colaboradores col   ON col.idcolaborador = v.idcolaborador
    JOIN clientes c          ON c.idcliente = v.idpropietario
    LEFT JOIN personas p     ON p.idpersona = c.idpersona
    LEFT JOIN empresas e     ON e.idempresa = c.idempresa
    WHERE v.idvehiculo = in_idvehiculo
    GROUP BY
      v.idventa, v.fechahora, v.tipocom,
      v.numserie, v.numcom, v.moneda,
      v.kilometraje, col.namuser,
      propietario;
END $$

-- *** Datos de prueba para vehículo ID = 1 ***
-- 5 órdenes de servicio con su detalle
INSERT INTO ordenservicios (idadmin, idpropietario, idcliente, idvehiculo, kilometraje, ingresogrua, fechaingreso, fechasalida, estado, observaciones)
VALUES
  (1, 1, 1, 1, 15000.00, FALSE, '2025-05-05 08:30:00', '2025-05-05 12:00:00', 'A', 'Cambio de aceite y filtro'),
  (2, 1, 1, 1, 15250.50, FALSE, '2025-05-10 09:15:00', '2025-05-10 11:45:00', 'A', 'Inspección general'),
  (3, 1, 1, 1, 15500.75, TRUE,  '2025-05-15 14:00:00', '2025-05-15 17:30:00', 'A', 'Revisión de frenos y rótulas'),
  (2, 1, 1, 1, 15720.00, FALSE, '2025-05-20 10:00:00', '2025-05-20 13:15:00', 'A', 'Alineación y balanceo'),
  (1, 1, 1, 1, 16000.20, FALSE, '2025-05-25 08:45:00', '2025-05-25 12:30:00', 'A', 'Cambio de bujías');

-- Obtener los últimos 5 IDs de ordenservicios para detalle
SET @o1 = (SELECT MAX(idorden) - 4 FROM ordenservicios);

INSERT INTO detalleordenservicios (idorden, idmecanico, idservicio, precio, estado)
VALUES
  (@o1,       1, (SELECT idservicio FROM servicios WHERE servicio = 'Cambio de aceite de motor' LIMIT 1), 48.50, 'A'),
  (@o1,       1, (SELECT idservicio FROM servicios WHERE servicio = 'Cambio de filtro de aceite' LIMIT 1),    13.90, 'A'),
  (@o1+1,     2, (SELECT idservicio FROM servicios WHERE servicio = 'Inspección general del vehículo' LIMIT 1),  20.00, 'A'),
  (@o1+2,     3, (SELECT idservicio FROM servicios WHERE servicio = 'Revisión de rótulas y terminales' LIMIT 1), 35.00, 'A'),
  (@o1+2,     3, (SELECT idservicio FROM servicios WHERE servicio = 'Cambio de amortiguadores' LIMIT 1),         80.00, 'A'),
  (@o1+3,     2, (SELECT idservicio FROM servicios WHERE servicio = 'Alineación y balanceo' LIMIT 1),        60.00, 'A'),
  (@o1+4,     1, (SELECT idservicio FROM servicios WHERE servicio = 'Cambio de bujías' LIMIT 1),             19.90, 'A');

-- 5 ventas con su detalle
INSERT INTO ventas (idpropietario, idcliente, idcolaborador, idvehiculo, tipocom, numserie, numcom, moneda, kilometraje, estado)
VALUES
  (1, 1, 1, 1, 'boleta', 'B001', '0001', 'PEN', 16010.00, TRUE),
  (1, 1, 2, 1, 'factura', 'F001', '1001', 'PEN', 16200.35, TRUE),
  (1, 1, 3, 1, 'boleta', 'B002', '0002', 'PEN', 16500.00, TRUE),
  (1, 1, 2, 1, 'boleta', 'B003', '0003', 'PEN', 16750.10, TRUE),
  (1, 1, 1, 1, 'factura', 'F002', '1002', 'PEN', 17000.50, TRUE);

-- Obtener los últimos 5 IDs de ventas para detalle
SET @v1 = (SELECT MAX(idventa) - 4 FROM ventas);

INSERT INTO detalleventa (idventa, idproducto, cantidad, numserie, precioventa, descuento)
VALUES
  (@v1,   1, 2, JSON_ARRAY('','',''), 48.50, 0),
  (@v1+1, 2, 1, JSON_ARRAY(''),        55.00, 5),
  (@v1+2, 3, 4, JSON_ARRAY('','','',''), 42.90, 10),
  (@v1+3, 4, 1, JSON_ARRAY(''),        79.90, 0),
  (@v1+4, 5, 2, JSON_ARRAY('',''),     35.00, 0);
