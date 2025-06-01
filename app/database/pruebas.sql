-- Test movements SQL for dbfix360
USE dbfix360;
-- select * from personas;
-- select * from productos;
-- select * from colaboradores;
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

-- select * from personas

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

-- select * from colaboradores;
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

-- call spListOrdenesPorVehiculo(1)
-- SP 2: Listado de órdenes de servicio por vehículo
DELIMITER ;
-- call spGetDatosGeneralesVehiculo(1)
-- SP 1: Datos generales del vehículo


-- call spHistorialOrdenesPorVehiculo('anual','2025-05-28','A',1)
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
  
  
  
  
  
  
  

  USE dbfix360;
DELIMITER $$

DROP PROCEDURE IF EXISTS spGraficoContactabilidadPorPeriodo $$
CREATE PROCEDURE spGraficoContactabilidadPorPeriodo(
    IN p_periodo       ENUM('ANUAL','MENSUAL','SEMANAL'),
    IN p_fecha_desde   DATE,
    IN p_fecha_hasta   DATE
)
BEGIN
    /*
      Este SP devuelve, para el rango de fechas [p_fecha_desde, p_fecha_hasta],
      el conteo de clientes agrupados según p_periodo:
        - 'ANUAL'   → por mes-año (YYYY-MM)
        - 'MENSUAL' → por semana dentro del mes
        - 'SEMANAL' → por día de la semana
      El resultado incluye:
        * periodo_label   (p.ej. '2025-01' si es ANUAL, 'Semana 2' si es MENSUAL, o 'Lunes' si es SEMANAL)
        * contactabilidad (texto de la tabla contactabilidad)
        * total_clientes  (conteo de clientes en ese bucket)
    */

    IF p_periodo = 'ANUAL' THEN
        -- Agrupar por mes/año (YYYY-MM)
        SELECT
            DATE_FORMAT(x.creado_registro, '%Y-%m') AS periodo_label,
            ctb.contactabilidad,
            COUNT(*) AS total_clientes
        FROM (
            /* Clientes que son personas */
            SELECT 
                cli.idcliente,
                cli.idcontactabilidad,
                p.creado AS creado_registro
            FROM clientes cli
            JOIN personas p ON cli.idpersona = p.idpersona
            WHERE p.creado BETWEEN p_fecha_desde AND p_fecha_hasta

            UNION ALL

            /* Clientes que son empresas */
            SELECT 
                cli.idcliente,
                cli.idcontactabilidad,
                e.creado AS creado_registro
            FROM clientes cli
            JOIN empresas e ON cli.idempresa = e.idempresa
            WHERE e.creado BETWEEN p_fecha_desde AND p_fecha_hasta
        ) AS X
        JOIN contactabilidad ctb ON x.idcontactabilidad = ctb.idcontactabilidad
        GROUP BY
            DATE_FORMAT(x.creado_registro, '%Y-%m'),
            ctb.contactabilidad
        ORDER BY
            DATE_FORMAT(x.creado_registro, '%Y-%m'),
            ctb.contactabilidad;

    ELSEIF p_periodo = 'MENSUAL' THEN
        /*
          Para agrupar por “semana” dentro del mes, podemos usar la función WEEK().
          Sin embargo WEEK() nos da el número de semana en el año. 
          Si quieres agrupar estrictamente “Semana 1 del mes”, “Semana 2 del mes”, …, 
          puedes hacer algo como:
            FLOOR((DAYOFMONTH(fecha) - 1)/7) + 1
          Esto divide el mes en bloques de 7 días: 1-7 → Semana 1; 8-14 → Semana 2; etc.
        */
        SELECT
            CONCAT(
               DATE_FORMAT(x.creado_registro, '%Y-%m'), 
               ' - Semana ', 
               FLOOR((DAYOFMONTH(x.creado_registro)-1)/7) + 1
            ) AS periodo_label,
            ctb.contactabilidad,
            COUNT(*) AS total_clientes
        FROM (
            /* Personas */
            SELECT 
                cli.idcliente,
                cli.idcontactabilidad,
                p.creado AS creado_registro
            FROM clientes cli
            JOIN personas p ON cli.idpersona = p.idpersona
            WHERE p.creado BETWEEN p_fecha_desde AND p_fecha_hasta

            UNION ALL

            /* Empresas */
            SELECT 
                cli.idcliente,
                cli.idcontactabilidad,
                e.creado AS creado_registro
            FROM clientes cli
            JOIN empresas e ON cli.idempresa = e.idempresa
            WHERE e.creado BETWEEN p_fecha_desde AND p_fecha_hasta
        ) AS X
        JOIN contactabilidad ctb ON x.idcontactabilidad = ctb.idcontactabilidad
        GROUP BY
            DATE_FORMAT(x.creado_registro, '%Y-%m'),
            FLOOR((DAYOFMONTH(x.creado_registro)-1)/7) + 1,
            ctb.contactabilidad
        ORDER BY
            DATE_FORMAT(x.creado_registro, '%Y-%m'),
            FLOOR((DAYOFMONTH(x.creado_registro)-1)/7) + 1,
            ctb.contactabilidad;

    ELSEIF p_periodo = 'SEMANAL' THEN
        /*
          Para agrupar por día de la semana, podemos usar DAYNAME().
          Esto devolverá 'Monday', 'Tuesday', … en inglés por defecto,
          o bien la versión en el idioma configurado en tu servidor MySQL.
          Si quieres forzar en español, puedes hacer:
             ELT(WEEKDAY(fecha) + 1, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo')
        */
        SELECT
            ELT(
              WEEKDAY(x.creado_registro) + 1,
              'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'
            ) AS periodo_label,
            ctb.contactabilidad,
            COUNT(*) AS total_clientes
        FROM (
            /* Personas */
            SELECT 
                cli.idcliente,
                cli.idcontactabilidad,
                p.creado AS creado_registro
            FROM clientes cli
            JOIN personas p ON cli.idpersona = p.idpersona
            WHERE p.creado BETWEEN p_fecha_desde AND p_fecha_hasta

            UNION ALL

            /* Empresas */
            SELECT 
                cli.idcliente,
                cli.idcontactabilidad,
                e.creado AS creado_registro
            FROM clientes cli
            JOIN empresas e ON cli.idempresa = e.idempresa
            WHERE e.creado BETWEEN p_fecha_desde AND p_fecha_hasta
        ) AS X
        JOIN contactabilidad ctb ON x.idcontactabilidad = ctb.idcontactabilidad
        GROUP BY
            WEEKDAY(x.creado_registro),
            ctb.contactabilidad
        ORDER BY
            WEEKDAY(x.creado_registro),
            ctb.contactabilidad;

    ELSE
        -- Si llega un valor distinto para p_periodo, no devolvemos nada (o podrías lanzar un SIGNAL).
        SELECT 
            'ERROR: El parámetro p_periodo debe ser ANUAL, MENSUAL o SEMANAL' 
            AS mensaje;
    END IF;
END $$





INSERT INTO personas (
    nombres, apellidos, tipodoc, numdoc, numruc, direccion, correo,
    telprincipal, telalternativo, creado
) VALUES
  -- Enero 2025 (12 personas)
  ('Ana',       'López',    'DNI', '10001', NULL, NULL, NULL, '900000001', NULL, '2025-01-03 09:15:00'),
  ('Bruno',     'Martínez', 'DNI', '10002', NULL, NULL, NULL, '900000002', NULL, '2025-01-07 11:30:00'),
  ('Carla',     'Pérez',    'DNI', '10003', NULL, NULL, NULL, '900000003', NULL, '2025-01-10 14:45:00'),
  ('Daniel',    'Ramírez',  'DNI', '10004', NULL, NULL, NULL, '900000004', NULL, '2025-01-12 08:20:00'),
  ('Elena',     'Gómez',    'DNI', '10005', NULL, NULL, NULL, '900000005', NULL, '2025-01-15 16:00:00'),
  ('Fernando',  'Torres',   'DNI', '10006', NULL, NULL, NULL, '900000006', NULL, '2025-01-18 10:10:00'),
  ('Gabriela',  'Vargas',   'DNI', '10007', NULL, NULL, NULL, '900000007', NULL, '2025-01-20 13:25:00'),
  ('Héctor',    'Rojas',    'DNI', '10008', NULL, NULL, NULL, '900000008', NULL, '2025-01-22 15:40:00'),
  ('Isabel',    'Bravo',    'DNI', '10009', NULL, NULL, NULL, '900000009', NULL, '2025-01-25 09:55:00'),
  ('Javier',    'Cruz',     'DNI', '10010', NULL, NULL, NULL, '900000010', NULL, '2025-01-27 17:05:00'),
  ('Karina',    'Ruiz',     'DNI', '10011', NULL, NULL, NULL, '900000011', NULL, '2025-01-29 12:00:00'),
  ('Luis',      'Flores',   'DNI', '10012', NULL, NULL, NULL, '900000012', NULL, '2025-01-30 08:00:00'),

  -- Febrero 2025 (12 personas)
  ('María',     'Castillo', 'DNI', '10013', NULL, NULL, NULL, '900000013', NULL, '2025-02-02 10:20:00'),
  ('Nicolás',   'Díaz',     'DNI', '10014', NULL, NULL, NULL, '900000014', NULL, '2025-02-05 11:10:00'),
  ('Olga',      'Mendoza',  'DNI', '10015', NULL, NULL, NULL, '900000015', NULL, '2025-02-07 14:00:00'),
  ('Pedro',     'Reyes',    'DNI', '10016', NULL, NULL, NULL, '900000016', NULL, '2025-02-10 16:30:00'),
  ('Quintana',  'Salas',    'DNI', '10017', NULL, NULL, NULL, '900000017', NULL, '2025-02-12 09:45:00'),
  ('Rosa',      'Arias',    'DNI', '10018', NULL, NULL, NULL, '900000018', NULL, '2025-02-15 13:15:00'),
  ('Sergio',    'Pinto',    'DNI', '10019', NULL, NULL, NULL, '900000019', NULL, '2025-02-18 15:55:00'),
  ('Teresa',    'Cárdenas', 'DNI', '10020', NULL, NULL, NULL, '900000020', NULL, '2025-02-20 08:25:00'),
  ('Ulises',    'Herrera',  'DNI', '10021', NULL, NULL, NULL, '900000021', NULL, '2025-02-22 12:40:00'),
  ('Verónica',  'Núñez',    'DNI', '10022', NULL, NULL, NULL, '900000022', NULL, '2025-02-24 17:10:00'),
  ('Walter',    'Campos',   'DNI', '10023', NULL, NULL, NULL, '900000023', NULL, '2025-02-26 09:00:00'),
  ('Ximena',    'Ortiz',    'DNI', '10024', NULL, NULL, NULL, '900000024', NULL, '2025-02-28 11:50:00'),

  -- Marzo 2025 (12 personas)
  ('Yolanda',   'Peña',     'DNI', '10025', NULL, NULL, NULL, '900000025', NULL, '2025-03-01 10:00:00'),
  ('Zacarías',  'Flores',   'DNI', '10026', NULL, NULL, NULL, '900000026', NULL, '2025-03-04 13:20:00'),
  ('Alberto',   'Iglesias', 'DNI', '10027', NULL, NULL, NULL, '900000027', NULL, '2025-03-06 15:30:00'),
  ('Beatriz',   'Saavedra', 'DNI', '10028', NULL, NULL, NULL, '900000028', NULL, '2025-03-08 09:10:00'),
  ('Cristian',  'Montoya',  'DNI', '10029', NULL, NULL, NULL, '900000029', NULL, '2025-03-11 11:45:00'),
  ('Diana',     'Vera',     'DNI', '10030', NULL, NULL, NULL, '900000030', NULL, '2025-03-13 14:55:00'),
  ('Esteban',   'Cordero',  'DNI', '10031', NULL, NULL, NULL, '900000031', NULL, '2025-03-15 16:10:00'),
  ('Fabiana',   'Salcedo',  'DNI', '10032', NULL, NULL, NULL, '900000032', NULL, '2025-03-18 10:05:00'),
  ('Gustavo',   'Molina',   'DNI', '10033', NULL, NULL, NULL, '900000033', NULL, '2025-03-20 12:35:00'),
  ('Helena',    'Suárez',   'DNI', '10034', NULL, NULL, NULL, '900000034', NULL, '2025-03-22 17:25:00'),
  ('Iván',      'Bravo',    'DNI', '10035', NULL, NULL, NULL, '900000035', NULL, '2025-03-25 09:50:00'),
  ('Julia',     'Ramos',    'DNI', '10036', NULL, NULL, NULL, '900000036', NULL, '2025-03-28 11:15:00'),

  -- Abril 2025 (14 personas, para completar 50 en total)
  ('Kevin',     'Reynoso',  'DNI', '10037', NULL, NULL, NULL, '900000037', NULL, '2025-04-01 10:30:00'),
  ('Laura',     'Guzmán',   'DNI', '10038', NULL, NULL, NULL, '900000038', NULL, '2025-04-03 14:40:00'),
  ('Miguel',    'Lozano',   'DNI', '10039', NULL, NULL, NULL, '900000039', NULL, '2025-04-05 16:55:00'),
  ('Natalia',   'Rocha',    'DNI', '10040', NULL, NULL, NULL, '900000040', NULL, '2025-04-07 09:05:00'),
  ('Óscar',     'Valencia', 'DNI', '10041', NULL, NULL, NULL, '900000041', NULL, '2025-04-10 11:20:00'),
  ('Paola',     'Benítez',  'DNI', '10042', NULL, NULL, NULL, '900000042', NULL, '2025-04-12 13:35:00'),
  ('Quique',    'Fuentes',  'DNI', '10043', NULL, NULL, NULL, '900000043', NULL, '2025-04-14 15:50:00'),
  ('Raquel',    'Sandoval', 'DNI', '10044', NULL, NULL, NULL, '900000044', NULL, '2025-04-16 08:15:00'),
  ('Sofía',     'Miranda',  'DNI', '10045', NULL, NULL, NULL, '900000045', NULL, '2025-04-18 10:25:00'),
  ('Tomás',     'Lara',     'DNI', '10046', NULL, NULL, NULL, '900000046', NULL, '2025-04-20 12:45:00'),
  ('Úrsula',    'Noriega',  'DNI', '10047', NULL, NULL, NULL, '900000047', NULL, '2025-04-22 14:10:00'),
  ('Víctor',    'Gómez',    'DNI', '10048', NULL, NULL, NULL, '900000048', NULL, '2025-04-24 16:00:00'),
  ('Wendy',     'Álvarez',  'DNI', '10049', NULL, NULL, NULL, '900000049', NULL, '2025-04-26 09:40:00'),
  ('Xavier',    'Padilla',  'DNI', '10050', NULL, NULL, NULL, '900000050', NULL, '2025-04-28 11:55:00');

-- 3) Ahora insertamos 50 clientes, uno por cada persona anterior.
--    Para asignar idcontactabilidad usamos un ciclo “1,2,3,4,5,6,1,2,…”
--    Eso asegura que haya variedad. Como la tabla CLIENTES exige que
--    (idpersona IS NOT NULL AND idempresa IS NULL), pasamos idempresa = NULL.



INSERT INTO clientes (idempresa, idpersona, idcontactabilidad)
VALUES
  -- Los 12 de Enero → personas ID 1..12
  (NULL,  1,  1),  -- Ana López → Facebook
  (NULL,  2,  2),  -- Bruno Martínez → Instagram
  (NULL,  3,  3),  -- Carla Pérez → tiktok
  (NULL,  4,  4),  -- Daniel Ramírez → Folletos
  (NULL,  5,  5),  -- Elena Gómez → Campaña publicitaria
  (NULL,  6,  6),  -- Fernando Torres → Recomendacion
  (NULL,  7,  1),  -- Gabriela Vargas → Facebook
  (NULL,  8,  2),  -- Héctor Rojas → Instagram
  (NULL,  9,  3),  -- Isabel Bravo → tiktok
  (NULL, 10,  4),  -- Javier Cruz → Folletos
  (NULL, 11,  5),  -- Karina Ruiz → Campaña publicitaria
  (NULL, 12,  6),  -- Luis Flores → Recomendacion

  -- Los 12 de Febrero → personas ID 13..24
  (NULL, 13,  2),  -- María Castillo → Instagram
  (NULL, 14,  3),  -- Nicolás Díaz → tiktok
  (NULL, 15,  4),  -- Olga Mendoza → Folletos
  (NULL, 16,  5),  -- Pedro Reyes → Campaña publicitaria
  (NULL, 17,  6),  -- Quintana Salas → Recomendacion
  (NULL, 18,  1),  -- Rosa Arias → Facebook
  (NULL, 19,  2),  -- Sergio Pinto → Instagram
  (NULL, 20,  3),  -- Teresa Cárdenas → tiktok
  (NULL, 21,  4),  -- Ulises Herrera → Folletos
  (NULL, 22,  5),  -- Verónica Núñez → Campaña publicitaria
  (NULL, 23,  6),  -- Walter Campos → Recomendacion
  (NULL, 24,  1),  -- Ximena Ortiz → Facebook

  -- Los 12 de Marzo → personas ID 25..36
  (NULL, 25,  3),  -- Yolanda Peña → tiktok
  (NULL, 26,  4),  -- Zacarías Flores → Folletos
  (NULL, 27,  5),  -- Alberto Iglesias → Campaña publicitaria
  (NULL, 28,  6),  -- Beatriz Saavedra → Recomendacion
  (NULL, 29,  1),  -- Cristian Montoya → Facebook
  (NULL, 30,  2),  -- Diana Vera → Instagram
  (NULL, 31,  3),  -- Esteban Cordero → tiktok
  (NULL, 32,  4),  -- Fabiana Salcedo → Folletos
  (NULL, 33,  5),  -- Gustavo Molina → Campaña publicitaria
  (NULL, 34,  6),  -- Helena Suárez → Recomendacion
  (NULL, 35,  1),  -- Iván Bravo → Facebook
  (NULL, 36,  2),  -- Julia Ramos → Instagram

  -- Los 14 de Abril → personas ID 37..50
  (NULL, 37,  3),  -- Kevin Reynoso → tiktok
  (NULL, 38,  4),  -- Laura Guzmán → Folletos
  (NULL, 39,  5),  -- Miguel Lozano → Campaña publicitaria
  (NULL, 40,  6),  -- Natalia Rocha → Recomendacion
  (NULL, 41,  1),  -- Óscar Valencia → Facebook
  (NULL, 42,  2),  -- Paola Benítez → Instagram
  (NULL, 43,  3),  -- Quique Fuentes → tiktok
  (NULL, 44,  4),  -- Raquel Sandoval → Folletos
  (NULL, 45,  5),  -- Sofía Miranda → Campaña publicitaria
  (NULL, 46,  6),  -- Tomás Lara → Recomendacion
  (NULL, 47,  1),  -- Úrsula Noriega → Facebook
  (NULL, 48,  2),  -- Víctor Gómez → Instagram
  (NULL, 49,  3),  -- Wendy Álvarez → tiktok
  (NULL, 50,  4);  -- Xavier Padilla → Folletos

-- Con esto ya tienes 50 clientes (todos personas) con fechas de creación repartidas
-- entre enero, febrero, marzo y abril de 2025, y con distintos idcontactabilidad (1..6).
-- Ahora puedes probar tu vista o el SP que construiste antes. Por ejemplo:

--  Ejemplo: Agrupación ANUAL (mes-año) para todo el rango Ene–Abr 2025
-- CALL sp_grafico_contactabilidad_periodo('ANUAL', '2025-01-01', '2025-04-30');

--  Ejemplo: Agrupación MENSUAL (semanas dentro de cada mes) Febrero 2025
-- CALL sp_grafico_contactabilidad_periodo('MENSUAL', '2025-02-01', '2025-02-28');

--  Ejemplo: Agrupación SEMANAL (días de la semana) Marzo 2025
-- CALL sp_grafico_contactabilidad_periodo('SEMANAL', '2025-03-01', '2025-03-31');

